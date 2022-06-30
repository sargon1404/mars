<?php
/**
* The Url Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Url Validator Class
*/
class Url extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_url_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		$url = strtolower($value);

		if (str_starts_with($url, 'ssh://')) {
			return false;
		} elseif (str_starts_with($url, 'ftp://')) {
			return false;
		} elseif (str_starts_with($url, 'mailto:')) {
			return false;
		}

		return filter_var($value, FILTER_VALIDATE_URL);
	}
}
