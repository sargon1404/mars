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
	use \Mars\AppTrait;

	/**
	* Builds the Device object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app ?? $this->getApp();
	}

	/**
	* @see \Mars\Device\DriverInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $useragent = null) : string
	{
		$useragent = $useragent ?? $this->app->useragent;
		$handle = new \Mobile_Detect(null, $useragent);

		if ($handle->isTablet()) {
			return 'tablet';
		} elseif ($handle->isMobile()) {
			return 'smartphone';
		}

		return 'desktop';
	}
}
