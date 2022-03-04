<?php
/**
* The Base Format Class
* @package Mars
*/

namespace Mars\Formats;

/**
* The Base Format Class
*/
abstract class Format
{
	use \Mars\AppTrait;

	/**
	* @param string $value The value to format
	* @param mixed $params Extra params, if any
	*/
	abstract public function get(string $value, ...$params);
}
