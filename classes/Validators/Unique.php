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
	 * Validates that a value is unique in a table
	 * @param string $value The value
	 * @param string $table The name of the table
	 * @param string $column The name of the column
	 * @return bool
	 */
	public function isValid(string $value, string $table = null, string $column = 'id') : bool
	{
		if (!$table) {
			throw new \Exception("The Validator Unique rule must have the name of the table and (optionally) column specified. Eg: unique:users or unique:users:id");
		}

		return $this->app->db->exists($table, [$column => $value], $column);
	}
}
