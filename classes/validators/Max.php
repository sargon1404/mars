<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Max Validator Class
*/
class Max extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $max) : bool
	{
		if ($value <= $max) {
			return true;
		}

		return false;
	}
}
