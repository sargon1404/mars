<?php
/**
* The Encoder Class
* @package Mars
*/

namespace Mars;

/**
* The Encoder Class
* Encodes/Decods data using json
*/
class Encoder
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
