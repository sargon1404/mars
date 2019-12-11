<?php
/**
* The Driver Trait
* @package Mars
*/

namespace Mars;

/**
* The Driver Trait
* Trait implementing driver functionality
*/
trait DriverTrait
{
	/**
	* @var string $driver The name of the driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_namespace The namespace where the driver's class can be found
	*/
	protected string $driver_namespace = '';

	/**
	* @var object $handle The driver's handle
	*/
	protected object $handle;

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

		$class = $this->driver_namespace . '\\' . App::strToClass($driver);
		$interface = $this->driver_namespace . '\\' . 'DriverInterface';

		$handle = new $class($this->app);

		if (!is_a($handle, $interface)) {
			throw new \Exception("The {$class} driver must implement interface {$interface}");
		}

		return $handle;
	}
}
