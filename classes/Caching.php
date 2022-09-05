<?php
/**
* The Content Caching Class
* @package Mars
*/

namespace Mars;

use Mars\Helpers\Minifier;

/**
* The Content Caching Class
* Caches content & serves it from cache
*/
class Caching extends Cacheable
{
	/**
	* @var bool $can_cache True if the content can be cached
	*/
	public bool $can_cache = false;

	/**
	* @var bool $minify True, if the output can be minified
	*/
	protected bool $minify = true;

	/**
	* Builds the caching object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->is_bin || !$this->app->config->content_cache_enable || defined('CONTENT_CACHE_DISABLE')) {
			return;
		}
		if ($this->app->method == 'post') {
			return;
		}

		$this->driver_name = $this->app->config->content_cache_driver;
		$this->path = $this->app->cache_path . 'content/';
		$this->expires_hours = $this->app->config->content_cache_expire_hours;
		$this->minify = $this->app->config->content_cache_minify;
		$this->can_cache = true;

		if ($this->app->accepts_gzip && $this->app->config->content_cache_gzip) {
			$this->gzip = true;
		}

		parent::__construct($app);

		$this->output();
	}

	/**
	* Returns the file where the content will be cached
	* @return string
	*/
	protected function getFile() : string
	{
		$file = hash('sha256', $this->app->full_url) . '.' . $this->extension;
		if ($this->gzip) {
			$file.= '.gz';
		}

		return $file;
	}

	/**
	* @see \Mars\Cachable::store()
	* {@inheritdoc}
	*/
	public function store(string $content) : static
	{
		if ($this->minify) {
			$content = $this->minify($content);
		}
		if ($this->gzip) {
			$content = $this->app->gzip($content);
		}

		parent::store($content);

		return $this;
	}

	/**
	* Deletes an item from the cache
	* @param int $item_id The item's id
	* @param string $item_type The item's type
	* @return static
	*/
	public function deleteItem(int $item_id, string $item_type) : static
	{
		$file = $item_type . '_' . $item_id . '.' . $this->extension;

		$this->deleteFile($file);

		return $this;
	}

	/**
	* Html minifies the content
	* @param string $content The code to minify
	* @return string The minified code
	*/
	public function minify(string $content) : string
	{
		$minifier = new Minifier;

		return $minifier->minifyHtml($content);
	}
}
