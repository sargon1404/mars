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
	 * Validates the number of chars of a string
	 * @param string $value The value
	 * @param int $min The minimum no. of chars
	 * @param int $max The maximum no. of chars
	 * @return bool
	 */
	public function isValid(string $value, int $min = null, int $max = null) : bool
	{
		if ($min === null || $max === null) {
			throw new \Exception("The Chars Validator rule must have the minimum/maximum number of chars. specified. Eg: chars:1:5");
		}

		$length = mb_strlen($value);

		if ($length >= $min && $length <= $max) {
			return true;
		}

		return false;
	}
}
