<?php
/**
* The Url Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Url Validator Class
*/
class Url extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $params) : bool
	{
		$url = strtolower($value);

		if (str_starts_with($url, 'ssh://')) {
			return false;
		} elseif (str_starts_with(($url, 'ftp://')) {
			return false;
		} elseif (str_starts_with(($url, 'mailto:')) {
			return false;
		}

		return filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}
}
