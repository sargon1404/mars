<?php
/**
* The Config Class
* @package Mars
*/

namespace Mars;

/**
* The Config Class
* Stores the system's config options
*/
class Config extends Data
{
	/**
   * @var bool $log_errors If true, will log all errors to the log files
   */
	public bool $log_errors =  true;

	/**
   * @var int $log_error_reporting The log error reporting level
   */
	public int $log_error_reporting =  E_ALL;

	/**
	* @var string $log_suffix The suffix format of the log files
	*/
	public string $log_suffix = 'd-F-Y';

	/**
	* @var string $log_date_format The format of the log dates
	*/
	public string $log_date_format = 'm-d-Y H:i:s';

	/**
	* @var string $url The url of the site
	*/
	public string $url = '';

	/**
	* @var string $url_static The base url from where the static resources will be served
	*/
	public string $url_static = '';

	/**
	* @var string $key The secret key of the site
	*/
	public string $key = '';

	/**
	* @var string $open_basedir If specified, will limit the files which are accessible to the specified folder. If the value is true the installation dir is used
	*/
	public bool|string $open_basedir = true;

	/**
	* @var bool $gzip If true, will gzip the output
	*/
	public bool $gzip = false;

	/**
	* @var array $trusted_proxies The trusted proxies from which we'll accept the HTTP_X_FORWARDED_FOR header
	*/
	public array $trusted_proxies = [];

	/**
	* @var bool $debug Set to true to enable debug mode
	*/
	public bool $debug = false;

	/**
	* @var string|array $debug_ips If specified, will enable debug only for the listed IPs. Works only if debug is false
	*/
	public string|array $debug_ips = [];

	/**
	* @var bool $development Set to true to enable development mode
	*/
	public bool $development = false;

	/**
	* @var string $development_device Will use this value as device, if specified. Valid values: 'desktop', 'tablet', 'smartphone'
	*/
	public string $development_device = '';

	/**
	* Change the driver only if you know what you're doing! Preferably at installation time.
	* You might try to unserialize data which has been serialized with a different driver, otherwise
	* @var string $serializer_driver The serializer driver. Supported options: php, igbinary
	*/
	public string $serializer_driver = 'php';

	/**
	* @var bool $device_start If false, will not start the device detection functionality
	*/
	public bool $device_start = true;

	/**
	* @var bool $session_start If false, will not start the session functionality
	*/
	public bool $session_start = true;

	/**
	* @var string $session_save_path The path where the sessions will be saved
	*/
	public string $session_save_path = '';

	/**
	* @var string $session_name The session name
	*/
	public string $session_name = '';

	/**
	* @var int $session_cookie_lifetime The lifetime of the session cookie
	*/
	public ?int $session_cookie_lifetime = null;

	/**
	* @var string $session_cookie_path The path of the session cookie
	*/
	public ?string $session_cookie_path = null;

	/**
	* @var string $session_cookie_domain The domain of the session cookie
	*/
	public ?string $session_cookie_domain = null;

	/**
	* @var bool $session_cookie_secure If true the session cookie will only be sent over secure connections.
	*/
	public ?bool $session_cookie_secure = null;

	/**
	* @var bool $session_cookie_httponly If true then httponly flag will be set for the session cookie
	*/
	public ?bool $session_cookie_httponly = true;

	/**
	* @var string $session_cookie_samesite The samesite value of the session_cookie
	*/
	public ?string $session_cookie_samesite = null;

	/**
	* @var string $session_driver The session driver. Supported options: php, db, memcache
	*/
	public string $session_driver = 'php';

	/**
	* @var string $session_driver The table where sessions are stored if the session_driver=db
	*/
	public string $session_table = 'php';

	/**
	* @var string $session_prefix Prefix to apply to all session keys
	*/
	public string $session_prefix = '';

	/**
	* @var int $cookie_expire_days The interval, in days, for which the cookies will be valid
	*/
	public int $cookie_expire_days = 30;

	/**
	* @var string $cookie_path The path of the cookies
	*/
	public string $cookie_path = '/';

	/**
	* @var string $cookie_domain The domain of the cookies
	*/
	public string $cookie_domain = '';

	/**
	* @var bool $cookie_secure If true the cookies will only be sent over secure connections.
	*/
	public bool $cookie_secure = false;

	/**
	* @var bool $cookie_httponly If true then httponly flag will be set for the cookies
	*/
	public bool $cookie_httponly = true;

	/**
	* @var string $cookie_samesite The samesite value of the cookies
	*/
	public string $cookie_samesite = '';

	/**
	* @var string $lang The default language
	*/
	public string $lang = 'english';

	/**
	* @var string $lang The default theme
	*/
	public string $theme = 'theme';

	/**
	* @var string $css_version Version param to apply to all css stylesheets
	*/
	public string $css_version = '1';

	/**
	* @var string $javascript_version Version param to apply to all JS scripts
	*/
	public string $javascript_version = '1';

	/**
	* @var string $html_allowed_elements The allowed html elements; used when filtering html. If null, all elements are allowed
	*/
	public ?string $html_allowed_elements = null;

	/**
	* @var string $html_allowed_attributes The allowed html attributes; used when filtering html
	*/
	public ?string $html_allowed_attributes = '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title';

	/**
	* Builds the Config object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->read();
	}

	/**
	* Reads the config settings from the config.php file
	* @return static
	*/
	public function read() : static
	{
		$this->readFile('config.php');

		return $this;
	}

	/**
	* Reads the config settings from the specified $filename
	* @param string $filename The filename
	* @return static
	*/
	public function readFile(string $filename) : static
	{
		$config = require($this->app->path . $filename);

		$this->assign($config);

		return $this;
	}

	/**
	* Normalizes the config options
	*/
	public function normalize()
	{
		if ($this->device_start) {
			$this->session_start = true;
		}

		if ($this->development) {
			$this->content_cache_enable = false;
			$this->css_version = time();
			$this->javascript_version = time();
		}

		if ($this->debug_ips) {
			if (in_array($this->app->ip, $this->debug_ips)) {
				$this->debug = true;
			}
		}

		if ($this->debug) {
			$this->db_debug = true;
		}
	}
}
