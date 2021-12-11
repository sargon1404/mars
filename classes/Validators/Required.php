<?php
/**
* The Required Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Required Validator Class
*/
class Required extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $params) : bool
	{
		if ($value) {
			return true;
		}

		return false;
	}
}
