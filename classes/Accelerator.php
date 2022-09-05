<?php
/**
* The Accelerator Class
* @package Mars
*/

namespace Mars;

use Mars\Accelerators\DriverInterface;

/**
* The Accelerator Class
* Handles the interactions with http accelerator - like varnish for example
*/
class Accelerator
{
	use AppTrait;

	/**
	* @var bool $enabled Will be set to true, if enabled
	*/
	protected bool $enabled = false;

	/**
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var DriverInterface $driver The driver object
	*/
	protected DriverInterface $driver;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'varnish' => '\Mars\Accelerators\Varnish'
	];

	/**
	* Constructs the accelerator object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->accelerator_enable) {
			return;
		}

		$this->enabled = true;
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'accelerators', $this->app);
		$this->driver = $this->drivers->get($this->app->config->accelerator_driver);
	}

	/**
	* Returns true if memcache is enabled
	*/
	public function isEnabled() : bool
	{
		return $this->enabled;
	}

	/**
	* Deletes $url from the accelerator's cache
	* @param string $url The url to delete
	* @return bool
	*/
	public function delete(string $url) : bool
	{
		if (!$this->enabled) {
			return $this;
		}

		return $this->driver->delete($url);
	}

	/**
	* Deletes by pattern from the accelerator's cache
	* @param string $pattern The pattern
	* @return bool
	*/
	public function deleteByPattern(string $pattern) : bool
	{
		if (!$this->enabled) {
			return $this;
		}

		return $this->driver->deleteByPattern($pattern);
	}

	/**
	* Deletes all the data from the accelerator's cache
	* @return bool
	*/
	public function deleteAll() : bool
	{
		if (!$this->enabled) {
			return $this;
		}

		return $this->driver->deleteAll();
	}
}
