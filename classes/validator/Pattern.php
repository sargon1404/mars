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
class Pattern extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $pattern) : bool
	{
		if (preg_match($pattern, $value, $m)) {
			return false;
		}

		return true;
	}
}
