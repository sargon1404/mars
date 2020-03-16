<?php
/**
* The Item Class
* @package Mars
*/

namespace Mars;

/**
* The Item Class
* ORM functionality for objects built from database data
* The classes extending Item must set the following static properties:
* protected static $table = ''; - The table from which the object will be loaded
* protected static $id_name = ''; - The id column of the table from which the object will be loaded
*
* protected static::$_store Array listing the properties which must be separately stored as original data. This way, if any property changes, the user of the object will be able to tell, flip the values etc..
* protected static::$_ignore Array listing the custom public properties(not found in the corresponding db table as columns) which should be ignored when inserting/updating
*/
abstract class Item extends Row
{
	use AppTrait;

	/**
	* @var string $table The table from which the object will be loaded.
	*/
	protected static string $table = '';
	/**
	* @var string $id_name The id column of the table from which the object will be loaded
	*/
	protected static string $id_name = '';

	/**
	* @var array $errors  Contains the generated error codes, if any
	*/
	protected array $errors = [];

	/**
	* @var array $_rules Validation rules
	*/
	protected static array $_rules = [];

	/**
	* @var array $_skip_rules Validation rules to skip when validating, if any
	*/
	protected array $_skip_rules = [];

	/**
	* @var array $_ignore Array listing the custom properties (not found in the corresponding db table) which should be ignored when inserting/updating
	*/
	protected static array $_ignore = [];

	/**
	* @var array $_store Array listing the properties which should be stored when the data is set
	*/
	protected static array $_store = [];

	/**
	* @var array $_store Array containing the stored data. The stored data is the original properties of an object
	*/
	protected array $_stored = [];

	/**
	* @var array $_defaults_array Array containing defaults in the format name=>value
	*/
	protected static array $_defaults_array = [];

	/**
	* @internal
	*/
	protected static array $_defaults = [];

	/**
	* @internal
	*/
	protected static array $_defaults_vals = [];

	/**
	* @var Db $db The database object. Alias for $this->app->db
	*/
	protected Db $db;
	/**
	* @var Validator $validator The validator object. Alias for $this->app->validator
	*/
	protected Validator $validator;

