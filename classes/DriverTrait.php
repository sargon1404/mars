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
	* @var string $driver The name of the driver
	*/
	//protected string $driver = '';

	/**
	* @var string $driver_namespace The namespace where the driver's class can be found
	*/
	//protected string $driver_namespace = '';

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

		$handle = new $class($this->app);

		$this->checkHandle($handle);

		return $handle;
	}

	/**
	* Sets the driver's handle
	* @param object $handle The handle
	* @return $this
	*/
	public function setHandle(object $handle)
	{
		$this->handle = $handle;

		$this->checkHandle($handle);
	}

	/**
	* Checks the driver's handle
	* @param object $handle The handle
	* @return $this
	*/
	protected function checkHandle(object $handle)
	{
		$interface = $this->driver_namespace . '\\' . 'DriverInterface';

		if (!is_a($handle, $interface)) {
			$class = get_class($handle);

			throw new \Exception("The {$class} driver must implement interface {$interface}");
		}

		return $this;
	}
}
