<?php
/**
* The Item Class
* @package Mars
*/

namespace Mars;

use Mars\Alerts\Errors;

/**
* The Item Class
* The classes extending Item must set these properties:
* protected static $table = '';
* protected static $id_field = '';
* protected static $name_field = '';
*/
abstract class Item extends Entity
{
	use AppTrait;
	use ValidationTrait {
		validate as protected validateData;
	}

	/**
	* @var Errors $errors The generated errors, if any
	*/
	public Errors $errors;

	/**
	* @var string $table The table from which the object will be loaded.
	*/
	protected static string $table = '';

	/**
	* @var string $id_field The id column of the table from which the object will be loaded
	*/
	protected static string $id_field = 'id';

	/**
	* @var string $name_field The name column of the table from which the object will be loaded
	*/
	protected static string $name_field = 'name';

	/**
	* @var string|array $fields The database fields to load
	*/
	protected string|array $fields = '*';

	/**
	* @var array $ignore Array listing the custom properties (not found in the corresponding db table) which should be ignored when inserting/updating
	*/
	protected static array $ignore = [];

	/**
	* @var array $original Array containing the original properties
	*/
	protected array $original = [];

	/**
	* @var bool $original_store If false, no original data will be set
	*/
	protected static bool $original_store = true;

	/**
	* @var array $original_list If specified, only the properties in the list will be stored as original data
	*/
	protected static array $original_list = [];

	/**
	* @var array $defaults The default properties
	*/
	protected static array $default = [];

	/**
	* @var array $defaults_override The list of overrides, when generating the default properties
	*/
	protected static array $default_override = [];

	/**
	* @var array $defaults_override The properties not to include on the list of default properties
	*/
	protected static array $default_ignore = [];

	/**
	* @var int $default_int The default value for int/float properties
	*/
	protected static int $default_int = 0;

	/**
	* @var string $default_char The default value for string properties
	*/
	protected static string $default_char = '';

	/**
	* @var array $validation_rules Validation rules
	*/
	protected static array $validation_rules = [];

	/**
	* @var array $validation_rules_to_skip Validation rules to skip when validating, if any
	*/
	protected static array $validation_rules_to_skip = [];

	/**
	* @var array $validation_error_strings Custom error strings
	*/
	protected static array $validation_error_strings = [];

	/**
	* @var Db $db The database object. Alias for $this->app->db
	*/
	protected Db $db;

