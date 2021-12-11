<?php
/**
* The Unique Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
* The Unique Validator Class
*/
class Unique extends Rule
{
	/**
	* @see \Mars\Validator\Rule::validate()
	* {@inheritdoc}
	*/
	public function validate(string|array $value, string|array $params) : bool
	{
		if ($this->app->db->exists($this->table, [$this->field => $value])) {
			return false;
		}

		return true;
	}
}
