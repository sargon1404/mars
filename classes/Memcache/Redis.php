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
	* {@inheritdoc}
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
	* {@inheritdoc}
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
	* {@inheritdoc}
	*/
	public function add(string $key, $value, int $expires = 0) : bool
	{
		$ret = $this->handle->set($key, serialize($value));

		if ($expires) {
			$this->handle->expireAt($key, time() + $expires);
		}

		return $ret;
	}

	/**
	* @see \Mars\Memcache\DriverInterface::set()
	* {@inheritdoc}
	*/
	public function set(string $key, $value, int $expires = 0) : bool
	{
		return $this->add($key, $value, $expires);
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
		if (!$this->handle->exists($key)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	* @see \Mars\Memcache\DriverInterface::delete()
	* {@inheritdoc}
	*/
	public function delete(string $key) : bool
	{
		return $this->handle->del($key);
	}

	/**
	* @see \Mars\Memcache\DriverInterface::deleteAll()
	* {@inheritdoc}
	*/
	public function deleteAll() : bool
	{
		return $this->handle->flushAll();
	}
}
