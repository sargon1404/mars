<?php
/**
* The Datetime Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Datetime Validator Class
*/
class Datetime extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_datetime_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		$format = $params[0] ?? $this->app->lang->datetime_picker_format;

		return $this->isValidDateTime($value, $format);
	}

	/**
	* Determines if $value is a valid DateTime
	* @param string $value The value
	* @param string $format The format
	* @return bool
	*/
	protected function isValidDateTime(string $value, string $format) : bool
	{
		try {
			$dt = \DateTime::createFromFormat($format, $value);
			if (!$dt) {
				return false;
			}

			$errors = $dt->getLastErrors();
			if ($errors['warning_count'] || $errors['error_count']) {
				return false;
			}
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}
}
