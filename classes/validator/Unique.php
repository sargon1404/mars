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
class Unique extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $params) : bool
	{
		if ($this->app->db->exists($this->table, [$this->field => $value])) {
			return false;
		}

		return true;
	}
}
