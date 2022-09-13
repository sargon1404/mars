<?php
/**
* The Required Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Required Validator Class
*/
class Required extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_required_error';

	/**
	* Validates that a value is not empty
	* @param string $value The value
	* @return bool
	*/
	public function isValid(string $value) : bool
	{
		if (trim($value)) {
			return true;
		}

		return false;
	}
}
