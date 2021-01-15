<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Min Validator Class
*/
class Min extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $min) : bool
	{
		if ($value >= $min) {
			return true;
		}

		return false;
	}
}
