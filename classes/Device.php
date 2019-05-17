<?php
/**
* The Device Class
* @package Mars
*/

namespace Mars;

/**
* The Device Class
* Encapsulates the user's device
*/
class Device
{
	use AppTrait;

	/**
	* @var string $type The device type. Eg: desktop/tablet/smartphone
	*/
	public $type = 'desktop';

	/**
	* @var string $devices Array listing the supported devices
	*/
	public $devices = ['desktop', 'tablet', 'smartphone'];

	/**
	* Builds the device object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->device_start) {
			return;
		}

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

	/**
	* Detects the user's device
	* @return string The user's device
	*/
	public function getDevice() : string
	{
		if ($this->app->config->development_device) {
			return $this->app->config->development_device;
		}

		$device = $this->app->session->get('device');
		if ($device !== null) {
			return $device;
		}

		$detector = new Detect\Device($this->app->useragent);

		$device = 'desktop';
		if ($detector->isTablet()) {
			$device = 'tablet';
		} elseif ($detector->isSmartphone()) {
			$device = 'smartphone';
		}

		$this->app->session->set('device', $device);

		return $device;
	}

	/**
	* Returns the device dir [desktop|tablets|smartphones], based on the current device
	* @param string $device If specified, will use $device instead of the current device
	* @return string
	*/
	public function getDir(?string $device = null) : string
	{
		if ($device === null) {
			$device = $this->type;
		}

		switch ($device) {
			case 'tablet':
				return App::MOBILE_DIRS['tablets'];
			case 'smartphone':
				return App::MOBILE_DIRS['smartphones'];
			default:
				return App::MOBILE_DIRS['desktop'];
		}
	}

	/**
	* Returns the device subdir [mobile|mobile/tablets|mobile/smartphones], based on the current device
	* @param string $device If specified, will use $device instead of the current device
	* @return string
	*/
	public function getSubdir(?string $device = null) : string
	{
		if ($device === null) {
			$device = $this->type;
		}

		switch ($device) {
			case 'mobile':
				return App::MOBILE_DIRS['mobile'];
			case 'tablet':
				return App::MOBILE_DIRS['mobile'] . App::MOBILE_DIRS['tablets'];
			case 'smartphone':
				return App::MOBILE_DIRS['mobile'] . App::MOBILE_DIRS['smartphones'];
			default:
				return '';
		}
	}

	/**
	* Returns the list of supported devices
	* @param bool $add_mobile If true, will add 'mobile' as a device list
	* @return array The list of devices
	*/
	public function getDevices(bool $add_mobile = false) : array
	{
		if ($add_mobile) {
			$devices = $this->devices;
			$devices[] = 'mobile';

			return $devices;
		}

		return $this->devices;
	}
}
