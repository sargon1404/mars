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
class Required extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $params) : bool
	{
		if ($value) {
			return true;
		}

		return false;
	}
}
