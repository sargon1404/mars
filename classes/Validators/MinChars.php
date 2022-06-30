<?php
/**
* The Min Chars Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The MinChars Validator Class
*/
class MinChars extends Rule
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
		if (!isset($params[0])) {
			throw new \Exception("The MinChars Validator rule must have the minimum number of chars. specified. Eg: min_chars:5");
		}

		$length = (int)$params[0];

		if (mb_strlen($value) >= $length) {
			return true;
		}

		return false;
	}
}
