<?php
/**
* The Url Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Url Validator Class
*/
class Url extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $params) : bool
	{
		$url = strtolower($value);

		if (str_starts_with($url, 'ssh://')) {
			return false;
		} elseif (str_starts_with($url, 'ftp://')) {
			return false;
		} elseif (str_starts_with($url, 'ftp://')) {
			return false;
		} elseif (str_starts_with($url, 'mailto:')) {
			return false;
		}

		return filter_var($value, FILTER_VALIDATE_URL);
	}
}
