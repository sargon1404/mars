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
	* Validates the number of chars of a string
	* @param string $value The value
	* @param int $length The minimum length of the string
	* @return bool
	*/
	public function isValid(string $value, int $length = null) : bool
	{
		if ($length === null) {
			throw new \Exception("The MinChars Validator rule must have the minimum number of chars. specified. Eg: min_chars:5");
		}

		if (mb_strlen($value) >= $length) {
			return true;
		}

		return false;
	}
}
