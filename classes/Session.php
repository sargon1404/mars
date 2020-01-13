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
	* @var string $key Key in $_SESSION, where the data will be read/written from
	*/
	protected string $key = '';
	
	/**
	* @var string $driver_namespace The driver's namespace
	*/
	protected string $driver_namespace = '\\Mars\\Session';

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

		$this->handle = $this->getHandle();
	}

	/**
	* Starts the session
	* @return $this
	*/
	public function start()
	{
		if (!$this->app->config->session_start) {
			return;
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
	* @param string $key The session key
	* @return bool Returns true if $_SESSION[$name] is set, false otherwise
	*/
	public function isSet($name, $key = null) : bool
	{
		if ($key === null) {
			$key = $this->key;
		}

		if ($key) {
			return isset($_SESSION[$key][$name]);
		} else {
			return isset($_SESSION[$name]);
		}
	}

	/**
	* Returns $_SESSION[$name] if set
	* @param string $name The name of the var
	* @param bool $unserialize If true, will unserialize the returned result
	* @param string $key The session key
	* @param mixed $not_set The return value, if $_SESSION[$name] isn't set
	* @param mixed $default_value The default value to return if $unserialize is true
	* @return mixed
	*/
	public function get(string $name, bool $unserialize = false, string $key = null, $not_set = null, $default_value = [])
	{
		if ($key === null) {
			$key = $this->key;
		}

		$value = '';

		if ($key) {
			if (!isset($_SESSION[$key][$name])) {
				return $not_set;
			}

			$value = $_SESSION[$key][$name];
		} else {
			if (!isset($_SESSION[$name])) {
				return $not_set;
			}

			$value = $_SESSION[$name];
		}

		if ($unserialize) {
			return App::unserialize($value, $default_value);
		}

		return $value;
	}

	/**
	* Sets a session value
	* @param string $name The name of the var
	* @param mixed $value The value
	* @param bool $serialize If true, will serialize the value
	* @param string $key The session key
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function set(string $name, $value, bool $serialize = false, string $key = '', $default_value = '')
	{
		if ($key === null) {
			$key = $this->key;
		}

		if ($serialize) {
			$value = App::serialize($value, $default_value);
		}

		if ($key) {
			$_SESSION[$key][$name] = $value;
		} else {
			$_SESSION[$name] = $value;
		}

		return $this;
	}

	/**
	* Unsets a session value
	* @param string $name The name of the var
	* @param string $key The session key
	* @return $this
	*/
	public function unset(string $name, string $key = null)
	{
		if ($key === null) {
			$key = $this->key;
		}

		if ($key) {
			unset($_SESSION[$key][$name]);
		} else {
			unset($_SESSION[$name]);
		}

		return $this;
	}
}
