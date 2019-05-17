<?php
/**
* The Detect Device Class
* @package Mars
*/

namespace Mars\Detect;

/**
* The Detect Device Class
* Detects the device a user is using from the useragent
*/
class Device
{
	/**
	* @var string $useragent The useragent to use
	*/
	public $useragent = '';

	/**
	* @var object $handle The driver's handle
	*/
	protected $handle = null;

	/**
	* Builds the Device object
	* @param string $useragent The useragent
	*/
	public function __construct(string $useragent)
	{
		$this->useragent = $useragent;
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
