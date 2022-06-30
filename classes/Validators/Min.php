<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Min Validator Class
*/
class Min extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_min_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (!isset($params[0])) {
			throw new \Exception("The Validator Min rule must have the minimum number specified. Eg: min:5");
		}

		$min = (float)$params[0];

		if ($value >= $min) {
			return true;
		}

		return false;
	}
}
