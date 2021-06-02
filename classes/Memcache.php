<?php
/**
* The Memcache Class
* @package Mars
*/

namespace Mars;

/**
* The Memcache Class
* Handles the interactions with the memory cache.
* Not the same as the memcache extension, although it might use it as a driver
*/
class Memcache
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $host The host to connect to
	*/
	protected string $host = '';

	/**
	* @var string $port The port to connect to
	*/
	protected string $port = '';

	/**
	* @var string $key Secret key used to identify the site
	*/
	protected string $key = '';

	/**
	* @var bool $enabled Will be set to true, if memcache is enabled
	*/
	protected bool $enabled = false;

	/**
	* @var bool $connected Set to true, if the connection to the memcache server has been made
	*/
	protected bool $connected = false;

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'memcache';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Memcache\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'redis' => '\Mars\Memcache\Redis',
		'memcache' => '\Mars\Memcache\Memcache',
		'memcached' => '\Mars\Memcache\Memcached'
	];

	/**
	* Contructs the memcache object
	* @param App $app The app object
	* @param string $driver The driver to use. redis and memcache are supported
	* @param string $host The host to connect to
	* @param string $port The port to connect to
	* @param string $key Secret key used to identify the site
	*/
	public function __construct(App $app, string $driver = '', string $host = '', string $port = '', string $key = '')
	{
		$this->app = $app;

		if (!$this->app->config->memcache_enable) {
			return;
		}

		if (!$driver) {
			$driver = $this->app->config->memcache_driver;
			$host = $this->app->config->memcache_memcache_host;
			$port = $this->app->config->memcache_memcache_port;
			$key = $this->app->config->key;
		}

		$this->driver = $driver;
		$this->host = $host;
		$this->port = $port;
		$this->key = $key;
		$this->enabled = true;
	}

	/**
	* Destroys the memcache object. Disconnects from the memcache server
	*/
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	* Returns true if memcache is enabled
	*/
	public function isEnabled() : bool
	{
		return $this->enabled;
	}

	/**
	* Connects to the memcache server
	*/
	protected function connect()
	{
		if (!$this->enabled || $this->connected) {
			return;
		}

		$this->handle = $this->getHandle();

		$this->handle->connect($this->host, $this->port);

		$this->connected = true;
	}

	/**
	* Disconnects from the memcache server
	*/
	protected function disconnect()
	{
		if (!$this->connected) {
			return;
		}

		$this->handle->disconnect();

		$this->handle = null;
	}

	/**
	* Adds a key to the memcache only if it doesn't already exists
	* @param string $key The key
	* @param string $value The value
	* @param bool $serialize If true, will serialize the value
	* @param int $expires The number of seconds after which the data will expire
	* @return $this
	*/
	public function add(string $key, $value, bool $serialize = false, int $expires = 0)
	{
		if (!$this->enabled) {
			return $this;
		}
		if (!$this->connected) {
			$this->connect();
		}

		if ($serialize) {
			$value = $this->app->serializer->serialize($value, false);
		}

		$this->handle->add($key . '-' . $this->key, $value, $expires);

		return $this;
	}

	/**
	* Adds a key to the memcache. If a key with the same name exists, it's value is overwritten
	* @param string $key The key
	* @param string $value The value
	* @param bool $serialize If true, will serialize the value
	* @param int $expires The number of seconds after which the data will expire
	* @return $this
	*/
	public function set(string $key, $value, bool $serialize = false, int $expires = 0)
	{
		if (!$this->enabled) {
			return $this;
		}
		if (!$this->connected) {
			$this->connect();
		}

		if ($serialize) {
			$value = $this->app->serializer->serialize($value, false);
		}

		$this->handle->set($key . '-' . $this->key, $value, $expires);

		return $this;
	}

	/**
	* Retrieves the value of $key from the memcache
	* @param string $key The key
	* @param bool $unserialize If true, will unserialize the returned result
	* @return mixed The value of $key
	*/
	public function get(string $key, bool $unserialize = false)
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->connected) {
			$this->connect();
		}

		$value = $this->handle->get($key . '-' . $this->key);

		if ($unserialize) {
			return $this->app->serializer->unserialize(data: $value, decode: false);
		}

		return $value;
	}

	/**
	* Checks if a key exists/is set
	* @param string $key The key
	* @return bool True if the key exists
	*/
	public function exists(string $key) : bool
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->connected) {
			$this->connect();
		}

		return $this->handle->exists($key . '-' . $this->key);
	}

	/**
	* Delets $key from the memcache
	* @param string $key The key
	* @return mixed The value for $key
	* @return $this
	*/
	public function delete(string $key)
	{
		if (!$this->enabled) {
			return $this;
		}
		if (!$this->connected) {
			$this->connect();
		}

		$this->handle->delete($key . '-' . $this->key);

		return $this;
	}

	/**
	* Deletes all keys from the memcache server
	*/
	public function deleteAll()
	{
		if (!$this->enabled) {
			return $this;
		}
		if (!$this->connected) {
			$this->connect();
		}

		$this->handle->deleteAll();

		return $this;
	}
}
