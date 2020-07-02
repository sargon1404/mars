<?php
/**
* The Min Chars Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The MinChars Validator Class
*/
class MinChars extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate($value, $length) : bool
	{
		if (strlen($value) >= $length) {
			return true;
		}

		return false;
	}
}
