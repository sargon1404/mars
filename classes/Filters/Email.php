<?php
/**
* The Email Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Email Filter Class
*/
class Email extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get($email, ...$params) : string
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}
}
