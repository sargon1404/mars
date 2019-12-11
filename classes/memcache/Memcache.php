<?php
/**
* The Memcache Memcache Class
* @see \Mars\Memcache\DriverInterface
* @package Mars
*/

namespace Mars\Memcache;

/**
* The Memcache Memcache Class
* Memcache driver which uses memcache
*/
class Memcache implements DriverInterface
{
	/**
	* @var object $handle The driver's handle
	*/
	protected object $handle;

	/**
	* @see \Mars\Memcache\DriverInterface::connect()
	* {@inheritDoc}
	*/
	public function connect(string $host, string $port)
	{
		if (!extension_loaded('memcache')) {
			throw new \Exception("The memcache extension isn't available on this server. Either install it or disable it's use by changing 'memcache_enable' to false in config.php");
		}

		$this->handle = new \Memcache;

		if (!$this->handle->connect($host, $port)) {
			throw new \Exception('Error connecting to the memcached server');
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::disconnect()
	* {@inheritDoc}
	*/
	public function disconnect()
	{
		if (isset($this->handle)) {
			unset($this->handle);
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::add()
	* {@inheritDoc}
	*/
	public function add(string $key, $value, int $expires = 0)
	{
		return $this->handle->add($key, $value, false, $expires);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::set()
	* {@inheritDoc}
	*/
	public function set(string $key, $value, int $expires = 0)
	{
		return $this->handle->set($key, $value, false, $expires);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::get()
	* {@inheritDoc}
	*/
	public function get(string $key)
	{
		return $this->handle->get($key);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::exists()
	* {@inheritDoc}
	*/
	public function exists(string $key) : bool
	{
		$data = $this->handle->get($key);
		if ($data === false) {
			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Memcache\DriverInterface::delete()
	* {@inheritDoc}
	*/
	public function delete(string $key)
	{
		return $this->handle->delete($key);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::delete()
	* {@inheritDoc}
	*/
	public function deleteAll()
	{
		return $this->handle->flush();
	}
}
