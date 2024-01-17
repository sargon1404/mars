<?php
/**
* The Sql Builder Class
* @package Mars
*/

namespace Mars;

/**
 * The Sql Builder Class.
 * Builds sql code
 */
class Sql
{
	use AppTrait;

	/**
	 * @var string $sql The sql code
	 */
	protected string $sql = '';

	/**
	 * @var array $params The params to use in prepared statements
	 */
	protected array $params = [];

	/**
	 * @var bool $is_read Determines if the statement is a read statement
	 */
	protected bool $is_read = false;

	/**
	 * @internal
	 */
	protected bool $where = false;

	/**
	 * @internal
	 */
	protected bool $having = false;

	/**
	 * @internal
	 */
	protected int $param_index = 0;

	/**
	 * @internal
	 */
	protected int $in_index = 0;

	/**
	 * Converts the sql to a string
	 */
	public function __toString()
	{
		return $this->getSql();
	}

	/**
	 * Runs the SQL code as a query
	 * @param return DbResult
	 */
	public function query() : DbResult
	{
		return $this->app->db->query($this);
	}

	/**
	 * Returns the sql code
	 * @return string
	 */
	public function getSql() : string
	{
		return $this->sql;
	}

	/**
	 * Returns the params
	 * @return array
	 */
	public function getParams() : array
	{
		return $this->params;
	}

	/**
	 * Adds params to the params list
	 * @param array $params The params to add
	 * @return static
	 */
	protected function addParams(array $params) : static
	{
		$this->params = $this->params + $params;

		return $this;
	}

	/**
	 * Adds a param to the params list
	 * @param string $param The param
	 * @param string $value The value of the param
	 * @return string The param, prepanded by ':'
	 */
	protected function addParam(string $param, string $value) : string
	{
		if (isset($this->params[$param])) {
			$param = $param . '_' . mt_rand(0, 9999999999);
		}

		$this->params[$param] = $value;

		return ':' . $param;
	}

	/**
	 * Generates a param name
	 */
	protected function generateParam() : string
	{
		$param = 'param_' . $this->param_index;
		$this->param_index++;

		return $param;
	}

	/**
	 *Returns true if this is a read statement
	 */
	public function isRead() : bool
	{
		return $this->is_read;
	}

	/**
	 * Starts a new statement
	 * @param bool $is_read The tpye of the statement
	 * @return static
	 */
	protected function start(bool $is_read = false)
	{
		$this->sql = '';
		$this->params = [];
		$this->is_read = $is_read;
		$this->where = false;
		$this->having = false;
		$this->param_index = 0;
		$this->in_index = 0;

		return $this;
	}

	/**
	 * Escapes a table name
	 * @param string $table The table
	 * @param string $alias The alias of the table, if any
	 */
	protected function escapeTable(string $table, string $alias = '') : string
	{
		$table = "`{$table}`";
		if ($alias) {
			$table.= " AS {$alias}";
		}

		return $table;
	}

	/**
	 * Escapes a column name
	 * @param string $column The column to escape
	 * @return string The escaped column name
	 */
	protected function escapeColumn(string $column) : string
	{
		return '`' . $column . '`';
	}

	/**
	 * Escapes a value meant to be used in a like %% part
	 * @param string $value The value to escape
	 * @return string The escaped value
	 */
	protected function escapeLike(string $value) : string
	{
		return str_replace('%', '\%', $value);
	}

	/**
	 * Returns a list of columns, delimited by comma
	 * @param array $cols The columns
	 * @return string The column list
	 */
	protected function getColumnsList(array $cols): string
	{
		array_walk($cols, function (&$col) {
			$col = $this->escapeColumn($col);
		});

		return implode(', ', $cols);
	}

	/**
	 * Builds a SELECT query
	 * @param string|array $cols The cols to select
	 * @return static
	 */
	public function select(string|array $cols = '*') : static
	{
		$this->start(true);

		if (is_array($cols)) {
			$cols = $this->getColumnsList($cols);
		}

		$this->sql = "SELECT {$cols}";

		return $this;
	}

	/**
	 * Builds a SELECT COUNT(*) query
	 * @return static
	 */
	public function selectCount() : static
	{
		return $this->select('COUNT(*)');
	}

	/**
	 * Adds the FROM clause
	 * @param string $table The table
	 * @param string $alias The alias of the table, if any
	 * @return static
	 */
	public function from(string $table, string $alias = '') : static
	{
		$table = $this->escapeTable($table, $alias);

		$this->sql.= " FROM {$table}";

		return $this;
	}

