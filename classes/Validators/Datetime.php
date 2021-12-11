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
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $params) : bool
	{
		if (!is_array($value)) {
			throw new \Exception('The Time validator accepts an array with [$year, $month, $day] as the value parameter');
		}

		[$year, $month, $day, $hour, $minute, $second] = $value;

		$date = new Date($this->app);
		if (!$date->validate([$year, $month, $day], $params)) {
			return false;
		}

		$time = new Time($this->app);
		if (!$time->validate([$hour, $minute, $second], $params)) {
			return false;
		}

		return true;
	}
}
