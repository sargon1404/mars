<?php
/**
* The Min Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Base Validator Class
*/
class Min extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $length) : bool
	{
		if (strlen($value) >= $length) {
			return true;
		}

		return false;
	}
}
