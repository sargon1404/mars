<?php
/**
* The Json Class
* @package Mars
*/

namespace Mars;

/**
* The Json Class
* Encodes/Decodes data using json
*/
class Json
{
	use AppTrait;

	/**
	* Encodes data
	* @param mixed $data The data to encode
	* @return string The encoded string
	*/
	public function encode($data) : string
	{
		if (!$data) {
			return '';
		}

		return json_encode($data);
	}

	/**
	* Decodes a string
	* @param string $string The string to decode
	* @return mixed The decoded data
	*/
	public function decode(string $string)
	{
		if (!$string) {
			return '';
		}

		return json_decode($string, true);
	}
}
