<?php
/**
* The Accelerator Class
* @package Mars
*/

namespace Mars;

/**
* The Accelerator Class
* Handles the interactions with http accelerator - like varnish for example
*/
class Accelerator
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var bool $enabled Will be set to true, if enabled
	*/
	protected bool $enabled = false;

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'accelerators';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Accelerators\DriverInterface';

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

		$this->driver = $this->app->config->accelerator_driver;

		$this->handle = $this->getHandle();
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

		return $this->handle->delete($url);
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

		return $this->handle->deleteByPattern($pattern);
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

		return $this->handle->deleteAll();
	}
}
