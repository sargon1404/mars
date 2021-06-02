<?php
/**
* The Random Class
* @package Mars
*/

namespace Mars;

/**
* The Random Class
* Generates random numbers/strings
*/
class Random
{
	use AppTrait;

	/**
	* Returns a random string
	* @param int $max The maximum number of chars. the string should have
	* @return string A random string
	*/
	public function getString(int $max = 32) : string
	{
		$str = bin2hex(random_bytes($max));

		return substr($str, 0, $max);
	}

	/**
	* Returns a random number
	* @param int $min Lowest value to be returned
	* @param int $max Highest value to be returned
	* @return int A random number
	*/
	public function getInt(int $min = 0, int $max = 0) : int
	{
		return random_int($min, $max);
	}
}
