<?php
/**
* The Database Result Class
* @package Mars
*/

namespace Mars;

use Mars\Db\DriverInterface;

/**
 * The Database Result Class
 * Handles the database results
 */
class DbResult
{
	/**
	 * @var DriverInterface $handle The handle which generated the result
	 */
	protected DriverInterface $handle;

	/**
	 * @var object The result returned by a database query
	 */
	protected object $result;

	/**
	 * Builds the DbResult object
	 * @param DriverInterface $handle The handle which generated the result
	 * @param object The result returned by a database query
	 */
	public function __construct(DriverInterface $handle, object $result)
	{
		$this->handle = $handle;
		$this->result = $result;
	}

	/**
	 * Frees the database result
	 */
	public function __destruct()
	{
		$this->handle->free($this->result);
	}

	/**
	 * Returns the current row
	 * @return array The row
	 */
	public function fetchArray() : array
	{
		return $this->handle->fetchArray($this->result);
	}

	/**
	 * Returns the current row
	 * @return array The row
	 */
	public function fetchRow() : array
	{
		return $this->handle->fetchRow($this->result);
	}

	/**
	 * Returns the current row
	 * @param string $class_name The name of the class. If empty stdClass is used
	 * @return object An object or null on failure
	 */
	public function fetchObject(string $class_name = '') : ?object
	{
		return $this->handle->fetchObject($this->result, $class_name);
	}

	/**
	 * Alias of fetchObject
	 * @see DbResult::fetchObject()
	 */
	public function fetch(string $class_name = '') : ?object
	{
		return $this->fetchObject($class_name);
	}

	/**
	 * Returns a single column from the results set
	 * @param int $column The column index
	 * @return string The column or null if there isn't any
	 */
	public function fetchColumn(int $column = 0) : ?string
	{
		return $this->handle->fetchColumn($this->result, $column);
	}

	/**
	 * Returns all the rows
	 * @param string $class_name The class name, if any. If true is passed, will return the rows as arrays
	 * @return array The rows
	 */
	public function fetchAll(bool|string $class_name = '') : array
	{
		return $this->handle->fetchAll($this->result, $class_name);
	}

	/**
	 * Alias for fetchAll
	 * @see DbResult::fetchAll
	 */
	public function all(bool|string $class_name = '') : array
	{
		return $this->fetchAll($class_name);
	}

	/**
	 * Returns all the results from a column
	 * @param int $column The column
	 * @return array The rows
	 */
	public function fetchAllFromColumn(int $column = 0) : array
	{
		return $this->handle->fetchAllFromColumn($this->result, $column);
	}

	/**
	 * Returns the id of an insert operation
	 * @return int
	 */
	public function lastId() : int
	{
		return $this->handle->lastId();
	}

	/**
	 * Returns the number of affected rows
	 * @return int
	 */
	public function affectedRows() : int
	{
		return $this->handle->affectedRows();
	}

	/**
	 * Gets the number of rows returned by a query
	 * @return int The number of rows
	 */
	public function numRows() : int
	{
		return $this->handle->numRows($this->result);
	}

	/**
	 * Returns the results generated by a query.
	 * @param string $key_field The name of the column which will be used as the returned array's key, if any
	 * @param string $field If specified, will return only this field rather than all of them
	 * @param string $class_name The name of the class to instantiate when loading the objects
	 * @param bool $as_array If true will return the rows as arrays
	 * @return array The rows
	 */
	public function get(string $key_field = '', ?string $field = null, string $class_name = '', bool $as_array = false) : array
	{
		if ($field || $as_array) {
			$class_name = true;
			$as_array = true;
		}

		$rows = $this->fetchAll($class_name);

		if ($key_field || $field) {
			$rows = array_column($rows, $field, $key_field);
		}

		return $rows;
	}

	/**
	 * Returns the results generated by a query as an array or arrays
	 * @param string $key_field The name of the column which will be used as the returned array's key, if any
	 * @param string $field If specified, will return only this field rather than all of them
	 * @return array The rows
	 */
	public function getArray(string $key_field = '', ?string $field = null) : array
	{
		return $this->get($key_field, $field, '', true);
	}

	/**
	 * Returns the first column from the generated results
	 * @return array The results
	 */
	public function getCol() : array
	{
		return $this->fetchAllFromColumn();
	}

	/**
	 * Returns the value from the first column of the first row
	 * @return string The result or null, if there isn't any
	 */
	public function getResult() : ?string
	{
		return $this->fetchColumn();
	}

	/**
	 * Returns the result of a count query
	 * @return int
	 */
	public function getCount() : int
	{
		return (int)$this->getResult();
	}
}
