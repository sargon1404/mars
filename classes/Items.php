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
* The static::$status_name property must be set if the items must be published/unpublished etc
*/
abstract class Items extends Rows implements \ArrayAccess
{
	use AppTrait;

	/**
	* @var string $table The table from which the objects will be loaded
	*/
	protected static string $table = '';
	/**
	* @var string $id_name The id column of the table from which the objects will be loaded
	*/
	protected static string $id_name = 'id';

	/**
	* @var string|array $fields The database fields to load
	*/
	protected string|array $fields = '*';

	/**
	* @var array $ids The ids of the currently loaded objects
	*/
	public array $ids = [];

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
	* Removes properties which shouldn't be displayed by var_dump/print_r
	*/
	public function __debugInfo()
	{
		$properties = get_object_vars($this);

		unset($properties['app']);
		unset($properties['db']);

		return $properties;
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
	public function setFields(string|array $fields = '*')
	{
		$this->fields = $fields;

		return $this;
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
		$class_name = $this->getClass();

		if ($class_name) {
			return new $class_name($id);
		}

		return $this->db->selectById($this->getTable(), $id, $this->fields, $this->getIdName());
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
	* {@inheritdoc}
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
	* @return array The loaded data
	*/
	public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		$sql = $this->db->sql->select($this->fields)->from($this->getTable())->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		return $this->loadBySql($sql);
	}

	/**
	* Loads objects using a sql query
	* @param string|Sql $sql The sql query used to load the objects. Either a string or a Sql object
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
	* Loads a set of objects based on ids
	* @param array $ids The ids of the objects to load
	* @return array The loaded data
	*/
	public function loadIds(array $ids) : array
	{
		if (!$ids) {
			return [];
		}

		$sql = $this->db->sql->select($this->fields)->from($this->getTable())->whereIn($this->getIdName(), $ids);

		return $this->loadBySql($sql);
	}

	/**
	* Loads a set of objects based on the based data. These keys might be specififed: where, order_by, order, limit, limit_offset
	* @param array $data The data used to build the sql object from
	* @return array The loaded data
	*/
	public function loadByData(array $data) : array
	{
		$where = $data['where'] ?? [];
		$order_by = $data['order_by'] ?? '';
		$order = $data['order'] ?? '';

		$sql = $this->db->sql->select($this->fields)->from($this->getTable());

		$sql->where($where)->orderBy($order_by, $order);
		//echo $sql->getSql();die;

		return $this->loadBySql($sql);
	}

	/**
	* Alias for load
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array The data
	*/
	public function getAll(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		return $this->load($where, $order_by, $order, $limit, $limit_offset);
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
		if (!isset(static::$title_name)) {
			throw new \Exception('The $title_name static property must be set, to use getList()');
		}

		return $this->db->selectList($this->getTable(), $this->getIdName(), static::$title_name, $where, $order_by, $order, $limit, $limit_offset);
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
	* {@inheritdoc}
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
	* @param array|object $data The data used to update the properties
	* @param int|array $ids The IDs to update (int,array). If empty, all the current loaded objects will be updated
	* @return int The number of affected rows
	*/
	public function update(array|object $data, int|array $ids = []) : int
	{
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

		return $this->db->updateByIds($this->getTable(), $data, $ids, $this->getIdName());
	}

	/**
	* Publishes the specified IDS. The static $status_name property must point to the status column
	* @param int|array $ids The IDs to publish. If empty, all the current loaded objects will be published
	* @return int The number of affected rows
	*/
	public function publish($ids = []) : int
	{
		return $this->updateStatus($ids, 1);
	}

	/**
	* Unpublishes the specified IDS. The The static $status_name property must point to the status column
	* @param int|array $ids The IDs to unpublish. If empty, all the current loaded objects will be unpublished
	* @return int The number of affected rows
	*/
	public function unpublish($ids = []) : int
	{
		return $this->updateStatus($ids, 0);
	}

	/**
	* Alias for publish
	* @param int|array $ids The IDs to enable. If empty, all the current loaded objects will be enabled
	* @return int The number of affected rows
	*/
	public function enable($ids = []) : int
	{
		return $this->publish($ids);
	}

	/**
	* Alias for unpublish
	* @param int|array $ids The IDs to disable. If empty, all the current loaded objects will be disable
	* @return int The number of affected rows
	*/
	public function disable($ids = []) : int
	{
		return $this->unpublish($ids);
	}

	/**
	* Updates the status of multiple ids
	* @param int|array $ids The IDs to update. If empty, all the current loaded objects will be updated
	* @param int $val The status value
	* @return int The number of affected rows
	*/
	protected function updateStatus($ids = [], int $val = 1) : int
	{
		if (!isset(static::$status_name)) {
			throw new \Exception('The $status_name static property must be set to be able to call updateStatus()');
		}

		$ids = $this->getIds($ids);
		if ($ids) {
			return 0;
		}

		return $this->db->updateByIds($this->getTable(), [$status_name => $val], $ids, $this->getIdName());
	}

	/**
	* Deletes the specified IDs.
	* @param int|array $ids The IDs to delete. If empty, all the current loaded objects will be deleted
	* @return int The number of affected rows
	*/
	public function delete($ids = []) : int
	{
		$ids = $this->getIds($ids);
		if (!$ids) {
			return 0;
		}

		return $this->db->deleteByIds($this->getTable(), $ids, $this->getIdName());
	}

	/**
	* Returns $ids if it's not empty; $this->ids otherwise
	* @param string|array $ids The ids list
	* @return array
	*/
	public function getIds(string|array $ids = []) : array
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
