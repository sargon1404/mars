<?php
/**
* The Cacheable Class
* @package Mars
*/

namespace Mars;

/**
* The Cacheable Class
* Caches content & serves it from cache
*/
abstract class Cacheable
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $path The folder where the content will be cached
	*/
	protected string $path = '';

	/**
	* @var string $file The name of the file used to cache the content
	*/
	protected string $file = '';

	/**
	* @var string $filename The filename of the file used to cache the content
	*/
	protected string $filename = '';

	/**
	* @var int $expires_hours The interval - in hours - after which the content should be refreshed by the browser
	*/
	protected int $expires_hours = 24;

	/**
	* @var string $extension The extension of the cache file
	*/
	protected string $extension = 'htm';

	/**
	* @var string $gzip If true, the content is considered to be gzipped
	*/
	protected bool $gzip = false;

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = 'file';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'cachable';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Cacheable\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'file' => '\Mars\Cacheable\File',
		'memcache' => '\Mars\Cacheable\Memcache'
	];

	/**
	* Returns the file used to cache the content
	*/
	abstract protected function getFile() : string;

	/**
	* Contructor for Cachable
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->file = $this->getFile();
		$this->filename = $this->path . $this->file;

		$this->handle = $this->getHandle();
	}

	/**
	* Initializes the driver & handle
	*/
	protected function init()
	{
		$this->handle = $this->getHandle();
	}

	/**
	* Returns the name of the driver to use
	* @return string The driver name
	*/
	protected function getDriver() : string
	{
		if ($this->driver == 'memcache') {
			if (!$this->app->config->memcache_enable) {
				throw new \Exception("Memcache must be enabled in order to use the memcache driver when caching content. Either set content_cache_driver to 'file' else or set memcache_enable to true");
			}
		}

		return $this->driver;
	}

	/**
	* Outputs the content, if it's cached
	*/
	public function output()
	{
		if ($this->app->is_bin) {
			return;
		}

		$last_modified = $this->getLastModified();

		if ($last_modified) {
			//we have the content in the cache
			$etag = $this->getEtag($last_modified);

			//check if we can send the 304 Not Modified header
			$this->outputNotModified($last_modified, $etag);

			//output the cache headers
			$this->outputHeaders($last_modified, $etag);

			$this->outputContent();
		}
	}

	/**
	* Sends the 304 Not Modified headers, if the etag matches
	* @param int $last_modified The date when the cached file has been last modified
	* @param string $etag The etag
	*/
	protected function outputNotModified(int $last_modified, string $etag)
	{
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if ($_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
				header('HTTP/1.1 304 Not Modified');
				die;
			}
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$cache_modified = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

			if ($last_modified <= $cache_modified) {
				header('HTTP/1.1 304 Not Modified');
				die;
			}
		}
	}

	/**
	* Outputs the headers needed when outputing from the cache
	* @param int $last_modified The date when the cached file has been last modified
	* @param string $etag The etag
	*/
	protected function outputHeaders(int $last_modified, string $etag)
	{
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
		header('Etag: ' . $etag);
		header('Vary: Accept-Encoding');

		if ($this->expires_hours) {
			$seconds = $this->expires_hours * 3600;
			$expires = gmdate('D, d M Y H:i:s', time() + $seconds);

			header('Expires: ' . $expires . ' GMT');
			header('Cache-Control: max-age = ' . $seconds);
		} else {
			header('Cache-Control: public');
		}
	}

	/**
	* Outputs the cached content
	*/
	public function outputContent()
	{
		$this->outputContentType();

		$content = $this->handle->get($this->filename);

		if ($this->gzip) {
			header('Content-encoding: gzip');
		}

		header('Content-Length: ' . strlen($content));

		echo $content;
		die;
	}

	/**
	* Outputs the content type. Must be implemented by the classes extending Cachable
	* @return static
	*/
	public function outputContentType() : static
	{
		return $this;
	}

	/**
	* Stores the content in the cache
	* @param string $content The content to store
	* return $this;
	*/
	public function store(string $content)
	{
		$this->handle->store($this->filename, $content);

		return $this;
	}

	/**
	* Deletes the cache file
	* @return static
	*/
	public function delete() : static
	{
		$this->handle->delete($this->filename);

		return $this;
	}

	/**
	* Deletes a file from the cache
	* @param string $file The file name to delete
	* @return static
	*/
	public function deleteFile(string $file) : static
	{
		$filename = $this->path . basename($file);

		$this->handle->delete($filename);

		return $this;
	}

	/**
	* Returns the date when the cached file has been last modified
	* @return int
	*/
	protected function getLastModified() : int
	{
		return $this->handle->getLastModified($this->filename);
	}

	/**
	* Returns the etag of the cached file
	* @param int $last_modified The date when the cached file has been last modified
	* @return string The etag
	*/
	protected function getEtag(int $last_modified) : string
	{
		return md5($this->file . $last_modified);
	}
}
