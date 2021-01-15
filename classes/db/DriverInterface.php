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
	* @param string $charset The database charset
	*/
	public function connect(string $hostname, string $port, string $username, string $password, string $database, string $charset);

	/**
	* Disconnects from the database
	*/
	public function disconnect();

	/**
	* Returns the error, if one was generated
	* @return string The error
	*/
	public function getError() : string;

	/**
	* Returns a params dump
	* @return string
	*/
	public function getDump() : string;

	/**
	* Selects a database
	* @param string $database The database name
	*/
	public function selectDb(string $database);

	/**
	* Escapes a value
	* @param string $value The value to escape
	* @return string The escaped value
	*/
	public function escape(string $value) : string;

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
	* @return array The data, or false on failure
	*/
	public function fetchArray($result) : bool|array;

	/**
	* Returns the next row, as an array, from a results set
	* @param resource $result The result
	* @return array The data, or false on failure
	*/
	public function fetchRow($result) : bool|array;

	/**
	* Returns the next row, as an object, from a results set
	* @param resource $result The result
	* @param string $class_name The class name
	* @return object The data, or false on failure
	*/
	public function fetchObject($result, string $class_name) : bool|object;
}
