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
	public function connect(string $hostname, string $port, string $username, string $password, string $database, bool $persistent, string $charset)
	{
		$dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset={$charset}";
		$options = [\PDO::ATTR_PERSISTENT => $persistent];

		try {
			$this->handle = new \PDO($dsn, $username, $password, $options);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
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
	 * @see \Mars\Db\DriverInterface::begin()
	 * {@inheritdoc}
	 */
	public function begin()
	{
		$this->handle->beginTransaction();
	}
	
	/**
	 * @see \Mars\Db\DriverInterface::commit()
	 * {@inheritdoc}
	 */
	public function commit()
	{
		$this->handle->commit();
	}
	
	/**
	 * @see \Mars\Db\DriverInterface::rollback()
	 * {@inheritdoc}
	 */
	public function rollback()
	{
		$this->handle->rollBack();
	}

	/**
	 * @see \Mars\Db\DriverInterface::query()
	 * {@inheritdoc}
	 */
	public function query(string $sql, array $params = []) : ?object
	{
		try {
			if ($params) {
				$this->result = $this->handle->prepare($sql);
				$this->result->execute($this->getParams($params));
			} else {
				$this->result = $this->handle->query($sql);
			}
		} catch (\PDOException $e) {
			$this->result = null;

			throw new \Exception($e->getMessage());
		}

		return $this->result;
	}

	/**
	 * Returns the prepared params
	 * @param array $params The params
	 * @return array The prepared params
	 */
	protected function getParams(array $params) : array
	{
		$keys = array_map(function ($key) {
			return ':' . $key;
		}, array_keys($params));
		
			
		return array_combine($keys, $params);
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
	public function fetchArray($result) : array
	{
		$row = $result->fetch(\PDO::FETCH_ASSOC);
		if (!$row) {
			$row = [];
		}

		return $row;
	}

	/**
	 * @see \Mars\Db\DriverInterface::fetchRow()
	 * {@inheritdoc}
	 */
	public function fetchRow($result) : array
	{
		$row = $result->fetch(\PDO::FETCH_NUM);
		if (!$row) {
			$row = [];
		}

		return $row;
	}

	/**
	 * @see \Mars\Db\DriverInterface::fetchObject()
	 * {@inheritdoc}
	 */
	public function fetchObject($result, string $class_name = '') : ?object
	{
		if (!$class_name) {
			$class_name = '\StdClass';
		}

		$row = $result->fetchObject($class_name);
		if (!$row) {
			$row = null;
		}

		return $row;
	}
	
	/**
	 * @see \Mars\Db\DriverInterface::fetchColumn()
	 * {@inheritdoc}
	 */
	public function fetchColumn($result, int $column = 0) : ?string
	{
		$col = $result->fetchColumn($column);
		if ($col === false) {
			$col = null;
		}
		
		return $col;
	}

	/**
	 * @see \Mars\Db\DriverInterface::fetchAll()
	 * {@inheritdoc}
	 */
	public function fetchAll($result, bool|string $class_name = '') : array
	{
		if ($class_name === true) {
			return $result->fetchAll(\PDO::FETCH_ASSOC);
		}

		if (!$class_name) {
			$class_name = '\StdClass';
		}

		return $result->fetchAll(\PDO::FETCH_CLASS, $class_name);
	}
	
	/**
	 * @see \Mars\Db\DriverInterface::fetchAllFromColumn()
	 * {@inheritdoc}
	 */
	public function fetchAllFromColumn($result, int $column = 0) : array
	{
		return $result->fetchAll(\PDO::FETCH_COLUMN, $column);
	}
}
