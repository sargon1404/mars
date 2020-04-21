<?php
/**
* The Min Chars Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The MaxChars Validator Class
*/
class MaxChars extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $length) : bool
	{
		if (strlen($value) <= $length) {
			return true;
		}

		return false;
	}
}
