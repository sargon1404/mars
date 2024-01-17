<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

use Mars\Device\DriverInterface;

/**
 * The Device Class
 * Encapsulates the user's device
 */
class Device
{
	use AppTrait;

	/**
	 * @var Drivers $drivers The drivers object
	 */
	public readonly Drivers $drivers;

	/**
	 * @var string $type The device type. Eg: desktop/tablet/smartphone
	 */
	public readonly string $type;

	/**
	 * @var string $devices Array listing the supported devices
	 */
	public readonly array $devices;

	/**
	 * @var array $supported_drivers The supported drivers
	 */
	protected array $supported_drivers = [
		'mobile_detect' => '\Mars\Device\MobileDetect'
	];

	/**
	 * Builds the device object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->device_start || $this->app->is_bin) {
			return;
		}

		$this->devices = ['desktop', 'tablet', 'smartphone'];
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'device', $this->app);

		$this->type = $this->getDevice();
	}

	/**
	 * Returns the current device
	 * @return string The device
	 */
	public function get() : string
	{
		return $this->type;
	}

	/**
	 * Detects the user's device
	 * @return string The user's device
	 */
	protected function getDevice() : string
	{
		if ($this->app->config->development_device) {
			return $this->app->config->development_device;
		}

		$device = $this->app->session->get('device');
		if ($device !== null) {
			return $device;
		}

		//do we get the device name from varnish?
		if (isset($_SERVER['X-Device'])) {
			if (in_array($_SERVER['X-Device'], $this->devices)) {
				$device = $_SERVER['X-Device'];
			}
		}

		if (!$device) {
			$driver = $this->drivers->get($this->app->config->device_driver);
			$device = $driver->get();
		}

		$this->app->session->set('device', $device);

		return $device;
	}

	/**
	 * Returns true if the user is using a desktop
	 * @return bool
	 */
	public function isDesktop() : bool
	{
		if (!$this->type || $this->type == 'desktop') {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the user is using a tablet/smartphone
	 * @return bool
	 */
	public function isMobile() : bool
	{
		if ($this->type == 'tablet' || $this->type == 'smartphone') {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the user is using a tablet
	 * @return bool
	 */
	public function isTablet() : bool
	{
		if ($this->type == 'tablet') {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the user is using a smartphone
	 * @return bool
	 */
	public function isSmartphone() : bool
	{
		if ($this->type == 'smartphone') {
			return true;
		}

		return false;
	}
}
