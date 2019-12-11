<?php
/**
* The Redis Memcache Class
* @package Mars
*/

namespace Mars\Memcache;

/**
* The Redis Memcache Class
* Memcache driver which uses Redis
*/
class Redis implements DriverInterface
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
		if (!class_exists('\\Redis')) {
			throw new \Exception("The redis extension isn't available on this server. Either install it or disable it's use by changing 'memcache_enable' to false in config.php");
		}

		$this->handle = new \Redis;

		if (!$this->handle->connect($host, $port)) {
			throw new \Exception('Error connecting to the redis server');
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::disconnect()
	* {@inheritDoc}
	*/
	public function disconnect()
	{
		if (isset($this->handle)) {
			$this->handle->close();

			unset($this->handle);
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::add()
	* {@inheritDoc}
	*/
	public function add(string $key, $value)
	{
		return $this->handle->set($key, serialize($value));
	}

	/**
	* @see \Mars\Memcache\DriverInterface::set()
	* {@inheritDoc}
	*/
	public function set(string $key, $value)
	{
		return $this->handle->set($key, serialize($value));
	}

	/**
	* @see \Mars\Memcache\DriverInterface::get()
	* {@inheritDoc}
	*/
	public function get(string $key)
	{
		$value = $this->handle->get($key);

		return unserialize($value);
	}
	
	/**
	* @see \Mars\Memcache\DriverInterface::exists()
	* {@inheritDoc}
	*/
	public function exists(string $key) : bool
	{
		if (!$this->handle->exists($key)) {
			return false;
		} else {
			return true;
		}
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
	* @see \Mars\Memcache\DriverInterface::deleteAll()
	* {@inheritDoc}
	*/
	public function deleteAll()
	{
		return $this->handle->flushAll();
	}
}
