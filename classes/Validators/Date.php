<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Date Validator Class
*/
class Date extends DateTime
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_date_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		$format = $params[0] ?? $this->app->lang->date_picker_format;

		return $this->isValidDateTime($value, $format);
	}
}
