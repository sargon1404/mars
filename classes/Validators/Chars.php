<?php
/**
* The Chars Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Chars Validator Class
*/
class Chars extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_minchars_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (!isset($params[0]) || !isset($params[1])) {
			throw new \Exception("The Chars Validator rule must have the minimum/maximum number of chars. specified. Eg: chars:1:5");
		}

		$min = (int)$params[0];
		$max = (int)$params[1];

		$length = mb_strlen($value);

		if ($length >= $min && $length <= $max) {
			return true;
		}

		return false;
	}
}