	/**
	* Builds an item
	* @param mixed $data If data is an int, will load the data with id = data from the database. If string will load the data with name = data. If an array or oject, will assume the array contains the object's data. If null, will load the defaults
	* @param App $app The app object
	*/
	public function __construct($data = 0, App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->db = $this->app->db;
		$this->errors = new Errors($this->app);

		$table = $this->getTable();
		$id_field = $this->getIdField();

		if (!$table) {
			throw new \Exception('The $table static property of class ' . get_class($this) . ' is not set!');
		}

		$this->setId(0);
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
	public function getIdField() : string
	{
		return static::$id_field;
	}

	/**
	* Returns the name field
	* @return string The name of the name field
	*/
	public function getNameField() : string
	{
		return static::$name_field;
	}

	/**
	* Returns the object's id
	* @return int
	*/
	public function getId() : int
	{
		$id_field = $this->getIdField();

		return $this->$id_field;
	}

	/**
	* Sets the id
	* @return static
	*/
	public function setId(int $id) : static
	{
		$id_field = $this->getIdField();

		$this->$id_field = $id;

		return $this;
	}

	/**
	* Returns the fields used to load/select the data
	* @return array|string The fields
	*/
	public function getFields()
	{
		return $this->fields;
	}

	/**
	* Sets the fields to load
	* @param string|array $fields The fields to load
	* @return static
	*/
	public function setFields(string|array $fields = '*') : static
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
	* Returns the validation rules to skip
	* @return array The rules to skip
	*/
	protected function getValidationRulesToSkip() : array
	{
		return static::$validation_rules_to_skip;
	}

	/**
	* Returns the validation error strings
	* @return array The error strings
	*/
	protected function getValidationErrorStrings() : array
	{
		return static::$validation_error_strings;
	}

	/**
	* Returns the array with the default properties
	* @return array
	*/
	protected function getDefaultsArray() : array
	{
		return static::$defaults_array;
	}

	/**
	* Determines if the object's id is set
	* @return bool
	*/
	public function is() : bool
	{
		return (bool)$this->getId();
	}

	/**
	* @see \Mars\Entity::set()
	* {@inheritdoc}
	*/
	public function set(array|object $data) : static
	{
		$this->original = $this->getOriginalData(App::array($data));

		return parent::set($data);
	}


	/**
	* Returns the row from the database, based on id
	* @param int $id The id to return the data for
	* @return object The row, or null on failure
	*/
	public function getRow(int $id) : ?object
	{
		return $this->db->selectById($this->getTable(), $id, $this->getIdField());
	}

	/**
	* Returns the row from the database, based on name
	* @param string $name The name to return the data for
	* @return object The row, or null on failure
	*/
	public function getRowByName(string $name) : ?object
	{
		return $this->db->selectOne($this->getTable(), [$this->getNameField() => $name]);
	}

	/**
	* Loads an object
	* @param mixed $data If data is an int, will load the data with id = data from the database. If string will load the data with name = data. If an array or oject, will assume the array contains the object's data. If null, will load the defaults
	* @return static
	*/
	public function load(mixed $data) : static
	{
		if ($data === null) {
			//load defaults
			$data = $this->getDefaultData();
		}

		if (!$data) {
			return $this;
		}

		if (is_numeric($data)) {
			//load the data from the db, by id
			$data = $this->getRow($data);
		} elseif (is_string($data)) {
			//load the data from the db, by name
			$data = $this->getRowByName($data);
		}

		if (!$data) {
			return $this;
		}

		//set the data from the array/object
		$this->set($data);

		$this->prepare();

		return $this;
	}

	/**
	* Loads the object by id
	* @param int $id The id
	* @return static
	*/
	public function loadById(int $id) : static
	{
		return $this->load($id);
	}

	/**
	* Loads the object by name
	* @param string $name The name
	* @return static
	*/
	public function loadByName(string $name) : static
	{
		return $this->load($this->getRowByName($name));
	}

	/**
	* Loads an objects using a sql query
	* @param string|Sql $sql The sql code used to load the object
	* @return static
	*/
	public function loadBySql(string|Sql $sql) : static
	{
		$data = $this->db->readQuery($sql)->fetchArray();
		if ($data === null) {
			//don't load the defaults, if an empty row is returned
			$data = 0;
		}

		return $this->load($data);
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
	* Child classes can implement this method to process the object when it's loaded
	*/
	protected function prepare()
	{
	}

	/**
	* Child classes can implement this method to process the object when it's inserted/updated
	*/
	protected function process()
	{
	}

	/**
	* Returns the default data
	* @return array
	*/
	protected function getDefaultData() : array
	{
		$class_name = static::class;

		if (!isset(static::$default[$class_name])) {
			static::$default[$class_name] = $this->db->fill($this->getTable(), static::$default_override, static::$default_ignore, static::$default_int, static::$default_char, true);
		}

		return static::$default[$class_name];
	}

	/**
	* Inserts the object in the database
	* @return int The id of the newly inserted item
	*/
	public function insert() : int
	{
		$this->process();

		if (!$this->validate()) {
			return 0;
		}

		$id = $this->db->insert($this->getTable(), $this->getData());

		$this->setId($id);

		return $id;
	}

	/**
	* Updates the object
	* @return bool Returns true if the update operation was succesfull
	*/
	public function update() : bool
	{
		$this->process();

		if (!$this->validate()) {
			return false;
		}

		$this->db->updateById($this->getTable(), $this->getData(), $this->getId(), $this->getIdField());

		return true;
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
	* Returns the data which will be used by an insert/update operation
	* Unsets the properties defined in static::$ignore, which shouldn't be stored when inserting/updating
	* @return array
	*/
	protected function getData() : array
	{
		$data = $this->get();

		//unset the id field
		$id_field = $this->getIdField();
		unset($data[$id_field]);

		//unset the errors property
		unset($data['errors']);

		if (static::$ignore) {
			$data = filterProperties($data, static::$ignore);
		}

		return $data;
	}

	/**
	* Deletes the object
	* @return int The number of affected rows
	*/
	public function delete() : int
	{
		return $this->db->deleteById($this->getTable(), $this->getId(), $this->getIdField());
	}

	/**
	* Binds the data from $data to the object's properties
	* @param array $data The data to bind
	* @param array $ignore_columns Array listing the columns from $table which shouldn't be included in the returned result
	* @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
	* @return $this
	*/
	public function bind(array $data, ?array $ignore_columns = null, ?string $ignore_value = null)
	{
		$id_field = $this->getIdField();

		//if no ignore columns array are specified, include the id field automatically
		if ($ignore_columns === null) {
			$ignore_columns = [$id_field];
		}

		$data = $this->db->bind($this->getTable(), $data, $ignore_columns, $ignore_value);

		return $this->set($data);

		return $this;
	}

	/**
	* Binds the data from $data to the object's properties
	* @param array $data The data to bind
	* @param array $columns_array Array with the columns from $data which should be used
	* @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
	* @return $this
	*/
	public function bindList(array $data, array $allowed_columns, ?string $ignore_value = null)
	{
		$data = $this->db->bindList($this->getTable(), $data, $allowed_columns, $ignore_value);

		return $this->set($data);
	}

	/**
	* Returns true if the specified property is stored as original data
	* @param string $property The name of the property
	* @return bool
	*/
	public function isOriginal(string $property) : bool
	{
		return isset($this->original[$property]);
	}

	/**
	* Returns the original data
	* @param string $property If specified, only this property will be returned
	*/
	public function getOriginal(string $property = '')
	{
		if ($property) {
			return $this->original[$property] ?? null;
		}

		return $this->original;
	}

	/**
	* Returns the original properties to be stored
	* @param array $data The data
	* @return array
	*/
	protected function getOriginalData(array $data) : array
	{
		if (!static::$original_store) {
			return [];
		}

		if (static::$original_list) {
			return App::getProperties($data, static::$original_list);
		}

		return $data;
	}

	/**
	* Determines if a property is updatable.
	* The property is considered updatable if it's set and doesn't equal the original stored value (assuming the original value exists)
	* @param string $property The name of the property
	* @return bool
	*/
	public function canUpdate(string $property) : bool
	{
		if (!isset($this->original[$property])) {
			return true;
		}

		return !($this->original[$property] == $this->$property);
	}

	/**
	* Flips the current value with the original value
	* @param string|array $properties The name of the properties to flip
	* @return $this
	*/
	public function flip(string|array $properties) : static
	{
		$properties = (array)$properties;

		foreach ($properties as $property) {
			if (!isset($this->original[$property])) {
				continue;
			}

			$val = $this->original[$property];

			$this->original[$property] = $this->$property;

			$this->$property = $val;
		}

		return $this;
	}
}
