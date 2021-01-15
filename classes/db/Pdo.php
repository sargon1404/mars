<?php
/**
* The PDO Database Driver
* @package Mars
*/

namespace Mars\Db;

/**
* The PDO Database Driver
*/
class Pdo implements DriverInterface
{
	/**
	* @var string $error_sql The sql which was executed when the error was generated
	*/
	public string $error_sql = '';

	/**
	* @var PDO $handle The PDO handle
	*/
	protected \PDO $handle;

	/**
	* @var object The result of the last query operation
	*/
	protected $result;

	/**
	* @see \Mars\Db\DriverInterface::connect()
	* {@inheritdoc}
	*/
	public function connect(string $hostname, string $port, string $username, string $password, string $database, string $charset)
	{
		$dsn = "mysql:dbname={$database};host={$hostname};port={$port};charset={$charset}";

		$this->handle = new \PDO($dsn, $username, $password);
	}

	/**
	* @see \Mars\Db\DriverInterface::disconnect()
	* {@inheritdoc}
	*/
	public function disconnect()
	{
		if (isset($this->handle)) {
			unset($this->handle);
		}
	}

	/**
	* @see \Mars\Db\DriverInterface::getError()
	* {@inheritdoc}
	*/
	public function getError() : string
	{
		if ($this->result) {
			$error = $this->result->errorInfo();
		} else {
			$error = $this->handle->errorInfo();
		}

		return $error[2];
	}

	/**
	* @see \Mars\Db\DriverInterface::getDump()
	* {@inheritdoc}
	*/
	public function getDump() : string
	{
		if (!$this->result) {
			return '';
		}

		ob_start();
		$this->result->debugDumpParams();

		return ob_get_clean();
	}

	/**
	* @see \Mars\Db\DriverInterface::selectDb()
	* {@inheritdoc}
	*/
	public function selectDb(string $database)
	{
		$this->query("USE {$database}");
	}

	/**
	* @see \Mars\Db\DriverInterface::escape()
	* {@inheritdoc}
	*/
	public function escape(string $value) : string
	{
		return $this->handle->quote($value);
	}

	/**
	* @see \Mars\Db\DriverInterface::query()
	* {@inheritdoc}
	*/
	public function query(string $sql, array $params = []) : ?object
	{
		if ($params) {
			$this->result = $this->handle->prepare($sql);

			//bind the params
			foreach ($params as $key => &$val) {
				$this->result->bindParam(':' . $key, $val, \PDO::PARAM_STR);
			}

			//execute the prepared statment
			if (!$this->result->execute()) {
				$this->error_sql = $sql;

				return null;
			}
		} else {
			$this->result = $this->handle->query($sql);
		}

		if (!$this->result) {
			$this->error_sql = $sql;
			$this->result = null;
		}

		return $this->result;
	}

	/**
	* @see \Mars\Db\DriverInterface::free()
	* {@inheritdoc}
	*/
	public function free($result)
	{
		unset($result);
	}

	/**
	* @see \Mars\Db\DriverInterface::lastId()
	* {@inheritdoc}
	*/
	public function lastId() : int
	{
		return $this->handle->lastInsertId();
	}

	/**
	* @see \Mars\Db\DriverInterface::affectedRows()
	* {@inheritdoc}
	*/
	public function affectedRows() : int
	{
		return $this->result->rowCount();
	}

	/**
	* @see \Mars\Db\DriverInterface::numRows()
	* {@inheritdoc}
	*/
	public function numRows($result) : int
	{
		return $result->rowCount();
	}

	/**
	* @see \Mars\Db\DriverInterface::fetchArray()
	* {@inheritdoc}
	*/
	public function fetchArray($result) : bool|array
	{
		return $result->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	* @see \Mars\Db\DriverInterface::fetchRow()
	* {@inheritdoc}
	*/
	public function fetchRow($result) : bool|array
	{
		return $result->fetch(\PDO::FETCH_NUM);
	}

	/**
	* @see \Mars\Db\DriverInterface::fetchObject()
	* {@inheritdoc}
	*/
	public function fetchObject($result, string $class_name) : bool|object
	{
		return $result->fetchObject($class_name);
	}
}
