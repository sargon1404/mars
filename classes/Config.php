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
	* @var array $defaults Array listing the default values of some config values
	*/
	protected array

 $defaults = [
		'log_error_types' => E_ALL & ~E_NOTICE,

		'site_url_static' => '',

		'debug' => false, //set to true to enable debug mode
		'debug_ips' => '', //if specified, will enable debug only for the listed IPs. debug must be set to false

		'development' => false, //set to true to enable development mode
		'development_plugins' => false, //if true, will output the plugin hooks names
		'development_device' => '', //will use this value as device, if specified

		'device_start' => false,

		'session_start' => true,
		'session_cookie_name' => '',
		'session_cookie_path' => '',
		'session_cookie_domain' => '',
		'session_save_path' => '',
		'session_table' => 'sessions',
		'session_driver' => 'php',

		'cookie_path' => '/',
		'cookie_domain' => '',
		'cookie_expires_days' => 30,

		'gzip' => false,

		'content_cache_enable' => false,
		'content_cache_driver' => 'file',
		'content_cache_expires_interval' => 24,
		'content_cache_gzip' => false,
		'content_cache_minify' => false,

		'lang' => 'english',
		'theme' => 'default',

		'css_version' => '1',
		'css_output' => true,

		'javascript_version' => '1',
		'javascript_output' => false,
	];

	/**
	* Reads the config settings from the config.php file
	* @return $this
	*/
	public function read()
	{
		$this->readFilename('config.php');

		$this->setDefault();

		$this->normalize();

		return $this;
	}

	/**
	* Reads the config settings from the specified $filename
	* @param string $filename The filename
	* @return $this
	*/
	public function readFilename(string $filename)
	{
		$config = require($filename);

		$this->assign($config);

		return $this;
	}

	/**
	* Sets default config values, if missing in the config file
	*/
	protected function setDefault()
	{
		foreach ($this->defaults as $name => $value) {
			if (!isset($this->$name)) {
				$this->$name = $value;
			}
		}
	}

	/**
	* Normalizes the config options
	*/
	protected function normalize()
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
			if (in_array($_SERVER['REMOTE_ADDR'], $this->debug_ips)) {
				$this->debug = true;
			}
		}

		if ($this->debug) {
			$this->db_debug = true;
		}
	}
}
