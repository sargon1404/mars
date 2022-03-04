<?php
/**
* The Interval Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Interval Filter Class
*/
class Interval extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $value, ...$params) : int|float
	{
		$min = $params[0] ?? 0;
		$max = $params[1] ?? 0;
		$default_value = $params[2] ?? 0;

		if ($value >= $min && $value <= $max) {
			return $value;
		} else {
			return $default_value;
		}
	}
}