	/**
	* Builds an item
	* @param mixed $data If data is an int, will load the data with id = data from the database. If an array, will assume the array contains the object's data. If null, will load the defaults
	*/
	public function __construct($data = null)
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
		$this->validator = $this->app->validator;

		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and $id_name static properties of class ' . get_class($this) . ' are not set!');
		}

		if (empty($this->$id_name)) {
			$this->$id_name = 0;
		}

		$this->load($data);
	}

	/**
	* Unsets the app & db property when serializing
	*/
	public function __sleep()
	{
		$data = get_object_vars($this);

		unset($data['app']);
		unset($data['db']);
		unset($data['validator']);

		return array_keys($data);
	}

	/**
	* Sets the app & db property when unserializing
	*/
	public function __wakeup()
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
		$this->validator = $this->app->validator;
	}

	/**
	* Removes properties which shouldn't be displayed by var_dump/print_r
	*/
	public function __debugInfo()
	{
		$properties = get_object_vars($this);

		unset($properties['app']);
		unset($properties['db']);
		unset($properties['validator']);

		return $properties;
	}

	/**
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->errors) {
			return false;
		}

		return true;
	}

	/**
	* Returns the generated errors, if any
	* @return array
	*/
	public function getErrors() : array
	{
		return $this->errors;
	}

	/**
	* Returns the first generated error, if any
	* @return mixed
	*/
	public function getFirstError()
	{
		return reset($this->errors);
	}

	/**
	* Returns the table name
	* @return string The table name
	*/
	public function getTable() : string
	{
		return static::$table;
	}

	/**
	* Returns the id field name
	* @return string The name of the id field
	*/
	public function getIdName() : string
	{
		return static::$id_name;
	}

	/**
	* Returns the validation rules
	* @return array The rules
	*/
	protected function getRules() : array
	{
		return static::$_rules;
	}

	/**
	* The same as skipRules
	* @param string $rule The rule to skip
	* @return $this
	*/
	public function skipRule(string $rule)
	{
		return $this->skipRules($rule);
	}

	/**
	* Skips rules from validation
	* @param array|string $skip_rules Rules which will be skipped at validation
	* @return $this
	*/
	public function skipRules($skip_rules)
	{
		$skip_rules = App::getArray($skip_rules);
		foreach ($skip_rules as $rule) {
			if (!in_array($rule, $this->_skip_rules)) {
				$this->_skip_rules[] = $rule;
			}
		}

		return $this;
	}

	/**
	* Returns the array with the defaults properties
	*/
	protected function getDefaultsArray() : array
	{
		return static::$_defaults_array;
	}

	/**
	* Returns the ID of the object
	* @return int The object's id
	*/
	public function getId() : int
	{
		$id_name = $this->getIdName();
		if (!$id_name) {
			return 0;
		}

		if (!isset($this->$id_name)) {
			return 0;
		}

		return (int)$this->$id_name;
	}

	/**
	* Sets the ID of the object
	* @param int $id The object's id
	* @return $this
	*/
	public function setId(int $id)
	{
		$id_name = $this->getIdName();
		if (!$id_name) {
			return $this;
		}

		if (!isset($this->$id_name)) {
			return $this;
		}

		$this->$id_name = (int)$id;

		return $this;
	}

	/**
	* Determines if the object's id is > 0
	* @return bool
	*/
	public function is() : bool
	{
		$id_name = $this->getIdName();

		if (!empty($this->$id_name)) {
			return true;
		}

		return false;
	}

	/**
	* Returns the row from the database, based on id
	* @param int $id The id to return the data for
	* @return mixed The row, or false on failure
	*/
	public function getRow(int $id) : ?object
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call get_row()');
		}

		return $this->db->selectById($table, $id_name, $id);
	}

	/**
	* Sets the object's properties
	* @param mixed $data The data (array,object)
	* @param bool $store If true will store the properties defined in static::$_store in $this->_stored
	* @return $this
	*/
	public function setData($data, bool $store = true)
	{
		$data = App::toArray($data);

		foreach ($data as $name => $val) {
			if ($store && static::$_store) {
				if (in_array($name, static::$_store)) {
					$this->_stored[$name] = $val;
				}
			}

			$this->$name = $val;
		}

		return $this;
	}

	/**
	* Loads an object
	* @param mixed $data If data is an int, will load the data with id = data from the database. If an array/object, will assume it contains the object's data
	* @return bool True if the object was loaded with data, false otherwise
	*/
	public function load($data) : bool
	{
		if ($data === null) {
			//load defaults
			$data = $this->getDefaultData();
		}

		if (!$data) {
			return false;
		}

		if (is_numeric($data)) {
			//load the data from the database
			$data = $this->getRow($data);
		}

		$this->setData($data);

		$this->prepare();

		return true;
	}

	/**
	* Loads the object by id
	* @param int $id The id
	* @return bool True if the object was loaded with data, false otherwise
	*/
	public function loadById(int $id) : bool
	{
		return $this->load($id);
	}

	/**
	* Loads an objects using a sql query
	* @param mixed $sql The sql code used to load the object. Either string or a Sql object
	* @return bool True if the object was loaded with data, false otherwise
	*/
	public function loadBySql($sql = '') : bool
	{
		$this->db->readQuery($sql);
		$data = $this->db->getRow();

		return $this->load($data);
	}

	/**
	* Child classes can implement this method to validate the object when it's inserted/updated
	* @return bool True if the validation passed all tests, false otherwise
	*/
	protected function validate() : bool
	{
		$rules = $this->getRules();
		if (!$rules) {
			return true;
		}

		if (!$this->validator->validate($this, $rules, $this->getTable(), $this->getIdName(), $this->_skip_rules)) {
			$this->errors = $this->validator->getErrors();

			return false;
		}

		return true;
	}

	/**
	* Child classes can implement this method to process the object when it's inserted/updated
	*/
	protected function process()
	{
	}

	/**
	* Inserts the object in the database
	* @param bool $process If false, won't call process() before inserting
	* @param bool $keep_old_id If true, the object won't have the value of the new insert id, after the insert operation
	* @return int The id of the newly inserted item
	*/
	public function insert(bool $process = true, bool $keep_old_id = false) : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call insert()');
		}

		if (!$this->validate()) {
			return 0;
		}

		if ($process) {
			$this->process();
		}

		$data = $this->getUpdatableData();

		$insert_id = $this->db->insert($table, $data);

		if (!$keep_old_id) {
			$this->setId($insert_id);
		}

		return $insert_id;
	}

	/**
	* Updates the object
	* @return int The number of affected rows
	*/
	public function update(bool $process = true) : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();
		$id = $this->getId();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call update()');
		}

		if (!$this->validate()) {
			return 0;
		}

		if ($process) {
			$this->process();
		}

		$data = $this->getUpdatableData();

		return $this->db->updateById($table, $data, $id_name, $id);
	}

	/**
	* Returns the 'updatable' data. Unsets the properties defined in static::$_ignore, which shouldn't be stored when inserting/updating
	* @param bool $unset_id If true will unset the ID field
	* @param array $unset_extra Extra data to unset, if any
	* @return array
	*/
	public function getUpdatableData(bool $unset_id = true, array $unset_extra = []) : array
	{
		$data = $this->getData();

		if ($unset_id) {
			$id_name = $this->getIdName();
			if ($id_name) {
				$unset_extra[] = $id_name;
			}
		}

		$unset_array = array_merge(static::$_ignore, $unset_extra);

		if ($unset_array) {
			foreach ($unset_array as $name) {
				if (isset($data[$name])) {
					unset($data[$name]);
				}
			}
		}

		return $data;
	}

	/**
	* Saves the data to the db. Calls insert if the id of the object is 0, update otherwise
	* @return int The id of the newly inserted item
	*/
	public function save() : int
	{
		$id = $this->getId();
		if ($id) {
			$this->update();

			return $id;
		} else {
			return $this->insert();
		}
	}

	/**
	* Deletes the object
	* @return int The number of affected rows
	*/
	public function delete() : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();
		$id = $this->getId();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call delete()');
		}

		return $this->db->deleteById($table, $id_name, $id);
	}

	/**
	* Binds the data from $data to the object's properties
	* @param array $data The data to bind
	* @param array $ignore_columns_array Array with the columns from $data which should be ignored.
	* @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
	* @return $this
	*/
	public function bind(array $data, ?array $ignore_columns_array = null, ?string $ignore_value = null)
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table) {
			throw new \Exception('The $table static property must be set to be able to call bind()');
		}

		//if no ignore columns array are specified, include the id field automatically
		if ($ignore_columns_array === null) {
			if ($id_name) {
				$ignore_columns_array = [$id_name];
			} else {
				$ignore_columns_array = [];
			}
		}

		if ($table) {
			$data = $this->db->bind($table, $data, $ignore_columns_array, $ignore_value);
		}

		$this->setData($data);

		return $this;
	}

	/**
	* Binds the data from $data to the object's properties
	* @param array $data The data to bind
	* @param array $columns_array Array with the columns from $data which should be used
	* @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
	* @return $this
	*/
	public function bindList(array $data, array $columns_array, ?string $ignore_value = null)
	{
		$table = $this->getTable();

		if (!$table) {
			throw new \Exception('The $table static property must be set to be able to call bind_list()');
		}

		$data = $this->db->bindList($table, $data, $columns_array, $ignore_value);

		$this->setData($data);

		return $this;
	}

	/**
	* Fills the object's properties
	* @param array $data Array with data in the format name=>value. If empty the default values are loaded
	* @param int $default_int The value to use for int values if the default values are loaded
	* @param string $default_char The value to use for string values if the default values are loaded
	* @return $this
	*/
	public function fill(array $data = [], int $default_int = 0, string $default_char = '')
	{
		if (!$data) {
			$data = $this->getDataDefault();
		}

		foreach ($data as $name => $val) {
			if (isset($this->$name)) {
				$this->$name = $val;
			}
		}

		return $this;
	}

	/**
	* Returns the default data
	* @param int $default_int The default value of the int properties
	* @param int $default_char The default value of the string properties
	* @return array
	*/
	public function getDefaultData(int $default_int = 0, string $default_char = '') : array
	{
		$defaults = [];
		$default_values = [$default_int, $default_char];
		$class_name = get_class($this);

		if (isset(static::$_defaults_vals[$class_name])) {
			$default_values = static::$_defaults_vals[$class_name];
		}

		if (empty(static::$_defaults[$class_name])) {
			//read the columns from the database and apply the default int and char values
			static::$_defaults[$class_name] = $this->getDefaults($default_int, $default_char);
			static::$_defaults_vals[$class_name] = [$default_int, $default_char];

			$defaults = static::$_defaults[$class_name];
		} else {
			///are the default_int/default_char params different than the stored default_vals?
			// If so,fill the data again as we can not use the stored defaults

			$default_values = static::$_defaults_vals[$class_name];
			if ($default_values[0] === $default_int && $default_values[1] === $default_char) {
				$defaults = static::$_defaults[$class_name];
			} else {
				$defaults = $this->getDefaults($default_int, $default_char);
			}
		}

		$defaults_array = $this->getDefaultsArray();
		if ($defaults_array) {
			$defaults = $defaults_array + $defaults;
		}

		return $defaults;
	}

	/**
	* Returns the default data from the database
	* @param int $default_int The default value of the int properties
	* @param int $default_char The default value of the string properties
	* @return array
	*/
	public function getDefaults(int $default_int = 0, string $default_char = '') : array
	{
		return $this->db->fill($this->getTable(), [], $default_int, $default_char, true);
	}

	/**
	* Fill the object with the default values
	* @param array $override_array Array with the properties to override, if any
	* @param int $default_int The default value of the int properties
	* @param int $default_char The default value of the string properties
	* @return $this
	*/
	public function loadDefaults(array $override_array = [], int $default_int = 0, string $default_char = '')
	{
		$defaults = $this->getDefaultData();

		if ($override_array) {
			$this->setData($override_array + $defaults);
		} else {
			$this->setData($defaults);
		}

		$this->prepare();

		return $this;
	}

	/**
	* Determines if a property is updatable.
	* The property is considered updatable if it's set and doesn't equal the stored value (assuming a stored value exists)
	* @param string $property The name of the property
	* @return bool
	*/
	public function canUpdate(string $property) : bool
	{
		if (!isset($this->$property)) {
			return false;
		}

		if (isset($this->_stored[$property])) {
			if ($this->_stored[$property] == $this->$property) {
				return false;
			}
		}

		return true;
	}

	/**
	* Sets the stored data
	* @param array $data The data to store
	* @return $this
	*/
	public function setStored(array $data)
	{
		foreach (static::$_store as $name) {
			$this->_stored[$name] = $data[$name];
		}

		return $this;
	}

	/**
	* Returns the stored data. If property is specified will only return that property
	* @param string $property The name of the stored property to return
	* @return mixed The stored data; false if the property isn't stored
	*/
	public function getStored(string $property = '')
	{
		if (!$property) {
			return $this->_stored;
		}

		if (!isset($this->_stored[$property])) {
			return false;
		}

		return $this->_stored[$property];
	}

	/**
	* Returns true if the specified property is stored
	* @param string $property The name of the property to check
	* @return bool
	*/
	public function isStored(string $property) : bool
	{
		return isset($this->_stored[$property]);
	}

	/**
	* Flips the stored values and the properties value
	* @param mixed $properties The name of the stored properties to flip (string,array)
	* @return $this
	*/
	public function flipStored($properties)
	{
		if (!is_array($properties)) {
			$properties = [$properties];
		}

		foreach ($properties as $property) {
			$val = $this->_stored[$property];

			$this->_stored[$property] = $this->$property;

			$this->$property = $val;
		}

		return $this;
	}

}
