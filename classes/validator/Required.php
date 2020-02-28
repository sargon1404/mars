<?php
/**
* The Required Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Required Validator Class
*/
class Required extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $params) : bool
	{
		if ($value) {
			return true;
		}

		return false;
	}
}
