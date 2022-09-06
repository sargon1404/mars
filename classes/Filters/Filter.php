<?php
/**
* The Base Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Base Filter Class
*/
abstract class Filter
{
	use \Mars\AppTrait;

	/**
	* @param string $value The value to filter
	* @param mixed $params Extra params, if any
	*/
	abstract public function get(string $value, ...$params);
}