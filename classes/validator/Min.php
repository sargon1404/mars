<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Min Validator Class
*/
class Min extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $min) : bool
	{
		if ($value >= $min) {
			return true;
		}

		return false;
	}
}
