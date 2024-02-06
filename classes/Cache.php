<?php
/**
* The Cache Class
* @package Mars
*/

namespace Mars;

use Mars\Handlers;
use Mars\Cache\Pages;
use Mars\Cache\Templates;
use Mars\System\Theme;

/**
 * The Cache Class
 * Stores the system's cached values & contains the functionality for interacting with the system's cached data
 * Not to be confused with Cachable or Caching
 */
class Cache extends Data
{
	/**
	 * @var Handlers $caches
	 */
	public readonly Handlers $caches;

	/**
	 * @var Page $page The Page Cache object
	 */
	public readonly Pages $pages;

	/**
	 * @var Templates $templates The Templates Cache object
	 */
	public readonly Templates $templates;
	
	/**
	 * @var array $supported_caches The list of supported caches
	 */
	protected array $supported_caches = [
		'pages' => '\Mars\Cache\Pages',
		'templates' => '\Mars\Cache\Templates'
	];

	/**
	 * @var array $caches_list The list of cache objects
	 */
	protected array $caches_list = [];

	/**
	 * Builds the Cache object
	 */
	public function __construct(App $app)
	{
		$this->app = $app ?? App::get();

		$this->caches = new Handlers($this->supported_caches, $this->app);
		
		$this->caches_list = &$this->caches->getAll();
		foreach ($this->caches_list as $cache_name => $cache) {
			$this->$cache_name = $cache;
		}
	}

	public function store(string $content)
	{
		$this->pages->store($content);
	}
}
