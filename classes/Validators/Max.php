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
	 * Validates that a value is lower than $max
	 * @param string $value The value
	 * @param int $max The maximum value
	 * @return bool
	 */
	public function isValid(string $value, int|float $max = null) : bool
	{
		if ($max === null) {
			throw new \Exception("The Max Validator rule must have the max number specified. Eg: max:5");
		}

		if ($value <= $max) {
			return true;
		}

		return false;
	}
}
