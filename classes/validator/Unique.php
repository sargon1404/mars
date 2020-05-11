<?php
/**
* The Unique Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Unique Validator Class
*/
class Unique extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritDocs}
	*/
	public function validate($value, $params) : bool
	{
		if ($this->app->db->exists($this->table, [$this->field => $value])) {
			return false;
		}

		return true;
	}
}
