<?php
/**
* The Memcache Driver Interface
* @package Mars
*/

namespace Mars\Memcache;

/**
* The Memcache Driver Interface
*/
interface DriverInterface
{
	/**
	* Connects to the memcache server
	* @param string $host The host
	* @param string $port The port
	*/
	public function connect(string $host, string $port);

	/**
	* Disconnects from the memcache server
	*/
	public function disconnect();

	/**
	* Adds a $key, if it doesn't already exists
	* @param string $key The key
	* @param mixed $value The value
	* @param int $expires The number of seconds after which the data will expire
	* @return bool
	*/
	public function add(string $key, $value, int $expires = 0) : bool;

	/**
	* Adds a $key. If a key with the same name exists, it's value is overwritten
	* @param string $key The key
	* @param mixed $value The value
	* @param int $expires The number of seconds after which the data will expire
	* @return bool
	*/
	public function set(string $key, $value, int $expires = 0) : bool;

	/**
	* Retrieves the value of $key
	* @param string $key The key
	* @return mixed The value of $key
	*/
	public function get(string $key);

	/**
	* Checks if a key exists/is set
	* @param string $key The key
	* @return bool True if the key exists
	*/
	public function exists(string $key) : bool;

	/**
	* Deletes $key
	* @param string $key The key to delete
	* @return bool
	*/
	public function delete(string $key) : bool;

	/**
	* Deletes all the keys
	* @return bool
	*/
	public function deleteAll() : bool;
}