	/**
	 * Adds a LEFT JOIN clause
	 * @param string $table The table to join
	 * @param string $alias The alias of the table, if any
	 * @param string $using The column used in the USING part, if any
	 * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
	 * @return static
	 */
	public function leftJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
	{
		$table = $this->escapeTable($table, $alias);

		$this->sql.= " LEFT JOIN {$table}" . $this->getJoinSql($using, $on);

		return $this;
	}

	/**
	 * Adds a RIGHT JOIN clause
	 * @param string $table The table to join
	 * @param string $alias The alias of the table, if any
	 * @param string $using The column used in the USING part, if any
	 * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
	 * @return static
	 */
	public function rightJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
	{
		$table = $this->escapeTable($table, $alias);

		$this->sql.= " RIGHT JOIN {$table}" . $this->getJoinSql($using, $on);

		return $this;
	}

	/**
	 * Adds a INNER JOIN clause
	 * @param string $table The table to join
	 * @param string $alias The alias of the table, if any
	 * @param string $using The column used in the USING part, if any
	 * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
	 * @return static
	 */
	public function innerJoin(string $table, string $alias = '', string $using = '', string $on = '') : static
	{
		$table = $this->escapeTable($table, $alias);

		$this->sql.= " INNER JOIN {$table}" . $this->getJoinSql($using, $on);

		return $this;
	}

	/**
	 * Builds the USING or OR part of a join
	 * @param string $using The column used in the USING part, if any
	 * @param string $on Custom sql to add in the ON part of the join clause, if $using is empty
	 */
	protected function getJoinSql(string $using, string $on) : string
	{
		if ($using) {
			return ' USING (' . $this->escapeColumn($using) . ')';
		} elseif ($on) {
			return " ON {$on}";
		}

		return '';
	}

	/**
	 * Builds an INSERT query
	 * @param string $table The table to insert into
	 * @return static
	 */
	public function insert(string $table) : static
	{
		$this->start();

		$this->sql = "INSERT INTO {$table}";

		return $this;
	}

	/**
	 * Builds an UPDATE query
	 * @param string $table The table
	 * @return static
	 */
	public function update(string $table) : static
	{
		$this->start();

		$this->sql = "UPDATE {$table}";

		return $this;
	}

	/**
	 * Builds a REPLACE query
	 * @param string $table The table
	 * @return static
	 */
	public function replace(string $table) : static
	{
		$this->start();

		$this->sql = "REPLACE INTO {$table}";

		return $this;
	}

	/**
	 * Builds a DELETE query
	 * @return static
	 */
	public function delete() : static
	{
		$this->start();

		$this->sql = "DELETE";

		return $this;
	}

	/**
	 * Builds the VALUES part of an INSERT query
	 * @param array $values The data to insert in the column => value format. If value is an array it will be inserted as it is. Usefull if a mysql function needs to be called (EG: NOW() )
	 * @return static
	 */
	public function values(array $values) : static
	{
		$cols = $this->getColumnsList(array_keys($values));
		$values = $this->getValuesList($values);

		$this->sql.= "({$cols}) VALUES({$values})";

		return $this;
	}

	/**
	 * Builds the VALUES part of an INSERT query by generating multiple values
	 * @param array $values_list Array containing the list of data to insert. Eg: [ ['foo' => 'bar'], ['foo' => 'bar2'] ... ]
	 * @return static
	 */
	public function valuesMulti(array $values_list) : static
	{
		$cols = $this->getColumnsList(array_keys(reset($values_list)));

		$this->sql.= "({$cols}) VALUES";

		$list = [];
		foreach ($values_list as $key => $values) {
			$list[] = '(' . $this->getValuesList($values, $key) . ')';
		}

		$this->sql.= implode(', ', $list);

		return $this;
	}

	/**
	 * Returns the values of an INSERT query
	 * @param array $values The values to insert
	 * @param string $suffix Suffix, if any, to add to the name of params
	 * @return string The values
	 */
	protected function getValuesList(array $values, string $suffix = '') : string
	{
		$vals = [];

		foreach ($values as $col => $value) {
			$col = $col . $suffix;

			if (is_array($value)) {
				$vals[] = $this->getValue($col, $value);
			} else {
				$vals[] = $this->addParam($col, $value);
			}
		}

		return implode(', ', $vals);
	}

