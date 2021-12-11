<?php
/**
* The Email Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Email Validator Class
*/
class Email extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $params) : bool
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
