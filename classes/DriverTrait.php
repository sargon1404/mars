<?php
/**
* The Driver Trait
* @package Mars
*/

namespace Mars;

/**
* The Driver Trait
* Trait implementing driver functionality. Classes using this trait must set the $driver and $driver_namespace properties
*/
trait DriverTrait
{
	/**
	* @var object $handle The driver's handle
	*/
	//protected object $handle;

	/**
	* @var string $driver The name of the driver
	*/
	//protected string $driver = '';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	//protected array $supported_drivers = [];

	/**
	* Adds a supported driver
	* @param string $name The name of the driver
	* @param string $class The class which will handle it
	* @return $this
	*/
	public function addSupportedDriver(string $name, string $class)
	{
		$this->supported_drivers[$name] = $class;
	}

	/**
	* Removes a supported driver
	* @param string $name The name of the driver
	* @return $this
	*/
	public function removeSupportedDriver(string $name)
	{
		unset($this->supported_drivers[$name]);

		return $this;
	}

	/**
	* Returns the name of the driver to use
	* @return string The driver name
	*/
	public function getDriver() : string
	{
		return $this->driver;
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

		if (!isset($this->supported_drivers[$driver])) {
			throw new \Exception("Driver {$driver} is not on the list of supported drivers");
		}

		$class = $this->supported_drivers[$driver];

		$handle = new $class($this->app);

		return $handle;
	}
}
