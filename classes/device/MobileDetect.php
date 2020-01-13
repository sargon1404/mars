<?php
/**
* The Detect Device Class
* @package Mars
*/

namespace Mars\Device;

use Mars\App;

/**
* The Detect Device Class
* Detects the device a user is using from the useragent
*/
class MobileDetect implements DriverInterface
{
	/**
	* @var string $useragent The useragent to use
	*/
	public string $useragent = '';

	/**
	* Builds the Device object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->useragent = $app->useragent;
	}

	/**
	* Inits the handle
	*/
	protected function init()
	{
		if ($this->handle) {
			return;
		}

		$this->handle = new \Mobile_Detect(null, $this->useragent);
	}

	/**
	* Returns true if the device is a tablet
	* @return bool
	*/
	public function isTablet() : bool
	{
		$this->init();

		return $this->handle->isTablet();
	}

	/**
	* Returns true if the device is a smartphone
	* @return bool
	*/
	public function isSmartphone() : bool
	{
		$this->init();

		return $this->handle->isMobile();
	}
}
