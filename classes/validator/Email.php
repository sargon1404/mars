<?php
/**
* The Email Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Email Validator Class
*/
class Email extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $params) : bool
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
