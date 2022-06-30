<?php
/**
* The Unique Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Unique Validator Class
*/
class Unique extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_unique_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (empty($params[0])) {
			throw new \Exception("The Validator Unique rule must have the name of the table and (optionally) column specified. Eg: unique:users or unique:users:id");
		}

		$table = $params[0];
		$col = $params[1] ?? 'id';

		return $this->app->db->exists($table, [$col => $value], $col);
	}
}
