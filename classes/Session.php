<?php
/**
* The Session Class
* @package Mars
*/

namespace Mars;

/**
* The Session Class
* The system's session object
*/
class Session
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $cookie_name The session cookie's name
	*/
	protected string $cookie_name = '';

	/**
	* @var string $cookie_path The session cookie's path
	*/
	protected string $cookie_path = '';

	/**
	* @var string $cookie_domain The session cookie's domain
	*/
	protected string $cookie_domain = '';

	/**
	* @var string $cookie_domain The session's save path
	*/
	protected string $save_path = '';

	/**
	* @var string $prefix Prefix to apply to all session keys
	*/
	protected string $prefix = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'session';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Session\DriverInterface';

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
		$this->driver = $this->app->config->session_driver;
		$this->cookie_name = $this->app->config->session_cookie_name;
		$this->cookie_path = $this->app->config->session_cookie_path;
		$this->cookie_domain = $this->app->config->session_cookie_domain;
		$this->save_path = $this->app->config->session_save_path;
		$this->prefix = $this->app->config->session_prefix;
		if ($this->prefix) {
			$this->prefix = $this->prefix . '_';
		}

		$this->handle = $this->getHandle();
	}

	/**
	* Starts the session
	* @return $this
	*/
	public function start()
	{
		if ($this->app->is_cli) {
			return;
		}
		if (!$this->app->config->session_start) {
			return;
		}
		//don't start the session if the http accelerator is enabled
		if ($this->app->config->accelerator_enable) {
			//return;
		}

		if ($this->save_path) {
			session_save_path($this->save_path);
		}

		if ($this->cookie_path || $this->cookie_domain) {
			session_set_cookie_params(0, $this->cookie_path, $this->cookie_domain);
		}

		if ($this->cookie_name) {
			session_name($this->cookie_name);
		}

		session_start();

		return $this;
	}

	/**
	* Deletes the session cookie
	*/
	public function delete()
	{
		session_unset();
		session_destroy();
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
	* Returns the session prefix
	* @return string
	*/
	public function getPrefix() : string
	{
		return $this->prefix;
	}

	/**
	* Regenerates the session id
	* @return string The new session id
	*/
	public function regenerateId() : string
	{
		$old_id = $this->getId();

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
			return $this->app->serializer->unserialize(data: $value, decode: false);
		}

		return $value;
	}

	/**
	* Sets a session value
	* @param string $name The name of the var
	* @param mixed $value The value
	* @param bool $serialize If true, will serialize the value
	* @return $this
	*/
	public function set(string $name, $value, bool $serialize = false)
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
	* @return $this
	*/
	public function unset(string $name)
	{
		$key = $this->prefix . $name;

		unset($_SESSION[$key]);

		return $this;
	}
}
