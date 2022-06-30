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
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (trim($value)) {
			return true;
		}

		return false;
	}
}
