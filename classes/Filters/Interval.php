<?php
/**
* The Interval Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Interval Filter Class
 */
class Interval
{
	/**
	 * @see \Mars\Filter::interval()
	 */
	public function filter(int|float $value, int|float $min, int|float $max, int|float $default_value) : int|float
	{
		if ($value >= $min && $value <= $max) {
			return $value;
		} else {
			return $default_value;
		}
	}
}
