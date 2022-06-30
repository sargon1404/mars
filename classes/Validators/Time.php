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
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		$format = $params[0] ?? $this->app->lang->time_picker_format;

		return $this->isValidDateTime($value, $format);
	}
}