	/**
	 * Returns the value to be inserted/updated from an array
	 * @param string $col The column
	 * @param string $value The value. Can contain the function/value keys
	 * @return string The value
	 */
	protected function getValue(string $col, array $value) : string
	{
		//if there is a 'function' key specified, use it to return the value as a MYSQL function
		if (isset($value['function'])) {
			$func = strtoupper($value['function']);

			if (isset($value['value'])) {
				return $func . '(' . $this->addParam($col, $value['value']) . ')';
			} else {
				return $func . '()';
			}
		} else {
			if (isset($value['value'])) {
				return $this->addParam($col, $value['value']);
			} else {
				return reset($value);
			}
		}
	}

	/**
	 * Returns the operator - value SQL part
	 * @param string $col The column
	 * @param string $value The value
	 * @param string $operator The operator
	 * @return string
	 */
	protected function prepareValue(string $col, string $value, string $operator) : string
	{
		switch (strtolower($operator)) {
			case 'like':
				$value = '%' . $this->escapeLike($value) . '%';
				return 'LIKE ' . $this->addParam($col, $value);
			case 'like_simple':
				$value = $this->escapeLike($value);
				return 'LIKE ' . $this->addParam($col, $value);
				break;
			case 'like_left':
				$value = '%' . $this->escapeLike($value);
				return 'LIKE ' . $this->addParam($col, $value);
				break;
			case 'like_right':
				$value = $this->escapeLike($value) . '%';
				return 'LIKE ' . $this->addParam($col, $value);
				break;
			default:
				return $operator . ' ' . $this->addParam($col, $value);
		}

		return $value;
	}

	/**
	 * Builds the SET part of an update query
	 * @param array $values The data to updated in the column => value format. If value is an array it will be updated as it is. Usefull if a mysql function needs to be called (EG: NOW() )
	 * @return static
	 */
	public function set(array $values) : static
	{
		$values = $this->getSetList($values);

		$this->sql.= " SET {$values}";

		return $this;
	}

	/**
	 * Returns the fields of an SET part
	 * @param array $values The values to insert
	 * @return string The fields
	 */
	protected function getSetList(array $values)
	{
		$vals = [];

		foreach ($values as $col => $value) {
			$col_esc = $this->escapeColumn($col);

			if (is_array($value)) {
				$vals[] = $col_esc . ' = ' . $this->getValue($col, $value);
			} else {
				$vals[] = $col_esc . ' = ' . $this->addParam($col, $value);
			}
		}

		return implode(', ', $vals);
	}

	/**
	 * Starts a WHERE clause
	 */
	protected function startWhere()
	{
		if (!$this->where) {
			$this->sql.= ' WHERE';
			$this->where = true;
		}
	}
	/**
	 * Builds a WHERE clause
	 * @param array $where The where conditions. The format must be: column => value or column => [value,operator,function,value]
	 * @param string $delimitator The delimitator to use between parts. By default AND is used.
	 * @return static
	 */
	public function where(array $where, string $delimitator = 'AND') : static
	{
		if (!$where) {
			return $this;
		}

		$this->startWhere();

		$this->sql.= ' (' . $this->getConditions($where, $delimitator) . ')';

		return $this;
	}

	/**
	 * Returns a WHERE IN(...) clause
	 * @param string $column The column
	 * @param array $values Array with the elements to place in the IN list
	 * @param bool $is_int If true,will treat the elements from $in_array as int values
	 * @return static
	 */
	public function whereIn(string $column, array $values, bool $is_int = true) : static
	{
		if (!$values) {
			return $this;
		}

		$this->startWhere();

		$this->sql.= ' ' . $this->escapeColumn($column) . $this->getIn($values, $is_int);

		return $this;
	}

	/**
	 * Returns the AND keyword
	 * @return static
	 */
	public function and() : static
	{
		$this->sql.= ' AND ';

		return $this;
	}

	/**
	 * Returns the AND keyword
	 * @return static
	 */
	public function or() : static
	{
		$this->sql.= ' OR ';

		return $this;
	}

	/**
	 * Determines if an array is a IN list
	 * @param array $value The array
	 * @return true True if it's an IN list
	 */
	protected function isIn(array $value) : bool
	{
		if (isset($value['operator']) || isset($value['function']) || isset($value['value'])) {
			return false;
		}

		return true;
	}

