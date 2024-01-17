<?php
/**
* The Memcache Class
* @package Mars
*/

namespace Mars;

use Mars\Memcache\DriverInterface;

/**
 * The Memcache Class
 * Handles the interactions with the memory cache.
 * Not the same as the memcache extension, although it might use it as a driver
 */
class Memcache
{
	use AppTrait;

	/**
	 * @var Drivers $drivers The drivers object
	 */
	public readonly Drivers $drivers;

	/**
	 * @var DriverInterface $driver The driver object
	 */
	protected DriverInterface $driver;

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
	 * @var array $supported_drivers The supported drivers
	 */
	protected array $supported_drivers = [
		'redis' => '\Mars\Memcache\Redis',
		'memcache' => '\Mars\Memcache\Memcache',
		'memcached' => '\Mars\Memcache\Memcached'
	];

	/**
	 * Constructs the memcache object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->memcache_enable) {
			return;
		}

		$this->host = $this->app->config->memcache_host;
		$this->port = $this->app->config->memcache_port;
		$this->key = $this->app->config->key;
		$this->enabled = true;
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'memcache', $this->app);
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

		$this->driver = $this->drivers->get($this->app->config->memcache_driver);

		$this->driver->connect($this->host, $this->port);

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

		$this->driver->disconnect();
	}

	/**
	 * Adds a key to the memcache only if it doesn't already exists
	 * @param string $key The key
	 * @param string $value The value
	 * @param bool $serialize If true, will serialize the value
	 * @param int $expires The number of seconds after which the data will expire
	 * @return bool
	 */
	public function add(string $key, $value, bool $serialize = false, int $expires = 0)
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->connected) {
			$this->connect();
		}

		if ($serialize) {
			$value = $this->app->serializer->serialize($value, false);
		}

		return $this->driver->add($key . '-' . $this->key, $value, $expires);
	}

	/**
	 * Adds a key to the memcache. If a key with the same name exists, it's value is overwritten
	 * @param string $key The key
	 * @param string $value The value
	 * @param bool $serialize If true, will serialize the value
	 * @param int $expires The number of seconds after which the data will expire
	 * @return bool
	 */
	public function set(string $key, $value, bool $serialize = false, int $expires = 0) : bool
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->connected) {
			$this->connect();
		}

		if ($serialize) {
			$value = $this->app->serializer->serialize($value, false);
		}

		return $this->driver->set($key . '-' . $this->key, $value, $expires);
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

		$value = $this->driver->get($key . '-' . $this->key);

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

		return $this->driver->exists($key . '-' . $this->key);
	}

	/**
	 * Delets $key from the memcache
	 * @param string $key The key
	 * @return mixed The value for $key
	 * @return bool
	 */
	public function delete(string $key) : bool
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->connected) {
			$this->connect();
		}

		return $this->driver->delete($key . '-' . $this->key);
	}

	/**
	 * Deletes all keys from the memcache server
	 * @return static
	 */
	public function deleteAll() : static
	{
		if (!$this->enabled) {
			return $this;
		}
		if (!$this->connected) {
			$this->connect();
		}

		$this->driver->deleteAll();

		return $this;
	}
}
