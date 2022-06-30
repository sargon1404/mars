<?php
/**
* The Min Chars Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The MaxChars Validator Class
*/
class MaxChars extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_maxchars_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (!isset($params[0])) {
			throw new \Exception("The  MaxChars Validator rule must have the max number of chars. specified. Eg: max_chars:5");
		}

		$length = (int)$params[0];

		if (mb_strlen($value) <= $length) {
			return true;
		}

		return false;
	}
}
