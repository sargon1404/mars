<?php
/**
* The Date Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Date Validator Class
*/
class Date extends Rule
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

		[$year, $month, $day] = $value;

		return checkdate($month, $day, $year);
	}
}
