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
	* Returns the device's type: desktop,tablet,smartphone
	* @param string $useragent The useragent. If null, the user's useragent is used
	* @return string
	*/
	public function get(string $useragent = null) : string;
}
