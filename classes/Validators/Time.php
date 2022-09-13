<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Date Validator Class
*/
class Time extends DateTime
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_time_error';

	/**
	* Validates a time value
	* @param string $value The value to validate
	* @param string $format The time's format
	* @return bool Returns true if the time value is valid
	*/
	public function isValid(string $value, string $format = null) : bool
	{
		$format = $format ?? $this->app->lang->time_picker_format;

		return $this->isValidDateTime($value, $format);
	}
}
