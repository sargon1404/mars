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
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (!isset($params[0]) || !isset($params[1])) {
			throw new \Exception("The Interval Validator rule must have the minimum/maximum number specified. Eg: interval:1:5");
		}

		$min = (float)$params[0];
		$max = (float)$params[1];

		if ($value >= $min && $value <= $max) {
			return true;
		}

		return false;
	}
}
