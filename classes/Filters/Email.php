<?php
/**
* The Email Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Email Filter Class
*/
class Email
{
	/**
	* @see \Mars\Filter::email()
	*/
	public function filter(string $email) : string
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}
}
