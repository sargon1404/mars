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
		$this->clearDir($this->app->cache_path . Theme::$DIRS['cache']);

		return $this;
	}

	/**
	* Clears a folder and copies the empty index.htm file
	* @param string $dir The folder's name
	*/
	protected function clearDir($dir)
	{
		$this->app->dir->clean($dir);

		$this->app->file->copy($this->app->path . 'src/index.htm', $dir . 'index.htm');
	}
}
