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
* protected static::$store Array listing the properties which must be separately stored as original data. This way, if any property changes, the user of the object will be able to tell, flip the values etc..
* protected static::$ignore Array listing the custom public properties(not found in the corresponding db table as columns) which should be ignored when inserting/updating
*/
abstract class Item extends Row
{
	use AppTrait;
	use ValidationTrait {
		validate as protected validateData;
	}

	/**
	* @var string $table The table from which the object will be loaded.
	*/
	protected static string $table = '';

	/**
	* @var string $id_name The id column of the table from which the object will be loaded
	*/
	protected static string $id_name = 'id';

	/**
	* @var string|array $fields The database fields to load
	*/
	protected $fields = '*';

	/**
	* @var array $errors  Contains the generated error codes, if any
	*/
	protected array $errors = [];

	/**
	* @var array $validation_rules Validation rules
	*/
	protected static array $validation_rules = [];

	/**
	* @var array $ignore Array listing the custom properties (not found in the corresponding db table) which should be ignored when inserting/updating
	*/
	protected static array $ignore = [];

	/**
	* @var array $store Array listing the properties which should be stored when the data is set
	*/
	protected static array $store = [];

	/**
	* @var array $stored Array containing the stored data. The stored data is the original properties of an object
	*/
	protected array $stored = [];

	/**
	* @var array $defaults_array Array containing defaults in the format name=>value
	*/
	protected static array $defaults_array = [];

	/**
	* @internal
	*/
	protected static array $defaults = [];

