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
	protected $table = '';

	/**
	* @var string $key The memcache key used to store the data
	*/
	protected $key = '';

	/**
	* @var string $scope The scope from where to read the data
	*/
	protected $scope = 'site';

	/**
	* @var array $data Saved data
	*/
	protected $data = [];

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
	* @param string The scope from where to read the data
	* @return string
	*/
	public function getKey(?string $scope = null) : string
	{
		if (!$this->key) {
			throw new \Exception('The $key property must be set to be able to use class Data');
		}

		return $this->key . '_' . $scope;
	}

	/**
	* Returns the scope
	* @param string The scope from where to read the data
	* @return string
	*/
	public function getScope(?string $scope = null) : string
	{
		if ($scope === null) {
			return $this->scope;
		}

		return $scope;
	}

	/**
	* Loads the data, either from memcache, if available, or from the database
	* @param string The scope from where to load the data
	* @param bool $save_data If true, will save the data in the $this->data array
	* @return $this
	*/
	public function load(?string $scope = 'site', bool $save_data = false)
	{
		$scope = $this->getScope($scope);

		$data = $this->app->memcache->get($this->getKey($scope));
		if (!$data) {
			$data = $this->app->db->selectList($this->getTable(), 'name', 'value', ['scope' => $scope], 'name');

			$this->app->memcache->set($this->getKey($scope), $data);
		}

		if ($save_data) {
			$this->data[$scope] = $data;
		}

		$this->assign($data);

		return $this;
	}

	/**
	* Clears the data from memcache
	* @param string The scope from where to read the data
	* @return $this
	*/
	protected function clearMemcache(string $scope)
	{
		$this->app->memcache->delete($this->getKey($scope));

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
	public function get(string $name, bool $unserialize = false, $default_value = [])
	{
		if ($unserialize) {
			return App::unserialize($this->$name, $default_value);
		}

		return $this->$name;
	}

	/**
	* Sets the value of a data entry
	* @param string $name The name of the  entry to set
	* @param mixed $value The new value
	* @param bool $serialize If true, will serialize the value
	* @param string The scope where the data will be set
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function set(string $name, $value, bool $serialize = false, ?string $scope = 'site', $default_value = '')
	{
		if (isset($this->$name)) {
			$this->update($name, $value, $serialize, $scope, $default_value);
		} else {
			$this->insert($name, $value, $serialize, $scope, $default_value);
		}

		return $this;
	}

	/**
	* Inserts an entry into the data table
	* @param string $name The name of the entry to insert
	* @param mixed $value The value
	* @param bool $serialize If true, will serialize the value
	* @param string The scope where the data will be set
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function insert(string $name, $value, bool $serialize = false, ?string $scope = 'site', $default_value = '')
	{
		$scope = $this->getScope($scope);

		if ($serialize) {
			$value = App::serialize($value, $default_value);
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
	* @param bool $serialize If true, will serialize the value
	* @param string The scope where the data will be updated
	* @param mixed $default_value The default value to return if $serialize is true
	* @return $this
	*/
	public function update(string $name, $value, bool $serialize = false, ?string $scope = 'site', $default_value = '')
	{
		$scope = $this->getScope($scope);

		if ($serialize) {
			$value = App::serialize($value, $default_value);
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
	public function delete(string $name, ?string $scope = 'site')
	{
		$scope = $this->getScope($scope);

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
