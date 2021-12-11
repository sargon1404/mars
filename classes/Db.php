<?php
/**
* The Database Class
* @package Mars
*/

namespace Mars;

use Mars\Db\DriverInterface;

/**
* The Database Class
* Handles the database interactions
*/
class Db
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var array $queries The list of executed queries, if debug is on
	*/
	public array $queries = [];

	/**
	* @var float $queries_time The total time needed to execute the queries, if debug is on
	*/
	public float $queries_time = 0;

	/**
	* @var bool $debug If true, the db will be run in debug mode
	*/
	public bool $debug = false;
	
	/**
	* @var string $charset The charset
	*/
	protected string $charset = '';
	
	/**
	* @var bool $persistent If true, the db connection will be persistent
	*/
	protected bool $persistent = false;
	
	/**
	* @var bool $connected Set to true, if the connection to the db server has been made
	*/
	protected bool $connected = false;
	
	/**
	* @var bool $use_same_handle True if the same handle is used for both read & write queries
	*/
	protected bool $use_same_handle = true;
	
	/**
	* @var bool $use_multi If true, will use multiple databases for read & write queries
	*/
	protected bool $use_multi = false;
	
	/**
	* @var string $read_hostname The hostname to connect to for read queries
	*/
	protected string $read_hostname = '';

	/**
	* @var string $read_port The port to connect to for read queries
	*/
	protected string $read_port = '';

	/**
	* @var string $read_username The username used to connect to the read server
	*/
	protected string $read_username = '';

	/**
	* @var string $read_password The password used to connect to the read server
	*/
	protected string $read_password = '';

	/**
	* @var string $read_database The database to connect to the read server
	*/
	protected string $read_database = '';
	
	/**
	* @var bool $read_pesistent If true, the read db connection will be persistent 
	*/
	protected bool $read_persistent = false;

	/**
	* @var string $write_hostname The hostname to connect to for write queries
	*/
	protected string $write_hostname = '';

	/**
	* @var string $write_port The port to connect to for write queries
	*/
	protected string $write_port = '';

	/**
	* @var string $write_username The username used to connect to the write server
	*/
	protected string $write_username = '';

	/**
	* @var string $write_password The password used to connect to the write server
	*/
	protected string $write_password = '';

	/**
	* @var string $write_database The database to connect to the write server
	*/
	protected string $write_database = '';
	
	/**
	* @var bool $write_pesistent If true, the write db connection will be persistent 
	*/
	protected bool $write_persistent = false;

	/**
	* @var DriverInterface $read_handle The handle for the read queries
	*/
	protected DriverInterface $read_handle;

	/**
	* @var DriverInterface $write_handle The handle for the write queries
	*/
	protected DriverInterface $write_handle;
	
	/**
	* @var string $driver The used driver
	*/
	protected string $driver = '';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'db';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Db\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'pdo' => '\Mars\Db\Pdo'
	];

	/**
	* Constructs the db object
	* @param App $app The app object
	* @param string $driver The database driver. Currently supported: pdo
	* @param string|array $hostname The db hostname. If array, the first entry will be used as the master hostname [both read&write], the other as slave hostnames [only read]
	* @param string|array $port The db port
	* @param string|array $username The db username
	* @param string|array $password The db password
	* @param string|array $database The database to use
	* @param bool|array $persistent If true, the database connection will be persistent
	* @param string $charset The database charset
	* @param bool $debug If true, will run in debug mode
	*/
	public function __construct(App $app, string $driver = '', string|array $hostname = '', string|array $port = '3306', string|array $username = '', string|array $password = '', string|array $database = '', bool|array $persistent = false, string $charset = 'utf8mb4', bool $debug = false)
	{
		$this->app = $app;

		if (!$driver) {
			$driver = $this->app->config->db_driver;
			$hostname = $this->app->config->db_hostname;
			$port = $this->app->config->db_port;
			$username = $this->app->config->db_username;
			$password = $this->app->config->db_password;
			$database = $this->app->config->db_database;
			$persistent = $this->app->config->db_persistent;
			$charset = $this->app->config->db_charset;
			$debug = $this->app->config->db_debug;
		}

		$this->driver = $driver;
		$this->persistent = $persistent;
		$this->charset= $charset;
		$this->debug = $debug;
		$this->use_multi = is_array($hostname) ? true : false;

		$this->setReadHost($hostname, $port, $username, $password, $database, $persistent);
		$this->setWriteHost($hostname, $port, $username, $password, $database, $persistent);
	}

	/**
	* Destroys the database object. Disconnects from the database server
	*/
	public function __destruct()
	{
		$this->disconnect();
	}
	
	/**
	* Returns the key of the server used for read queries
	* @param string|array $hostname The db hostname
	*/
	protected function getReadKey(string|array $hostname) : int
	{
		if ($this->use_multi) {
			return mt_rand(0, count($hostname) - 1);
		}
		
		return 0; 
	}

	/**
	* Sets the read hostname
	* @param string|array $hostname The db hostname
	* @param string|array $hostname The db port
	* @param string|array $username The db username
	* @param string|array $password The db password
	* @param string|array $database The database to use
	*/
	protected function setReadHost(string|array $hostname, string|array $port, string|array $username, string|array $password, string|array $database, bool|array $persistent)
	{
		$key = $this->getReadKey($hostname);
		
		$this->read_hostname = $this->use_multi ? $hostname[$key] : $hostname;
		$this->read_port = $this->use_multi ? $port[$key] : $port;
		$this->read_username = $this->use_multi ? $username[$key] : $username;
		$this->read_password = $this->use_multi ? $password[$key] : $password;
		$this->read_database = $this->use_multi ? $database[$key] : $database;
		$this->read_persistent = $this->use_multi ? $persistent[$key] : $persistent;
	}

	/**
	* Sets the write hostname
	* @param string|array $hostname The db hostname
	* @param string|array $port The db port
	* @param string|array $username The db username
	* @param string|array $password The db password
	* @param string|array $database The database to use
	*/
	protected function setWriteHost(string|array $hostname, string|array $port, string|array $username, string|array $password, string|array $database, bool|array $persistent)
	{
		$this->write_hostname = $this->use_multi ? $hostname[0] : $hostname;
		$this->write_port = $this->use_multi ? $port[0] : $port;
		$this->write_username = $this->use_multi ? $username[0] : $username;
		$this->write_password = $this->use_multi ? $password[0] : $password;
		$this->write_database = $this->use_multi ? $database[0] : $database;
		$this->write_persistent = $this->use_multi ? $persistent[0] : $persistent;
	}

	/**
	* Connects to the database server(s)
	*/
	protected function connect()
	{
		if ($this->connected) {
			return;
		}

		try {
			$this->write_handle = $this->getHandle($this->driver);
			$this->write_handle->connect($this->write_hostname, $this->write_port, $this->write_username, $this->write_password, $this->write_database, $this->write_persistent, $this->charset);

			if ($this->use_same_handle) {
				$this->read_handle = $this->write_handle;
			} else {
				$this->read_handle = $this->getHandle($this->driver);
				$this->read_handle->connect($this->read_hostname, $this->read_port, $this->read_username, $this->read_password, $this->read_database, $this->read_persistent, $this->charset);
			}
		} catch (\Exception $e) {
			throw new \Exception('Error connecting to the database: ' . $e->getMessage());
		}

		$this->connected = true;
	}

	/**
	* Disconnects from the database server
	*/
	protected function disconnect()
	{
		if (!$this->connected) {
			return;
		}

		$this->read_handle->disconnect();
		$this->write_handle->disconnect();
	}

	/**
	* Returns a Sql object
	* @return Sql;
	*/
	public function getSql() : Sql
	{
		return new Sql;
	}
	
	/**
	* Executes a read query
	* @param string|Sql $sql The query to execute
	* @param array $params Params to be used in prepared statements
	* @return object The result
	*/
	public function readQuery(string | Sql $sql, array $params = [])
	{
		return $this->query($sql, $params, true);
	}

	/**
	* Executes a write query
	* @param string|Sql $sql The query to execute
	* @param array $params Params to be used in prepared statements
	* @return object The result
	*/
	public function writeQuery(string|Sql $sql, array $params = [])
	{
		return $this->query($sql, $params, false);
	}

	/**
	* Executes a query
	* @param string|Sql $sql The query to execute
	* @param array $params Params to be used in prepared statements
	* @param bool $is_read If true, this is a read query
	* @return object The result
	*/
	public function query(string|Sql $sql, array $params = [], ?bool $is_read = null)
	{
		if (!$this->connected) {
			$this->connect();
		}

		if ($sql instanceof Sql) {
			$params = $sql->getParams();
			$is_read = $sql->isRead();
			$sql = $sql->getSql();			
		}

		$this->handle = $this->getQueryHandle($sql, $is_read);

		if ($this->debug) {
			$this->app->timer->start('sql');
		}

		try {
			$result = $this->handle->query($sql, $params);
		} catch (\Exception $e) {
			throw new \Exception('Query Error: ' . $this->getQueryError($e->getMessage(), $sql, $params));
		}

		if ($this->debug) {
			$exec_time = $this->app->timer->end('sql');

			$this->queries_time+= $exec_time;
			$this->queries[] = [$sql, $params, $exec_time];
		}

		return new DbResult($this->handle, $result);
	}

	/**
	* Returns the handle used to process the query
	* @param string $sql The sql query
	* @param bool $is_read  If true, this is a read query
	*/
	protected function getQueryHandle(string $sql, ?bool $is_read = null) : DriverInterface
	{
		if ($this->use_same_handle) {
			return $this->read_handle;
		} else {
			if ($is_read === null) {
				$is_read = $this->isReadQuery($sql);
			}

			if ($is_read) {
				return $this->read_handle;
			}

			return $this->write_handle;
		}
	}
	
	/**
	* Determines if the query is a read query
	* @param string $sql The sql query
	* @return bool
	*/
	protected function isReadQuery(string $sql) : bool
	{
		if (stripos(trim($sql), 'select') !== 0) {
			return false;
		}

		return true;
	}

	/**
	* Returns the error message for an invalid query
	* @param string $error The error
	* @param string $sql The sql code which generated the error
	* @param array $params The query params
	* @return string The error
	*/
	protected function getQueryError(string $error, string $sql, array $params)
	{
		$error = $error . "\n\n" . $sql;
		if ($params) {		
			$error.= "\n\n" . print_r($params, true);
		}
		
		return $error;
	}

	/**
	* Selects data from a table
	* @param string $table The table name
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array Returns an with the rows as array/objects. If there are no rows, will return an empty array
	*/
	public function select(string $table, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		$sql = $this->getSql()->select()->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		return $this->query($sql)->fetchAll();
	}

	/**
	* Selects data from a table and uses $key as a key of the returned array
	* @param string $table The table name
	* @param string $key The column to use as a key.
	* @param string|array $fields The fields to select
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @param bool $load_array If true, will return the result as an array
	* @return array
	*/
	public function selectWithKey(string $table, string $key, string|array $fields = '*', array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, bool $load_array = false) : array
	{
		$this->sql->select($fields)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		$this->readQuery();

		return $this->get($key, null, $load_array);
	}

	/**
	* Selects data from a table and returns it as an array
	* @see Db::select()
	* @return array
	*/
	public function selectArray(string $table, $fields = '*', array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		return $this->select($table, $fields, $where, $order_by, $order, $limit, $limit_offset, true);
	}

	/**
	* Similar with selectWithKey but will return the data as array
	* @see Db::selectWithKey()
	* @return array
	*/
	public function selectArrayWithKey(string $table, string $key, $fields = '*', array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		$this->sql->select($fields)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		$this->readQuery();

		return $this->get($key, null, true);
	}

	/**
	* Selects a single column from multiple rows and returns the results
	* @param string $table The table name
	* @param string $key_field The name of the field whose value will be used as the returned array's key
	* @param string $field The name of the field to return
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array
	*/
	public function selectList(string $table, string $key_field, string $field, array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0) : array
	{
		$this->sql->select([$key_field, $field])->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		$this->readQuery();

		return $this->getList($key_field, $field);
	}

	/**
	* Selects a single column from multiple rows and returns the results
	* @param string $table The table name
	* @param string $field The name of the field to return
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @return array
	*/
	public function selectField($table, $field, $where = [], $order_by = '', $order = '', $limit = 0, $limit_offset = 0) : array
	{
		$this->sql->select($field)->from($table)->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		$this->readQuery();

		return $this->get('', '', true, true);
	}

	/**
	* Selects a single row from the database
	* @param string $table The table name
	* @param string|array $fields The fields to select
	* @param array $where Where conditions in the format col => val
	* @param bool $load_array If true, will return the result as an array
	* @return array|object|null Returns the row as an object/array. If only a single column/field is requested will return it as a string; Returns null on failure
	*/
	public function selectRow(string $table, string|array $fields = '*', array $where = [], bool $load_array = false) : array|object|null
	{
		$this->sql->select($fields)->from($table)->where($where)->limit(1);

		$this->readQuery();

		return $this->getRow($load_array);
	}

	/**
	* Returns the first column from the first row generated by a query
	* @param string $table The table name
	* @param string|array $fields The fields to select. If only a single column/field is requested will return it as a string instead of array/object
	* @param array $where Where conditions in the format col => val
	* @return string The first column from the first row or null
	*/
	public function selectResult(string $table, string|array $fields = '*', array $where = []) : ?string
	{
		$this->sql->select($fields)->from($table)->where($where)->limit(1);

		$this->readQuery();

		return $this->getResult();
	}

	/**
	* Selects a single row from the database based on it's id
	* @param string $table The table name
	* @param int $id The id of the row to return
	* @param string|array $fields The fields to select
	* @param string $id_col The name of the id column
	* @param bool $load_array If true, will return the result as an array
	* @return array Returns the row with the id matching $id_value as an object; Returns null on failure
	*/
	public function selectById(string $table, int $id, string|array $fields = '*', string $id_col = 'id', bool $load_array = false)
	{
		$this->sql->select($fields)->from($table)->where([$id_col => $id])->limit(1);

		$this->readQuery();

		return $this->getRow($load_array);
	}

	/**
	* Selects multiple rows from the database based on id
	* @param string $table The table name
	* @param array $ids Array with the ids for which we're retriving data
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param string|array $fields The fields to select
	* @param string $id_col The name of the id column
	* @return array Returns the rows as an array with the id as a key
	*/
	public function selectByIds(string $table, array $ids, string $order_by = '', string $order = '', string|array $fields = '*', string $id_col = 'id', bool $load_array = false) : array
	{
		if (!$ids) {
			return [];
		}

		$this->sql->select($fields)->from($table)->whereIn($id_col, $ids)->orderBy($order_by, $order);

		$this->readQuery();

		return $this->get($id_col, null, $load_array);
	}

	/**
	* Determines if a row matching some conditions exists
	* @param string $table The table name
	* @param array $where Where conditions in the format col => val
	* @param string $field The field to include in the FROM clause. Should be the primary key, for performance purposes, if possible
	* @return bool True if the row exists, false otherwise
	*/
	public function exists(string $table, array $where, string $field = '*') : bool
	{
		$this->sql->select($field)->from($table)->where($where)->limit(1);

		$this->readQuery();

		if ($this->getRow() === null) {
			return false;
		}

		return true;
	}

	/**
	* Builds a count query. The format is: select count(*) from $table $where
	* @param string $table The table name
	* @param array $where Where conditions in the format col => val
	* @return int The number of rows
	*/
	public function count(string $table, array $where = []) : int
	{
		$this->sql->select('COUNT(*)')->from($table)->where($where);

		$this->readQuery();

		return $this->getCount();
	}

	/**
	* Counts the rows with the ids defined in $ids
	* @param string $table The table name
	* @param array $ids Array with the ids for which we're retriving the count
	* @param string $id_col The name of the id column
	* @return int The number of rows
	*/
	public function countIds(string $table, array $ids, string $id_col = 'id') : int
	{
		if (!$ids) {
			return 0;
		}

		$this->sql->select('COUNT(*)')->from($table)->whereIn($id_col, $ids);

		$this->readQuery();

		return (int)$this->getResult();
	}

	/**
	* Inserts data
	* @param string $table The table
	* @param array $values The data to insert in the column => value format. If value is an array it will be inserted without quotes/escaping. Usefull if a mysql function needs to be called (EG: NOW() )
	* @return int Returns the id of the newly inserted row
	*/
	public function insert(string $table, array $values) : int
	{
		if (!$values) {
			return 0;
		}

		$this->sql->insert($table)->values($values);

		$this->writeQuery();

		return $this->lastId();
	}

	/**
	* Does a multiple insert
	* @param string $table The table
	* @param array $values_list Array containing the list of data to insert. Eg: [ ['foo' => 'bar'], ['foo' => 'bar2'] ... ]
	* @param bool $columns If true, will also add the columns list
	* @return int The number of inserted rows
	*/
	public function insertMultiple(string $table, array $values_list, bool $columns = true) : int
	{
		if (!$values_list) {
			return 0;
		}

		$this->sql->insert($table)->valuesMulti($values_list, $columns);

		$this->writeQuery();

		return $this->affectedRows();
	}

	/**
	* Updates data
	* @param string $table The table
	* @param array $values The data to update in the column => value format. If value is an array it will be inserted without quotes/escaping. Usefull if a mysql function needs to be called (EG: NOW() )
	* @param array $where Where conditions in the format col => val
	* @param int $limit The limit, if any
	* @return int The number of affected rows
	*/
	public function update(string $table, array $values, array $where = [], int $limit = 0) : int
	{
		if (!$values) {
			return 0;
		}

		$this->sql->update($table)->set($values)->where($where)->limit($limit);

		$this->writeQuery();

		return $this->affectedRows();
	}

	/**
	* Updates a single row in the database based on id
	* @param $table The table
	* @param array $values @see update
	* @param int $id The id of the row to be updated
	* @param string $id_col The name of the id column
	* @return int The number of affected rows
	*/
	public function updateById(string $table, array $values, int $id, string $id_col = 'id') : int
	{
		return $this->update($table, $values, [$id_col => $id], 1);
	}

	/**
	* Updates multiple rows in the database based on id
	* @param string $table The table to update
	* @param array $values @see update
	* @param array $ids Array with the ids of the rows to update
	* @param string $id_col The name of the id column
	* @return int The number of affected rows
	*/
	public function updateByIds(string $table, array $values, array $ids, string $id_col = 'id') : int
	{
		if (!$ids) {
			return 0;
		}

		$this->sql->update($table)->set($values)->whereIn($id_col, $ids);

		$this->writeQuery();

		return $this->affectedRows();
	}

	/**
	* Executes a replace query
	* @param string $table The table
	* @param array $values The data to update in the column => value format. If value is an array it will be inserted without quotes/escaping. Usefull if a mysql function needs to be called (EG: NOW() )
	* @return int Returns the id of the inserted row
	*/
	public function replace(string $table, array $values) : int
	{
		if (!$values) {
			return 0;
		}

		$this->sql->replace($table)->set($values);

		$this->writeQuery();

		return $this->lastId();
	}

	/**
	* Deletes rows from a table
	* @param string $table The table
	* @param array $where Where conditions in the format col => val
	* @param int $limit The limit, if any
	* @return int The number of affected rows
	*/
	public function delete(string $table, array $where = [], int $limit = 0) : int
	{
		$this->sql->delete()->from($table)->where($where)->limit($limit);

		$this->writeQuery();

		return $this->affectedRows();
	}

	/**
	* Deletes a single row in the database based on id
	* @param $table The table
	* @param int $id The id of the row to be deleted
	* @param string $id_col The name of the id column
	* @return int The number of affected rows
	*/
	public function deleteById(string $table, int $id, string $id_col = 'id') : int
	{
		return $this->delete($table, [$id_col => $id], 1);
	}

	/**
	* Deletes multiple rows in the database based on id
	* @param $table The table
	* @param array $ids Array with the ids of the rows to update
	* @param string $id_col The name of the id column
	* @return int The number of affected rows
	*/
	public function deleteByIds(string $table, array $ids, string $id_col = 'id') : int
	{
		if (!$ids) {
			return 0;
		}

		$this->sql->delete()->from($table)->whereIn($id_col, $ids);

		$this->writeQuery();

		return $this->affectedRows();
	}
	
	
	/**
	* Returns the UNIX_TIMESTAMP function
	* @return array
	*/
	public function unixTimestamp() : array
	{
		return ['function' => 'UNIX_TIMESTAMP'];
	}

	/**
	* Returns the CRC32 function
	* @param string $value The value for which to compute the crc
	* @return array
	*/
	public function crc32(string $value) : array
	{
		return ['function' => 'CRC32', 'value' => $value];
	}

	/**
	* Returns the columns of table $table
	* @param string $table The name of the table for which the columns will be returned
	* @return array Returns the table columns
	*/
	public function getColumns(string $table) : array
	{
		$cols = [];

		$result = $this->readQuery("SHOW COLUMNS FROM {$table}");
		while (($arr = $this->fetchArray($result)) !== null) {
			$cols[] = $arr['Field'];
		}

		$this->free($result);

		return $cols;
	}

	/**
	* Returns the columns of table $table and the type
	* @param string $table The name of the table for which the columns will be returned
	* @return array Returns the table columns & types in the format col_name=>col_type
	*/
	public function getColumnsTypes(string $table) : array
	{
		$cols = [];

		$result = $this->readQuery("SHOW COLUMNS FROM {$table}");
		while (($arr = $this->fetchArray($result)) !== null) {
			$name = $arr['Field'];
			$type = $arr['Type'];

			$col_type = 'c';
			if (str_contains($type, 'int')) {
				$col_type = 'i';
			} elseif (str_contains($type, 'float')) {
				$col_type = 'f';
			}

			$cols[$name] = $col_type;
		}

		$this->free($result);

		return $cols;
	}

	/**
	* Returns a copy of $row based on the column types from $table
	* @param string $table The name of the table for which the copy is to be returned
	* @param array $row Array with the data to be copied in the format col_name => col_value
	* @param array $override_array Array with the data which will override the one from $row. Format col_name => col_value
	* @param array $reset_array Array containing the column names to be reset. If the column is int/float it will be set to 0. It will be set to '' otherwise
	* @param array $unset_array Array with the keys from $rows which should be unser,thus not returned
	* @param bool $primary_key If true will also return the primary array. By default set to false
	* @return array The copy
	*/
	public function copy(string $table, array $row, array $override_array = [], array $reset_array = [], array $unset_array = [], bool $primary_key = false) : array
	{
		$copy = [];

		$result = $this->readQuery("SHOW COLUMNS FROM {$table}");
		while (($arr = $this->fetchArray($result)) !== null) {
			$name = $arr['Field'];
			$type = $arr['Type'];

			if (!isset($row[$name])) {
				continue;
			}

			if (!$primary_key && $arr['Key'] == 'PRI') {
				continue;
			}

			$col_type = 'c';
			if (str_contains($type, 'int')) {
				$col_type = 'i';
			} elseif (str_contains($type, 'float')) {
				$col_type = 'f';
			}

			if ($override_array) {
				if (isset($override_array[$name])) {
					if (!is_array($override_array[$name])) {
						$copy[$name] = $thid->filter($override_array[$name], $col_type);
					} else {
						$copy[$name] = $override_array[$name];
					}

					continue;
				}
			}

			if ($unset_array) {
				if (in_array($name, $unset_array)) {
					continue;
				}
			}

			if ($reset_array) {
				if (in_array($name, $reset_array)) {
					if ($col_type == 'i' || $col_type == 'f') {
						$copy[$name] = 0;
					} else {
						$copy[$name] = '';
					}

					continue;
				}
			}

			$copy[$name] = $thid->filter($row[$name], $col_type);
		}

		$this->free($result);

		return $copy;
	}

	/**
	* Builds a set from table $table with the values from $data. The values are filtered based on the column's type
	* The difference between bind and bind_list is bind is blacklisting the columns which shouldn't be included; bind_list is whitelisting the desired columns.
	* @param string $table The name of the table
	* @param array $data If the column from $columns_array is null will read the value from $data
	* @param array $ignore_columns_array Array listing the columns from $table which shouldn't be included in the returned result
	* @param string $ignore_value If $ignore_value is not null,any values which equals $ignore_value won't be included in the returned result
	* @param string $data_prefix Prefix to be used on $data, if any
	* @return array
	*/
	public function bind(string $table, array $data = [], array $ignore_columns_array = [], ?string $ignore_value = null, string $data_prefix = '') : array
	{
		$ret = [];
		$table_columns_array = $this->getColumnsTypes($table);

		foreach ($table_columns_array as $name => $type) {
			if (in_array($name, $ignore_columns_array)) {
				continue;
			}

			$value = '';
			if (isset($data[$data_prefix . $name])) {
				$value = $data[$data_prefix . $name];
			} else {
				continue;
			}

			if ($value === $ignore_value) {
				continue;
			}

			$value = $this->filter($value, $type);

			if ($value === $ignore_value) {
				continue;
			}

			$ret[$name] = $value;
		}

		return $ret;
	}

	/**
	* Builds a set from table $table with the values from $data. The values are filtered based on the column's type
	* The difference between bind and bind_list is bind is blacklisting the columns which shouldn't be included; bind_list is whitelisting the desired columns.
	* @param string $table The name of the table
	* @param array $data If the column from $columns_array is null will read the value from $data
	* @param array $columns_array Array specifieing the columns of table $table for which the request values should be bind
	* @param string $ignore_value If $ignore_value is not null, any values which equals $ignore_value won't be included in the returned result
	* @param string $data_prefix Prefix to be used on $data, if any
	* @return array
	*/
	public function bindList(string $table, array $data = [], array $columns_array = [], ?string $ignore_value = null, string $data_prefix = '') : array
	{
		$ret = [];
		$table_columns_array = $this->getColumnsTypes($table);

		foreach ($table_columns_array as $name => $type) {
			if (!in_array($name, $columns_array)) {
				continue;
			}

			$value = '';
			if (isset($data[$data_prefix . $name])) {
				$value = $data[$data_prefix . $name];
			} else {
				continue;
			}

			if ($value === $ignore_value) {
				continue;
			}

			$value = $this->filter($value, $type);

			if ($value === $ignore_value) {
				continue;
			}

			$ret[$name] = $value;
		}

		return $ret;
	}

	/**
	* Returns an array from $table with the columns filled based on their type [int,float,char]
	* @param string $table The name of the table
	* @param array $override_array If specified will override the default filling
	* @param int $int_val The value used to fill int/float columns
	* @param string $string_val The value used to fill char/string columns
	* @param bool $primary_key If true will also return the primary array
	* @return array
	*/
	public function fill(string $table, array $override_array = [], int $int_val = 0, string $string_val = '', bool $primary_key = false) : array
	{
		$ret = [];
		$result = $this->readQuery("SHOW COLUMNS FROM {$table}");
		while (($arr = $this->fetchArray($result)) !== null) {
			$name = $arr['Field'];
			$type = $arr['Type'];

			if (!$primary_key && $arr['Key'] == 'PRI') {
				continue;
			}

			if (str_contains($type, 'int')) {
				$ret[$name] = $int_val;
			} elseif (str_contains($type, 'float')) {
				$ret[$name] = $int_val;
			} else {
				$ret[$name] = $string_val;
			}
		}

		$this->free($result);

		if ($override_array) {
			$ret = array_merge($ret, $override_array);
		}

		return $ret;
	}

	/**
	* @internal
	*/
	protected function filter(string $value, string $type)
	{
		switch ($type) {
			case 'i':
				return (int)$value;
			case 'f':
				return (float)$value;
			default:
				return (string)$value;
		}
	}
}
