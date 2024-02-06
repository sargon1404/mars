<?php
/**
* The Page Cache Class
* @package Mars
*/

namespace Mars\Cache;

use Mars\App;

/**
 * The Page Cache Class
 * Class which handles the caching of pages
 */
class Pages extends Cacheable
{
	/**
	 * @var string $id The id of the page
	 */
	public string $id = '';
	
	/**
	 * @var bool $can_cache True if the content can be cached
	 */
	public bool $can_cache = false;

	/**
	 * @var bool $minify True, if the output can be minified
	 */
	protected bool $minify = true;

	/**
	 * Builds the page cache object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->is_bin || !$this->app->config->cache_page_enable || defined('DISABLE_CACHE_PAGE')) {
			return;
		}
		if ($this->app->config->debug || $this->app->config->development) {
			return;
		}
		if ($this->app->method != 'get') {
			return;
		}

		$this->driver_name = $this->app->config->cache_page_driver;
		$this->path = $this->app->cache_path . '/pages';
		$this->expires_hours = $this->app->config->cache_page_expire_hours;
		$this->minify = $this->app->config->cache_page_minify;
		$this->can_cache = true;
		$this->id = $this->app->page_url;

		parent::__construct($app);

		$this->output();
	}

	/**
	 * Returns the file where the content will be cached
	 * @return string
	 */
	protected function getFile() : string
	{
		return $this->getFileById($this->id);
	}

	/**
	 * Returns the file where the content will be cached, by id
	 * @return string
	 */
	protected function getFileById(string $id) : string
	{
		return md5($id) . '.' . $this->extension;
	}

	/**
	 * Stores the content in the cache
	 */
	public function store(string $content)
	{
		if (!$this->can_cache) {
			return;
		}
		if ($this->minify) {
			$content = $this->minify($content);
		}

		parent::storeContent($content);
	}

	/**
	 * Clears the cached page
	 * @param string $id The id of the page to clear
	 */
	public function clear(string $id)
	{
		$filename = $this->path . '/' . $this->getFileById($id);
		if (is_file($filename)) {
			unlink($filename);
		}
	}

	/**
	 * Clears all the cached pages
	 */
	public function clearAll()
	{
		$dir = $this->app->cache_path . '/pages';

		$this->app->dir->clean($dir);

		$this->app->file->copy($this->app->path . '/src/index.htm', $dir . '/index.htm');
	}

	/**
	 * Html minifies the content
	 * @param string $content The code to minify
	 * @return string The minified code
	 */
	public function minify(string $content) : string
	{
		$minifier = new \Mars\Minifiers\Html;

		return $minifier->minify($content);
	}
}
