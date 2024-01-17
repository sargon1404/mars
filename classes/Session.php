<?php
/**
* The Session Class
* @package Mars
*/

namespace Mars;

use Mars\Session\DriverInterface;

/**
 * The Session Class
 * The system's session object
 */
class Session
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
	 * @var string $prefix Prefix to apply to all session keys
	 */
	protected string $prefix = '';

	/**
	 * @var array $supported_drivers The supported drivers
	 */
	protected array $supported_drivers = [
		'php' => '\Mars\Session\Php',
		'db' => '\Mars\Session\Db',
		'memcache' => '\Mars\Session\Memcache'
	];

	/**
	 * Builds the session object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->is_bin || !$this->app->config->session_start) {
			return;
		}
		//don't start the session if the http accelerator is enabled
		if ($this->app->config->accelerator_enable) {
			return;
		}

		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'session', $this->app);
		$this->driver = $this->drivers->get($this->app->config->session_driver);
		$this->prefix = $this->getPrefix();

		session_start();
	}

	/**
	 * Returns the session prefix
	 * @return string
	 */
	protected function getPrefix() : string
	{
		$prefix = $this->app->config->session_prefix;

		if ($prefix) {
			$prefix = $prefix . '-';
		}

		return $prefix;
	}

	/**
	 * Deletes the session cookie
	 * @return static
	 */
	public function delete() : static
	{
		session_unset();
		session_destroy();

		return $this;
	}


	/**
	 * Returns the session id
	 * @return string The session id
	 */
	public function getId() : string
	{
		return session_id();
	}

	/**
	 * Regenerates the session id
	 * @return string The new session id
	 */
	public function regenerateId() : string
	{
		session_regenerate_id();

		return $this->getId();
	}

	/**
	 * Determines if $_SESSION[$name] is set
	 * @param string $name The name of the var
	 * @return bool Returns true if $_SESSION[$name] is set, false otherwise
	 */
	public function isSet(string $name) : bool
	{
		$key = $this->prefix . $name;

		return isset($_SESSION[$key]);
	}

	/**
	 * Returns $_SESSION[$name] if set
	 * @param string $name The name of the var
	 * @param bool $unserialize If true, will unserialize the returned result
	 * @param mixed $not_set The return value, if $_SESSION[$name] isn't set
	 * @return mixed
	 */
	public function get(string $name, bool $unserialize = false, $not_set = null)
	{
		$key = $this->prefix . $name;

		if (!isset($_SESSION[$key])) {
			return $not_set;
		}

		$value = $_SESSION[$key];

		if ($unserialize) {
			return $this->app->serializer->unserialize($value, [], false);
		}

		return $value;
	}

	/**
	 * Sets a session value
	 * @param string $name The name of the var
	 * @param mixed $value The value
	 * @param bool $serialize If true, will serialize the value
	 * @return static
	 */
	public function set(string $name, $value, bool $serialize = false) : static
	{
		$key = $this->prefix . $name;

		if ($serialize) {
			$value = $this->app->serializer->serialize($value, false);
		}

		$_SESSION[$key] = $value;

		return $this;
	}

	/**
	 * Unsets a session value
	 * @param string $name The name of the var
	 * @return static
	 */
	public function unset(string $name) : static
	{
		$key = $this->prefix . $name;

		unset($_SESSION[$key]);

		return $this;
	}
}
