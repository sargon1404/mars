<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validator;

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
	public function validate($value, $max) : bool
	{
		if ($value <= $max) {
			return true;
		}

		return false;
	}
}
