<?php
/**
* The Data Class
* @package Mars
*/

namespace Mars;

/**
* The Data Class.
* Represent data stored in the format name => value
*/
abstract class Data
{
	use AppTrait;

	/**
	* @var string $table The database table used to store the data
	*/
	protected string $table = '';

	/**
	* @var string $memcache_key The memcache key used to store the data
	*/
	protected string $memcache_key = '';

	/**
	* @var bool $serialize If true, by default, will serialize/unserialize the data
	*/
	protected bool $serialize = false;

	/**
	* @var bool $serialize_php_driver If true, will always use the php driver for serialization
	*/
	protected bool $serialize_php_driver = true;

	/**
	* @var array $scope The scope(s) from where to read the data
	*/
	protected array $scope = ['frontend'];

	/**
	* @var string $default_scope The scope where used to insert/update, by default
	*/
	protected string $default_scope = 'frontend';

	/**
	* @var array $data Saved data
	*/
	protected array $data = [];

	/**
	* Returns the table where the data is stored
	* @return string
	*/
	public function getTable() : string
	{
		if (!$this->table) {
			throw new \Exception('The $table property must be set to be able to use class Data');
		}

		return $this->table;
	}

	/**
	* Returns the memcache key used to store the data
	* @return string
	*/
	public function getKey() : string
	{
		if (!$this->memcache_key) {
			throw new \Exception('The $memcache_key property must be set to be able to use class Data');
		}

		return $this->memcache_key . '-' . implode('-', $this->scope);
	}

	/**
	* Loads the data, either from memcache, if available, or from the database
	* @return $this
	*/
	public function load()
	{
		$key = $this->getKey();

		$data = $this->app->memcache->get($key);
		if (!$data) {
			$data = $this->app->db->select($this->getTable(), 'name, value, scope', ['scope' => $this->scope]);

			$data = $this->processData($data);

			$this->app->memcache->set($key, $data);
		}

		$this->assign($data);

		return $this;
	}

	/**
	* Processes the data
	* @param array $data The data to process
	* @return array The data
	*/
	protected function processData(array $data) : array
	{
		if (count($this->scope) > 1) {
			$data_array = [];

			foreach ($this->scope as $scope) {
				$this->data[$scope] = $this->getData($data, $scope);

				$data_array = array_merge($data_array, $this->data[$scope]);
			}

			return $data_array;
		} else {
			return $this->getData($data);
		}
	}

	/***
	* Returns the data in the name => val format
	* @param array $data The data to return
	* @param string $scope The scope of the data
	* @return array The data
	*/
	protected function getData(array $data, string $scope = '') : array
	{
		$data_array = [];

		foreach ($data as $d) {
			if ($scope) {
				if ($d->scope != $scope) {
					continue;
				}
			}

			$data_array[$d->name] = $d->value;
		}

		return $data_array;
	}

	/**
	* Clears the data from memcache
	* @return $this
	*/
	protected function clearMemcache()
	{
		$this->app->memcache->delete($this->getKey());

		return $this;
	}

	/**
	* Returns data stored in the $this->data array
	* @param string $name The name of the entry to return
	* @param string $scope The scope from where to return the data
	* @return $this
	*/
	public function getFromScope(string $name, string $scope)
	{
		return $this->data[$scope][$name] ?? $this->$name;
	}

	/**
	* Determines if a entry exists and is not empty
	* @param string $name The name of the entry
	* @return bool
	*/
	public function is(string $name) : bool
	{
		if (empty($this->$name)) {
			return false;
		}

		return true;
	}

	/**
	* Returns the value of a data entry
	* @param string $name The name of the entry to return
	* @param bool $unserialize If true, will unserialize the returned result
	* @param mixed $default_value The default value to return if $unserialize is true
	* @return mixed
	*/
	public function get(string $name, ?bool $unserialize = null, $default_value = [])
	{
		$unserialize = $unserialize ?? $this->serialize;

		if ($unserialize) {
			return $this->app->serializer->unserialize($this->$name, $default_value, true, $this->serialize_php_driver);
		}

		return $this->$name;
	}

	/**
	* Adds an entry into the data table
	* @param string $name The name of the entry to insert
	* @param mixed $value The value
	* @param string The scope where the data will be set
	* @param bool $serialize If true, will serialize the value
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function add(string $name, $value, ?string $scope = null, ?bool $serialize = null, $default_value = '')
	{
		$scope = $scope ?? $this->default_scope;
		$serialize = $serialize ?? $this->serialize;

		if ($serialize) {
			if (!$value) {
				$value = $default_value;
			} else {
				$value = $this->app->serializer->serialize($value, true, $this->serialize_php_driver);
			}
		}

		$this->$name = $value;

		$insert_array = [
			'name' => $name,
			'value' => $value,
			'scope' => $scope
		];

		$this->app->db->insert($this->getTable(), $insert_array);

		$this->clearMemcache($scope);

		return $this;
	}

	/**
	* Updates a data entry. Will not create the entry, if it doesn't already exist
	* @param string $name The name of the value
	* @param mixed $value The new value
	* @param string The scope where the data will be updated
	* @param bool $serialize If true, will serialize the value
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function set(string $name, $value, ?string $scope = null, ?bool $serialize = null, $default_value = '')
	{
		$scope = $scope ?? $this->default_scope;
		$serialize = $serialize ?? $this->serialize;

		if ($serialize) {
			if (!$value) {
				$value = $default_value;
			} else {
				$value = $this->app->serializer->serialize($value, true, $this->serialize_php_driver);
			}
		}

		$this->$name = $value;
		$table = $this->getTable();

		$this->app->db->writeQuery("UPDATE {$table} SET value = :value WHERE name = :name AND scope = :scope", ['name' => $name, 'scope' => $scope, 'value' => $value]);

		$this->clearMemcache($scope);

		return $this;
	}

	/**
	* Deletes a data entry
	* @param string $name The name of the entry to delete
	* @param string The scope where the data will be set
	* @return $this
	*/
	public function delete(string $name, ?string $scope = null)
	{
		$scope = $scope ?? $this->default_scope;

		$table = $this->getTable();
		$this->app->db->writeQuery("DELETE FROM {$table} WHERE name = :name AND scope = :scope", ['name' => $name, 'scope' => $scope]);

		$this->clearMemcache($scope);

		return $this;
	}

	/**
	* Assigns the data to the object
	* @param array $data Array in the name=>value format
	* @return $this
	*/
	public function assign(array $data)
	{
		foreach ($data as $name => $value) {
			$this->$name = $value;
		}

		return $this;
	}
}
