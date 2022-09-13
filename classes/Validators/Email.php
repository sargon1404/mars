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
	* Checks if $value is a valid email address
	* @param string $value The email to validate
	* @return bool Returns true if the email is valid
	*/
	public function isValid(string $value) : bool
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
