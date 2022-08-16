<?php
/**
* The Driver Trait
* @package Mars
*/

namespace Mars;

/**
* The Driver Trait
* Trait implementing driver functionality.
* Classes using this trait must set these properties:
* protected string $driver = '';
* protected string $driver_key = '';
* protected string $driver_interface = '';
* protected array $supported_drivers = [];
*/
trait DriverTrait
{
	/**
	* @var object $handle The driver's handle
	*/
	protected object $handle;

	/**
	* @var string $driver The name of the driver
	*/
	//protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	//protected string $driver_key = '';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	//protected string $driver_interface = '';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	//protected array $supported_drivers = [];

	/**
	* Returns the name of the driver to use
	* @return string The driver name
	*/
	public function getDriver() : string
	{
		return $this->driver;
	}

	/**
	* Adds a driver to the list of supported drivers
	* @param string $name The name of the driver
	* @param string $class The class handling the driver
	* @return static
	*/
	public function addSupportedDriver(string $name, string $class) : static
	{
		$this->supported_drivers[$name] = $class;

		return $this;
	}

	/**
	* Removes a driver from the list of supported drivers
	* @param string $name The name of the drive
	* @return static
	*/
	public function removeSupportedDrive(string $name) : static
	{
		unset($this->supported_drivers[$name]);

		return $this;
	}

	/**
	* Returns the handle corresponding to the driver
	* @param string $driver The driver's name
	* @return object The handle
	*/
	public function getHandle(string $driver = '') : object
	{
		if (!$driver) {
			$driver = $this->driver;
		}

		if ($this->driver_key) {
			if (isset($this->app->config->drivers[$this->driver_key])) {
				$this->supported_drivers = $this->supported_drivers + $this->app->config->drivers[$this->driver_key];
			}
		}

		if (!isset($this->supported_drivers[$driver])) {
			throw new \Exception("Driver {$driver} is not on the list of supported drivers");
		}

		$class = $this->supported_drivers[$driver];

		$handle = new $class($this->app);

		if ($this->driver_interface) {
			if (!$handle instanceof $this->driver_interface) {
				throw new \Exception("Driver {$class} must implement interface {$this->driver_interface}");
			}
		}

		return $handle;
	}
}
