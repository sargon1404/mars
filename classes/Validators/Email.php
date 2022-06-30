<?php
/**
* The Email Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Email Validator Class
*/
class Email extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_email_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
