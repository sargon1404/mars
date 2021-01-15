<?php
/**
* The Min Chars Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The MaxChars Validator Class
*/
class MaxChars extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $length) : bool
	{
		if (strlen($value) <= $length) {
			return true;
		}

		return false;
	}
}
