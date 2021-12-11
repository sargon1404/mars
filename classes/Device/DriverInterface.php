<?php
/**
* The Device Detector Driver Interface
* @package Mars
*/

namespace Mars\Device;

/**
* The Device Detector Driver Interface
*/
interface DriverInterface
{
	/**
	* Returns true if the device is a tablet
	* @return bool
	*/
	public function isTablet() : bool;
	
	/**
	* Returns true if the device is a smartphone
	* @return bool
	*/
	public function isSmartphone() : bool;
}
