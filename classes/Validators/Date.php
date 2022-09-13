<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Date Validator Class
*/
class Date extends Datetime
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_date_error';

	/**
	* Validates a date
	* @param string $value The value to validate
	* @param string $format The date's format
	* @return bool Returns true if the date is valid
	*/
	public function isValid(string $value, string $format = null) : bool
	{
		$format = $format ?? $this->app->lang->date_picker_format;

		return $this->isValidDateTime($value, $format);
	}
}
