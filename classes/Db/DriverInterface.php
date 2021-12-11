<?php
/**
* The Database Driver Interface
* @package Mars
*/

namespace Mars\Db;

/**
* The Database Driver Interface
*/
interface DriverInterface
{
	/**
	* Connects to the database
	* @param string $hostname The db hostname
	* @param string $port The db port
	* @param string $username The db username
	* @param string $password The db password
	* @param string $database The database to use
	* @param bool $persistent If true, the db connection will be persistent
	* @param string $charset The database charset
	*/
	public function connect(string $hostname, string $port, string $username, string $password, string $database, bool $persistent, string $charset);

	/**
	* Disconnects from the database
	*/
	public function disconnect();
	
	/**
	* Begins a transaction
	*/
	public function begin();
	
	/**
	* Commits a transaction
	*/
	public function commit();
	
	/**
	* Rollback a transaction
	*/
	public function rollback();

	/**
	* Executes a query
	* @param string $sql The query to execute
	* @param array $params Params to be used in prepared statements
	* @return object The result
	*/
	public function query(string $sql, array $params = []) : ?object;

	/**
	* Frees the results of a query
	* @param resource $result The result
	*/
	public function free($result);

	/**
	* Returns the last id of an insert/replace operation
	* @return int The last id
	*/
	public function lastId() : int;

	/**
	* Returns the number of affected rows of an update/replace operation
	* @return int The number of affected rows
	*/
	public function affectedRows() : int;

	/**
	* Returns the number of rows of a select operation
	* @param resource $result The result
	* @return int The number of rows
	*/
	public function numRows($result) : int;

	/**
	* Returns the next row, as an array, from a results set
	* @param resource $result The result
	* @return array The row
	*/
	public function fetchArray($result) : array;

	/**
	* Returns the next row, as an array, from a results set
	* @param resource $result The result
	* @return array The row
	*/
	public function fetchRow($result) : array;

	/**
	* Returns the next row, as an object, from a results set
	* @param resource $result The result
	* @param string $class_name The class name
	* @return object The data. If no row was found, null is returned
	*/
	public function fetchObject($result, string $class_name = '') : ?object;
	
	/**
	* Returns a single column from the results set
	* @param resource $result The result
	* @param int $column The column index
	* @return string The column or null if there isn't any
	*/
	public function fetchColumn($result, int $column = 0) : ?string;

	/**
	* Returns all the rows as objects
	* @param resource $result The result
	* @param string $class_name The class name, if any. If true is passed, will return the rows as arrays
	* @return array The rows
	*/
	public function fetchAll($result, bool|string $class_name = '') : array;
	
	/**
	* Returns all the results from a column
	* @param resource $result The result
	* @param int $column The column
	* @return array The rows
	*/
	public function fetchAllFromColumn($result, int $column = 0) : array;
}
