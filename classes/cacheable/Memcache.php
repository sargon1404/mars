<?php
/**
* The Memcache Driver
* @package Mars
*/

namespace Mars\Cacheable;

use Mars\App;

/**
* The Memcache Driver
* Driver which stores in memcache the cached resources
*/
class Memcache implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->memcache->isEnabled()) {
			throw new \Exception('Memcache must be enabled to be able to use the memcache cachable driver');
		}
	}

	/**
	* @see \Mars\Cacheable\DriverInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $filename) : string
	{
		return $this->app->memcache->get($filename);
	}

	/**
	* @see \Mars\Cachable\DriverInterface::store()
	* {@inheritdoc}
	*/
	public function store(string $filename, string $content) : bool
	{
		$this->app->memcache->set($filename, $content);
		$this->app->memcache->set($filename . '-last-modified', time());

		return true;
	}

	/**
	* @see \Mars\Cachable\DriverInterface::getLastModified()
	* {@inheritdoc}
	*/
	public function getLastModified(string $filename) : int
	{
		return (int)$this->app->memcache->get($filename . '-last-modified');
	}

	/**
	* @see \Mars\Cachable\DriverInterface::delete()
	* {@inheritdoc}
	*/
	public function delete(string $filename) : bool
	{
		$this->app->memcache->delete($filename);
		$this->app->memcache->delete($filename . '-last-modified');

		return true;
	}
}
