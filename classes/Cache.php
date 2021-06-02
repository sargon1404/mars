<?php
/**
* The Cache Class
* @package Mars
*/

namespace Mars;

use Mars\System\Theme;

/**
* The Cache Class
* Stores the system's cached values & contains the functionality for interacting with the system's cached data
* Not to be confused with Cachable or Caching
*/
class Cache extends Data
{
	/**
	* @internal
	*/
	protected bool $serialize = true;

	/**
	* @internal
	*/
	protected bool $serialize_php_driver = false;

	/**
	* Clears the templates cache
	* @return $this
	*/
	public function clearTemplates()
	{
		$dir = $this->app->cache_dir . Theme::$DIRS['cache'] . '/';

		$this->clearDir($dir);

		return $this;
	}
}
