<?php
/**
* The Memcached Memcache Class
* @package Mars
*/

namespace Mars\Memcache;

/**
* The Memcached Memcache Class
* Memcache driver which uses Memcached
*/
class Memcached implements DriverInterface
{
	/**
	* @var object $handle The driver's handle
	*/
	protected object $handle;

	/**
	* @see \Mars\Memcache\DriverInterface::connect()
	* {@inheritdoc}
	*/
	public function connect(string $host, string $port)
	{
		if (!extension_loaded('memcached')) {
			throw new \Exception("The memcached extension isn't available on this server. Either install it or disable it's use by changing 'memcache_enable' to false in config.php");
		}

		$this->handle = new \Memcached;

		if (!$this->handle->addServer($host, $port)) {
			throw new \Exception('Error connecting to the memcached server');
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::disconnect()
	* {@inheritdoc}
	*/
	public function disconnect()
	{
		if (isset($this->handle)) {
			unset($this->handle);
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::add()
	* {@inheritdoc}
	*/
	public function add(string $key, $value, int $expires = 0)
	{
		return $this->handle->set($key, serialize($value), $expires);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::set()
	* {@inheritdoc}
	*/
	public function set(string $key, $value, int $expires = 0)
	{
		return $this->handle->set($key, serialize($value), $expires);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $key)
	{
		$value = $this->handle->get($key);

		return unserialize($value);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::exists()
	* {@inheritdoc}
	*/
	public function exists(string $key) : bool
	{
		$ret = $this->handle->append($key, null);
		if ($ret === false) {
			return false;
		}

		return true;
	}

	/**
	* @see \Mars\Memcache\DriverInterface::delete()
	* {@inheritdoc}
	*/
	public function delete(string $key)
	{
		return $this->handle->delete($key);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::deleteAll()
	* {@inheritdoc}
	*/
	public function deleteAll()
	{
		return $this->handle->flush();
	}
}
