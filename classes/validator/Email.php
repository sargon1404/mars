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
class Email extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $params) : bool
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}
