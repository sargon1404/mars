<?php
/**
* The Time Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Time Validator Class
*/
class Time extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $params) : bool
	{
		if (!is_array($value)) {
			throw new \Exception('The Time validator accepts an array with [$hour, $minute, $second] as the value parameter');
		}

		[$hour, $minute, $second] = $value;

		if ($hour < 0 || $hour > 23) {
			return false;
		}
		if ($minute < 0 || $minute > 59) {
			return false;
		}
		if ($second < 0 || $second > 59) {
			return false;
		}

		return true;
	}
}
