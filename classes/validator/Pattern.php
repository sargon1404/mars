<?php
/**
* The Pattern Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Pattern Validator Class
*/
class Pattern extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate($value, $pattern) : bool
	{
		if (preg_match($pattern, $value, $m)) {
			return false;
		}

		return true;
	}
}