	/**
	* @internal
	*/
	protected static array $defaults_vals = [];

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
	* @param int|array|object|null $data If data is an int, will load the data with id = data from the database. If an array, will assume the array contains the object's data. If null, will load the defaults
	*/
	public function __construct($data = 0)
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
		$this->validator = $this->app->validator;

		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table) {
			throw new \Exception('The $table static property of class ' . get_class($this) . ' is not set!');
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
	* Returns the fields which will be loaded
	* @return array|string The fields
	*/
	public function getFields()
	{
		return $this->fields;
	}

	/**
	* Sets the fields to load
	* @param string|array $fields The fields to load
	* @return $this
	*/
	public function setFields($fields = '*')
	{
		$this->fields = $fields;

		return $this;
	}

	/**
	* Returns the validation rules
	* @return array The rules
	*/
	protected function getValidationRules() : array
	{
		return static::$validation_rules;
	}

	/**
	* Returns the array with the defaults properties
	*/
	protected function getDefaultsArray() : array
	{
		return static::$defaults_array;
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
	* @return object The row, or null on failure
	*/
	public function getRow(int $id) : ?object
	{
		return $this->db->selectById($this->getTable(), $id, $this->getIdName());
	}

	/**
	* Sets the object's properties
	* @param array|object $data The data
	* @param bool $store If true will store the properties defined in static::$store in $this->stored
	* @return $this
	*/
	public function setData($data, bool $store = true)
	{
		$data = App::toArray($data);

		foreach ($data as $name => $val) {
			if ($store && static::$store) {
				if (in_array($name, static::$store)) {
					$this->stored[$name] = $val;
				}
			}

			$this->$name = $val;
		}

		return $this;
	}

	/**
	* Loads an object
	* @param int|array|object|null $data If data is an int, will load the data with id = data from the database. If an array/object, will assume it contains the object's data
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
	* @param string|Sql $sql The sql code used to load the object
	* @return bool True if the object was loaded with data, false otherwise
	*/
	public function loadBySql($sql = '') : bool
	{
		$this->db->readQuery($sql);
		$data = $this->db->getRow();
		if ($data === null) {
			//don't load the defaults, if an empty row is returned
			$data = 0;
		}

		return $this->load($data);
	}

	/**
	* Determines if the loaded item is valid/has an id
	*/
	public function isValid() : bool
	{
		if ($this->getId()) {
			return true;
		}

		return false;
	}

	/**
	* Child classes can implement this method to validate the object when it's inserted/updated
	* @return bool True if the validation passed all tests, false otherwise
	*/
	protected function validate() : bool
	{
		return $this->validateData($this);
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
		if (!$this->validate()) {
			return 0;
		}

		if ($process) {
			$this->process();
		}

		$data = $this->getUpdatableData();

		$insert_id = $this->db->insert($this->getTable(), $data);

		if (!$keep_old_id) {
			$this->setId($insert_id);
		}

		return $insert_id;
	}

	/**
	* Updates the object
	* @return bool Returns true if the update operation was succesfull, false otherwise
	*/
	public function update(bool $process = true) : bool
	{
		if (!$this->validate()) {
			return false;
		}

		if ($process) {
			$this->process();
		}

		$data = $this->getUpdatableData();

		$this->db->updateById($this->getTable(), $data, $this->getId(), $this->getIdName());

		return true;
	}

	/**
	* Returns the 'updatable' data. Unsets the properties defined in static::$ignore, which shouldn't be stored when inserting/updating
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

		$unset_array = array_merge(static::$ignore, $unset_extra);

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
		return $this->db->deleteById($this->getTable(), $this->getId(), $this->getIdName());
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
		$id_name = $this->getIdName();

		//if no ignore columns array are specified, include the id field automatically
		if ($ignore_columns_array === null) {
			$ignore_columns_array = [$id_name];
		}

		$data = $this->db->bind($this->getTable(), $data, $ignore_columns_array, $ignore_value);

		$this->setData($data, false);

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
		$data = $this->db->bindList($this->getTable(), $data, $columns_array, $ignore_value);

		$this->setData($data, false);

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

		if (isset(static::$defaults_vals[$class_name])) {
			$default_values = static::$defaults_vals[$class_name];
		}

		if (empty(static::$defaults[$class_name])) {
			//read the columns from the database and apply the default int and char values
			static::$defaults[$class_name] = $this->getDefaults($default_int, $default_char);
			static::$defaults_vals[$class_name] = [$default_int, $default_char];

			$defaults = static::$defaults[$class_name];
		} else {
			///are the default_int/default_char params different than the stored default_vals?
			// If so,fill the data again as we can not use the stored defaults

			$default_values = static::$defaults_vals[$class_name];
			if ($default_values[0] === $default_int && $default_values[1] === $default_char) {
				$defaults = static::$defaults[$class_name];
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

		if (isset($this->stored[$property])) {
			if ($this->stored[$property] == $this->$property) {
				return false;
			}
		}

		return true;
	}

	/**
	* Loads the data of the current item as stored data
	* @return $this
	*/
	public function loadStored()
	{
		$id = $this->getId();
		if (!$id) {
			return;
		}

		$data = $this->db->selectById($this->getTable(), $id, '*', $this->getIdName(), true);

		return $this->setStored($data);
	}

	/**
	* Sets the stored data
	* @param array $data The data to store
	* @return $this
	*/
	public function setStored(array $data)
	{
		foreach (static::$store as $name) {
			$this->stored[$name] = $data[$name];
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
			return $this->stored;
		}

		if (!isset($this->stored[$property])) {
			return false;
		}

		return $this->stored[$property];
	}

	/**
	* Returns true if the specified property is stored
	* @param string $property The name of the property to check
	* @return bool
	*/
	public function isStored(string $property) : bool
	{
		return isset($this->stored[$property]);
	}

	/**
	* Flips the stored values and the properties value
	* @param string|array $properties The name of the stored properties to flip
	* @return $this
	*/
	public function flipStored($properties)
	{
		if (!is_array($properties)) {
			$properties = [$properties];
		}

		foreach ($properties as $property) {
			$val = $this->stored[$property];

			$this->stored[$property] = $this->$property;

			$this->$property = $val;
		}

		return $this;
	}
}
