<?php
/**
* The GeoIP Class
* @package Mars
*/

namespace Mars\Helpers;

/**
* The GeoIP Class
* Wrapper around the GeoIP extension
*/
class Geoip
{
	/**
	* @var bool $enabled Will be set to true, if geoip is enabled
	*/
	public $enabled = false;

	/**
	* @var string $localhost The name for localhost
	*/
	protected $localhost = 'localhost';

	/**
	* Builds the Geoip object
	*/
	public function __construct()
	{
		if (extension_loaded('geoip')) {
			$this->enabled = true;
		}
	}

	/**
	* Determines the country name from ip
	* @param string $ip The IP
	* @return string The country
	*/
	public function getCountry(string $ip) : string
	{
		if (!$this->enabled || !$ip) {
			return '';
		}

		if ($this->isLocalhost($ip)) {
			return $this->localhost;
		}

		return @geoip_country_name_by_name($ip);
	}

	/**
	* Determines the region's name from ip
	* @param string $ip The IP
	* @return string The region
	*/
	public function getRegion(string $ip) : string
	{
		if (!$ip || !$this->enabled) {
			return '';
		}

		if (!$this->enabled || !$ip) {
			return $this->localhost;
		}

		return @geoip_region_by_name($ip);
	}

	/**
	* Determines the ISP's name from ip
	* @param string $ip The IP
	* @return string The isp
	*/
	public function getIsp(string $ip) : string
	{
		if (!$ip || !$this->enabled) {
			return '';
		}

		if (!$this->enabled || !$ip) {
			return $this->localhost;
		}

		return geoip_isp_by_name($ip);
	}

	/**
	* Determines if the $ip corresponds to localhost
	* @param string $ip The IP
	* @return bool
	*/
	protected function isLocalhost(string $ip) : bool
	{
		if ($ip == '127.0.0.1') {
			return true;
		}

		return false;
	}
}
