<?php
/**
* The Items Class
* @package Mars
*/

namespace Mars;

use Mars\Alerts\Errors;

/**
* The Items Class
* Container of multiple items
* The classes extending Items must set the following static properties:
* protected static $table = ''; - The table from which the objects will be loaded
* protected static $id_name = ''; - The id column of the table from which the objects will be loaded
*
* The static::$status_name property must be set if the items must be published/unpublished etc
*/
abstract class Items extends Rows implements \ArrayAccess
{
	use AppTrait;

	/**
	* @var string $table The table from which the objects will be loaded
	*/
	//protected static $table = '';
	/**
	* @var string $id_name The id column of the table from which the objects will be loaded
	*/
	//protected static $id_name = '';

	/**
	* @var array $ids The ids of the currently loaded objects
	*/
	//public array $ids = [];

	/**
	* @var Errors $errors The errors object. Contains the generated errors, if any
	*/
	protected Errors $errors;

	/**
	* @var Db $db The database object. Alias for $this->app->db
	*/
	protected Db $db;

	/**
	* Builds the Item object
	*/
	public function __construct()
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
		$this->errors = new Errors;
	}

	/**
	* Unsets the app & db property when serializing
	*/
	public function __sleep()
	{
		$data = get_object_vars($this);

		unset($data['app']);
		unset($data['db']);

		return array_keys($data);
	}

	/**
	* Sets the app & db property when unserializing
	*/
	public function __wakeup()
	{
		$this->app = $this->getApp();
		$this->db = $this->app->db;
	}

	/**
	* Removes the app & db obj from the list of properties which are displayed by var_dump
	*/
	public function unsetApp()
	{
		unset($this->app);
		unset($this->db);
	}

	/**
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->errors->count()) {
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
		return $this->errors->get();
	}

	/**
	* Returns the first generated error, if any
	* @param bool $only_text If true, will return only the text rather than the object
	* @return mixed
	*/
	public function getFirstError(bool $only_text = false)
	{
		return $this->errors->getFirst($only_text);
	}

	/**
	* Returns the table name
	* @return string
	*/
	public function getTable() : string
	{
		return static::$table;
	}

	/**
	* Returns the id field name
	* @return string
	*/
	public function getIdName() : string
	{
		return static::$id_name;
	}

	/**
	* Returns an object
	* @param int $id The id of the object to return
	* @return object The object or null of not found
	*/
	public function get(int $id)
	{
		$obj = $this->find($id);
		if ($obj) {
			return $obj;
		}

		return $this->getObject($this->getRow($id));
	}

	/**
	* Returns the data from the database, based on id
	* @param int $id The id to return the data for
	* @return object The row, or false on failure
	*/
	public function getRow(int $id)
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();
		$class_name = $this->getClass();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call get_row()');
		}

		if ($class_name) {
			return new $class_name($id);
		}

		return $this->db->selectById($table, $id_name, $id);
	}

	/**
	* Returns the number of items from the table
	* @return int
	*/
	public function getCount() : int
	{
		return $this->db->count($this->getTable());
	}

	/**
	* @see \Mars\Entities::setData()
	* {@inheritDoc}
	*/
	public function setData(iterable $data, bool $convert = true)
	{
		parent::setData($data, $convert);

		$this->setIds($data);

		return $this;
	}

	/**
	* Loads the objects
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @param string $fields The fields to select
	* @return array The loaded data
	*/
	public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = '*') : array
	{
		$table = $this->getTable();
		if (!$table) {
			throw new \Exception('The $table static property must be set to be able to call load()');
		}

		$sql = $this->db->sql->select($fields)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		return $this->loadBySql($sql);
	}

	/**
	* Loads objects using a sql query
	* @param mixed $sql The sql query used to load the objects. Either a string or a Sql object
	* @return array The loaded data
	*/
	public function loadBySql($sql = null) : array
	{
		$this->data = [];
		$this->current = 0;
		$this->loaded = true;

		if (!$sql) {
			$sql = $this->db->sql;
		}

		$table = $this->getTable();
		$id_name = $this->getIdName();
		$class_name = $this->getClass();

		$q = $this->db->readQuery($sql);

		$i = 0;
		while ($data = $this->db->fetchArray($q)) {
			$this->data[$i] = new $class_name($data);
			$i++;
		}

		$this->db->free($q);

		$this->count = $i;

		$this->setIds($this->data);

		return $this->data;
	}

	/**
	* Alias for load, with the $fields param first
	* @param string $fields The fields to select
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array The loaded data
	*/
	public function loadFields(string $fields = '*', array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		return $this->load($where, $order_by, $order, $limit, $limit_offset, $fields);
	}

	/**
	* Loads a set of objects based on ids
	* @param array $ids The ids of the objects to load
	* @param string $fields The fields to load. By default, all fields are loaded (*)
	* @return array The loaded data
	*/
	public function loadIds(array $ids, string $fields = '*') : array
	{
		if (!$ids) {
			return [];
		}

		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call load_ids()');
		}

		$sql = $this->db->sql->select($fields)->from($table)->whereIn($id_name, $ids);

		return $this->loadBySql($sql);
	}

	/**
	* Alias for load
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @param string $fields The fields to select
	* @return array The data
	*/
	public function getAll(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = '*') : array
	{
		return $this->load($where, $order_by, $order, $limit, $limit_offset, $fields);
	}

	/**
	* Returns a list with the ids as keys and the titles as values
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array
	*/
	public function getList(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$id_name || !isset(static::$title_name)) {
			throw new \Exception('The $id_name and $title_name static properties must be set, to use get_list()');
		}

		return $this->db->selectList($table, $id_name, static::$title_name, $where, $order_by, $order, $limit, $limit_offset);
	}

	/**
	* Returns the object with id = $id from the list of loaded object
	* @param int $id The id of the object to return
	* @return object The object or null, if nothing is found
	*/
	public function find(int $id)
	{
		$obj = null;

		$id_name = $this->getIdName();
		foreach ($this->data as $data) {
			if ($data->$id_name == $id) {
				$obj = $data;
				break;
			}
		}

		return $obj;
	}

	/**
	* Returns the internal key in the data array of an object
	* @param int $id The id of the object to return the key for
	* @return int The key or null, if nothing is found
	*/
	protected function findKey(int $id) : ?int
	{
		$key = null;

		$id_name = $this->getIdName();
		foreach ($this->data as $dkey => $data) {
			if ($data->$id_name == $id) {
				$key = $dkey;
				break;
			}
		}

		return $key;
	}

	/**
	* @see \Mars\Entities::updateData()
	* {@inheritDoc}
	*/
	public function updateData(int $index, $data) : bool
	{
		if (!parent::updateData($index, $data)) {
			return false;
		}

		//update the id of the item
		$id_name = $this->getIdName();

		if (isset($data->$id_name)) {
			$this->ids[$index] = $data->$id_name;
		}

		return true;
	}

	/**
	* Updates multiple objects based on the properties of $data
	* @param mixed $data The data used to update the properties (array,object)
	* @param mixed $ids The IDs to update (int,array). If empty, all the current loaded objects will be updated
	* @return int The number of affected rows
	*/
	public function update($data, $ids = []) : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call update()');
		}

		$ids = $this->getIds($ids);
		if (!$ids) {
			return 0;
		}

		//unset the id and the ignore fields of the object
		if ($data instanceof Item) {
			$data = $data->getUpdatableData();
		}

		$data = App::toArray($data);
		if (!$data) {
			return 0;
		}

		return $this->db->updateByIds($table, $data, $id_name, $ids);
	}

	/**
	* Publishes the specified IDS. The static $status_name property must point to the status column
	* @param mixed $ids The IDs to publish (int,array). If empty, all the current loaded objects will be published
	* @return int The number of affected rows
	*/
	public function publish($ids = []) : int
	{
		return $this->updateStatus($ids, 1);
	}

	/**
	* Unpublishes the specified IDS. The The static $status_name property must point to the status column
	* @param mixed $ids The IDs to unpublish (int,array). If empty, all the current loaded objects will be unpublished
	* @return int The number of affected rows
	*/
	public function unpublish($ids = []) : int
	{
		return $this->updateStatus($ids, 0);
	}

	/**
	* Alias for publish
	* @param mixed $ids The IDs to enable (int,array). If empty, all the current loaded objects will be enabled
	* @return int The number of affected rows
	*/
	public function enable($ids = []) : int
	{
		return $this->publish($ids);
	}

	/**
	* Alias for unpublish
	* @param mixed $ids The IDs to disable (int,array). If empty, all the current loaded objects will be disable
	* @return int The number of affected rows
	*/
	public function disable($ids = []) : int
	{
		return $this->unpublish($ids);
	}

	/**
	* Updates the status of multiple ids
	* @param mixed $ids The IDs to update (int,array). If empty, all the current loaded objects will be updated
	* @param int $val The status value
	* @return int The number of affected rows
	*/
	protected function updateStatus($ids = [], int $val = 1) : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();
		$val = (int)$val;

		if (!$table || !$id_name || !isset(static::$status_name)) {
			throw new \Exception('The $table, $id_name and $status_name static properties must be set to be able to call update_status()');
		}

		$ids = $this->getIds($ids);
		if ($ids) {
			return 0;
		}

		return $this->db->updateByIds($table, [$status_name => $val], $id_name, $ids);
	}

	/**
	* Deletes the specified IDs.
	* @param mixed $ids The IDs to delete (int,array). If empty, all the current loaded objects will be deleted
	* @return int The number of affected rows
	*/
	public function delete($ids = []) : int
	{
		$table = $this->getTable();
		$id_name = $this->getIdName();

		if (!$table || !$id_name) {
			throw new \Exception('The $table and the $id_name static properties must be set to be able to call delete()');
		}

		$ids = $this->getIds($ids);
		if (!$ids) {
			return 0;
		}

		return $this->db->deleteByIds($table, $id_name, $ids);
	}

	/**
	* Returns $ids if it's not empty; $this->ids otherwise
	* @param mixed $ids The ids list
	* @return array
	*/
	public function getIds($ids = []) : array
	{
		if ($ids) {
			if (is_array($ids)) {
				return $ids;
			} else {
				return [$ids];
			}
		}

		return $this->ids;
	}

	/**
	* Sets the internal ids
	* @param iterable $data The data
	*/
	protected function setIds(iterable $data)
	{
		$this->ids = [];
		$id_name = $this->getIdName();

		foreach ($data as $i => $obj) {
			if (isset($obj->$id_name)) {
				$this->ids[$i] = $obj->$id_name;
			}
		}
	}

	/********** Array Access ********************/

	public function offsetExists($offset) : bool
	{
		return in_array($offset, $this->ids);
	}

	public function offsetGet($offset)
	{
		return $this->find($offset);
	}

	public function offsetSet($offset, $value)
	{
		$key = $this->findKey($offset);
		$id_key = array_search($offset, $this->ids);

		if ($key === null || $id_key === false) {
			return;
		}

		$id_name = $this->getIdName();

		$this->ids[$id_key] = $value->$id_name;
		$this->data[$key] = $value;
	}

	public function offsetUnset($offset)
	{
		$key = $this->findKey($offset);
		$id_key = array_search($offset, $this->ids);

		if ($key === null || $id_key === false) {
			return;
		}

		unset($this->ids[$id_key]);
		unset($this->data[$key]);

		$this->current = 0;
		$this->count--;
	}
}
