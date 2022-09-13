<?php
/**
* The Interval Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Interval Validator Class
*/
class Interval extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_interval_error';

	/**
	* Validates an interval
	* @param string $value The value
	* @param int $min The minimum value
	* @param int $max The maximum value
	* @return bool
	*/
	public function isValid(string $value, int $min = null, int $max = null) : bool
	{
		if ($min === null || $max === null) {
			throw new \Exception("The Interval Validator rule must have the minimum/maximum number specified. Eg: interval:1:5");
		}

		if ($value >= $min && $value <= $max) {
			return true;
		}

		return false;
	}
}
