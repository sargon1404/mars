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
	 * Validates that a value is greater than $min
	 * @param string $value The value
	 * @param int $min The minimum value
	 * @return bool
	 */
	public function isValid(string $value, int|float $min = null) : bool
	{
		if ($min === null) {
			throw new \Exception("The Validator Min rule must have the minimum number specified. Eg: min:5");
		}

		if ($value >= $min) {
			return true;
		}

		return false;
	}
}
