<?php
/**
* The Serializer Class
* @package Mars
*/

namespace Mars;

use Mars\Serializers\DriverInterface;

/**
* The Serializer Class
* Serializes/Unserializes data
* Change the driver only if you know what you're doing! Preferably at installation time. You might try to unserialize data which has been serialized with a different driver, otherwise
*/
class Serializer
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'serializer';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Serializers\DriverInterface';

	/**
	* protected DriverInterface $php_handle The php handle
	*/
	protected DriverInterface $php_handle;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'php' => '\Mars\Serializers\Php',
		'igbinary' => '\Mars\Serializers\Igbinary'
	];

	/**
	* Constructs the db object
	* @param App $app The app object
	* @param string $driver The serializer driver. Currently supported: php and igbinary
	* @param bool $debug If true, will run in debug mode
	*/
	public function __construct(App $app, string $driver = '')
	{
		$this->app = $app;

		if (!$driver) {
			$driver = $this->app->config->serializer_driver;
		}

		$this->driver = $driver;
		$this->handle = $this->getHandle();

		if ($this->driver == 'php') {
			$this->php_handle = $this->handle;
		} else {
			$this->php_handle = $this->getHandle('php');
		}
	}

	/**
	* Returns the handle used to serialize/unserialize
	* @param bool $use_php_driver If true, will always serialize using the php driver
	* @return DriverInterface The handle
	*/
	protected function getCurrentHandle(bool $use_php_driver) : DriverInterface
	{
		if ($use_php_driver) {
			return $this->php_handle;
		}

		return $this->handle;
	}

	/**
	* Serializes data
	* @param mixed $data The data to serialize
	* @param bool $encode If true, will base64 encode the serialize data
	* @param bool $use_php_driver If true, will always serialize using the php driver
	* @return string The serialized data
	*/
	public function serialize($data, bool $encode = true, bool $use_php_driver = true) : string
	{
		$data = $this->getCurrentHandle($use_php_driver)->serialize($data);

		if ($encode) {
			$data = base64_encode($data);
		}

		return $data;
	}

	/**
	* Unserializes data
	* @param mixed $data The data to unserialize
	* @param mixed $default_value The default value to return if $data is an empty string or null
	* @param bool $decode If true, will base64 decode the serialize data
	* @param bool $use_php_driver If true, will always unserialize using the php driver
	* @return mixed The unserialized data
	*/
	public function unserialize(?string $data, $default_value = [], bool $decode = true, bool $use_php_driver = true)
	{
		if ($data === '' || $data === null) {
			return $default_value;
		}

		if ($decode) {
			$data = base64_decode($data);
		}

		return $this->getCurrentHandle($use_php_driver)->unserialize($data);
	}
}
