<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Max Validator Class
*/
class Max extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_max_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (!isset($params[0])) {
			throw new \Exception("The Max Validator rule must have the max number specified. Eg: max:5");
		}

		$min = (float)$params[0];

		if ($value <= $min) {
			return true;
		}

		return false;
	}
}