	/**
	 * Returns an IN(...) list
	 * @param array $values The IN values
	 * @param bool $is_int If true, will treat the elements from the list as int values
	 * @return string
	 */
	protected function getIn(array $values, bool $is_int = true) : string
	{
		if ($is_int) {
			$values = $this->app->filter->int($values);
		} else {
			$key = 0;

			foreach ($values as $value) {
				//generate a param for each IN value
				$col = 'in_' . $this->in_index . '_' . $key;
				$values[$key] = $this->addParam($col, $value);

				$key++;
			}

			$this->in_index++;
		}

		return ' IN(' . implode(', ', $values) . ')';
	}

	/**
	 * Builds multiple conditions
	 * @param array $conditions The conditions
	 * @param string $delimitator The delimitator to use
	 * @param bool If true, will escape the column
	 * @param bool If true, will generate param names
	 * @return string
	 */
	protected function getConditions(array $conditions, string $delimitator, bool $escape_col = true, bool $generate_param = false) : string
	{
		$parts = [];
		foreach ($conditions as $col => $value) {
			$col_esc = $escape_col ? $this->escapeColumn($col) : $col;
			$col = $generate_param ? $this->generateParam() : $col;

			if (is_array($value)) {
				if ($this->isIn($value)) {
					$parts[] = $col_esc . $this->getIn($value, false);
				} else {
					$operator = $value['operator'] ?? '=';
					$value = $value['value'] ?? '';

					$parts[] = $col_esc . ' ' . $this->prepareValue($col, $value, $operator);
				}
			} else {
				$parts[] = $col_esc . ' = ' . $this->addParam($col, $value);
			}
		}

		return implode(' ' . $delimitator . ' ', $parts);
	}

	/**
	 * Starts a HAVING clause
	 */
	protected function startHaving()
	{
		if (!$this->having) {
			$this->sql.= ' HAVING';
			$this->having = true;
		}
	}

	/**
	 * Builds a HAVING clause
	 * @param array $where The where conditions. The format must be: column => value or column => [value,operator,function,value]
	 * @param string $delimitator The delimitator to use between parts. By default AND is used.
	 * @return static
	 */
	public function having(array $having, string $delimitator = 'AND') : static
	{
		if (!$having) {
			return $this;
		}

		$this->startHaving();

		$this->sql.= ' (' . $this->getConditions($having, $delimitator, false, true) . ')';

		return $this;
	}

	/**
	 * Returns an ORDER BY clause
	 * @param string $order_by The order by column
	 * @param string $order The order: asc/desc
	 * @return static
	 */
	public function orderBy(string $order_by, string $order = '') : static
	{
		if (!$order_by) {
			return $this;
		}

		$order_by = $this->escapeColumn($order_by);
		$order = strtoupper(trim($order));

		if ($order == 'ASC' || $order == 'DESC') {
			$this->sql.= " ORDER BY {$order_by} {$order}";
		} else {
			$this->sql.= " ORDER BY {$order_by}";
		}

		return $this;
	}

	/**
	 * Returns a GROUP BY clause
	 * @param string $group_by The group by column
	 * @return static
	 */
	public function groupBy(string $group_by) : static
	{
		$group_by = $this->escapeColumn($group_by);

		$this->sql.= " GROUP BY {$group_by}";

		return $this;
	}

	/**
	 * Returns a LIMIT clause
	 * @param int $count The number of items
	 * @param int int The offset, if any
	 * @return static
	 */
	public function limit(int $count, int $offset = 0) : static
	{
		if (!$count) {
			return $this;
		}

		if ($offset) {
			$this->sql.= " LIMIT {$offset}, {$count}";
		} else {
			$this->sql.= " LIMIT {$count}";
		}

		return $this;
	}

	/**
	 * Returns a LIMIT clause corresponding to the current page
	 * @param int $page The page number of the current page
	 * @param int $page_items Items per page
	 * @param int $total_items The total number of items.
	 * @return static
	 */
	public function pageLimit(int $page = 0, int $page_items = 0, int $total_items = 0) : static
	{
		$page--;

		if ($page < 0) {
			$page = 1;
		}

		if ($total_items) {
			$nr_pages = ceil($total_items / $page_items);
			if ($page >= $nr_pages) {
				$page = 1;
			}
		}

		$offset = $page * $page_items;

		$this->limit($page_items, $offset);

		return $this;
	}
}
