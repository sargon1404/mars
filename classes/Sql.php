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
	* @internal
	*/
	protected array $has = [];

	/**
	* Resets the sql/params
	* @return $this
	*/
	public function reset()
	{
		$this->sql = '';
		$this->params = [];
		$this->has = [];

		return $this;
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
	* Converts the sql to a string
	*/
	public function __toString()
	{
		return $this->getSql();
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
	* Sets the sql code
	* @param string $sql The sql code
	* @param array The params if any
	* @return $this
	*/
	public function setSql(string $sql, array $params = [])
	{
		$this->reset();

		$this->sql = $sql;
		$this->params = $params;

		return $this;
	}

	/**
	* Adds params to the params list
	* @param array $params The params to add
	* @return $this
	*/
	public function addParams(array $params)
	{
		if (!$params) {
			return $this;
		}

		$this->params = $this->params + $params;

		return $this;
	}

	/**
	* Escapes a column name
	* @param string $column The column to escape
	* @return string The escaped column name
	*/
	public function escapeColumn(string $column) : string
	{
		if ($column[0] == '`') {
			return $column;
		}

		//is the table specified? Eg: users.username
		if (!str_contains($column, '.')) {
			return '`' . $column . '`';
		}

		$parts = explode('.', $column);

		return "{$parts[0]}.`{$parts[1]}`";
	}

	/**
	* Escapes a value meant to be used in a like %% part
	* @param string $value The value to escape
	* @return string The escaped value
	*/
	public function escapeLike(string $value) : string
	{
		$value = str_replace('%', '\%', $value);

		return $value;
	}

	/**
	* Builds a SELECT query
	* @param string|array $fields The fields to select. Either a string or an array. If array, the fields will be escaped, if string will NOT
	* @return $this
	*/
	public function select(string|array $fields = '*')
	{
		$this->reset();

		if (!$fields) {
			$fields = '*';
		}

		if (is_array($fields)) {
			//escape the fields
			foreach ($fields as $i => $field) {
				$field[$i] = $this->escapeColumn($field);
			}

			$fields = implode(', ', $fields);
		}

		$this->sql = "SELECT {$fields}";

		return $this;
	}

	/**
	* Builds a SELECT COUNT(*) query
	* @return $this
	*/
	public function selectCount()
	{
		return $this->select('COUNT(*)');
	}

	/**
	* Adds the FROM statement
	* @param string $table The table
	* @param string $alias The alias of the table, if any
	* @return $this
	*/
	public function from(string $table, string $alias = '')
	{
		$this->sql.= " FROM {$table}";

		if ($alias) {
			$this->sql.= " AS {$alias}";
		}

		return $this;
	}

	/**
	* Returns a list of columns from $cols_list
	* @param array $cols_list The columns list
	* @return string The column list, delimited by comma
	*/
	public function getColumnsList(array $cols_list): string
	{
		$cols = [];

		foreach ($cols_list as $col) {
			$cols[] = $this->escapeColumn($col);
		}

		$cols_list = implode(', ', $cols);

		return "({$cols_list})";
	}

	/**
	* Builds an INSERT query
	* @param string $table The table
	* @return $this
	*/
	public function insert(string $table)
	{
		$this->reset();

		$this->sql = "INSERT INTO {$table}";

		return $this;
	}

	/**
	* Builds the VALUES part of an insert query
	* @param array $values The data to insert in the column => value format. If value is an array it will be inserted without quotes/escaping. Usefull if a mysql function needs to be called (EG: NOW() )
	* @param bool $columns If true, will also add the columns list
	* @return $this
	*/
	public function values(array $values, bool $columns = true)
	{
		if ($columns) {
			$this->sql.= $this->getColumnsList(array_keys($values));
		}

		$this->sql.= ' VALUES(' . $this->getInsertFields($values) . ')';

		return $this;
	}

	/**
	* Builds the VALUES part of an multi insert query
	* @param array $values_list Array containing the list of data to insert. Eg: [ ['foo' => 'bar'], ['foo' => 'bar2'] ... ]
	* @param bool $columns If true, will also add the columns list
	* @return $this
	*/
	public function valuesMulti(array $values_list, bool $columns = true)
	{
		if ($columns) {
			$this->sql.= $this->getColumnsList(array_keys(reset($values_list)));
		}

		$this->sql.= ' VALUES';

		$sql_array = [];
		foreach ($values_list as $key => $values) {
			$sql_array[] = '(' . $this->getInsertFields($values, $key) . ')';
		}

		$this->sql.= implode(', ', $sql_array);

		return $this;
	}

	/**
	* Returns the fields of an insert query
	* @param array $values The values to insert
	* @param string $suffix Suffix, if any, to add to the name of the prepared statement param
	* @return string The fields
	*/
	protected function getInsertFields(array $values, string $suffix = '') : string
	{
		$vals = [];

		foreach ($values as $col => $value) {
			$col = $col . $suffix;

			if (is_array($value)) {
				$vals[] = $this->getValueFromArray($value, $col, false, true);
			} else {
				$vals[] = ':' . $col;
				$this->params[$col] = $value;
			}
		}

		return implode(', ', $vals);
	}

	/**
	* Returns the value to be inserted/updated from an array
	* @param array $array The array. Can contain keys: function/value/operator
	* @param string $col The key under which the param, if any, will be saved
	* @param bool $add_operator If true, will also return the operator
	* @param bool $is_function if true, by default the return value is considered a function
	* @return string The value
	*/
	protected function getValueFromArray(array $array, string $col, bool $add_operator = true, bool $is_function = false) : string
	{
		$value = '';
		$operator = $array['operator'] ?? '=';

		if (isset($array['function'])) {
			$func = strtoupper($array['function']);

			if (isset($array['value'])) {
				$value.= $func . "(:{$col})";
				$this->params[$col] = $array['value'];
			} else {
				$value.= $func . '()';
			}
		} else {
			$val = $array['value'] ?? reset($array);

			if ($is_function) {
				$value.= strtoupper($val) . '()';
			} else {
				$value.= ':' . $col;
				$this->params[$col] = $this->prepareValue($val, $operator);
			}
		}

		if ($add_operator) {
			$value = ' ' . $operator . ' ' . $value;
		}

		return $value;
	}

	/**
	* Prepares a value
	* @param string $value The value
	* @param string $operator The operator
	* @return string
	*/
	protected function prepareValue(string $value, string $operator) : string
	{
		$operator = strtolower($operator);

		switch ($operator) {
			case 'like':
				$value = '%' . $this->escapeLike($value) . '%';
			break;
			case 'like_left':
				$value = '%' . $this->escapeLike($value);
			break;
			case 'like_right':
				$value = $this->escapeLike($value) . '%';
			break;
			//else if(strpos($value[1], '{VALUE}') !== false)
			//$parts[] = str_replace('{VALUE}', $this->escape($value[0], false), $value[1]);
		}

		return $value;
	}

	/**
	* Builds an UPDATE query
	* @param string $table The table
	* @return $this
	*/
	public function update(string $table)
	{
		$this->reset();

		$this->sql = "UPDATE {$table}";

		return $this;
	}

	/**
	* Builds the SET part of an update query
	* @param array $values The data to update in the column => value format
	* @return $this
	*/
	public function set(array $values)
	{
		$this->sql.= " SET " . $this->getSetFields($values);

		return $this;
	}

	/**
	* Returns the fields of an update set query
	* @param array $values The values to insert
	* @return string The fields
	*/
	protected function getSetFields(array $values)
	{
		$vals = [];
		foreach ($values as $col => $value) {
			$col_esc = $this->escapeColumn($col);

			if (is_array($value)) {
				$vals[] = $col_esc . $this->getValueFromArray($value, $col, true, true);
			} else {
				$vals[] = $col_esc . '= :' . $col;
				$this->params[$col] = $value;
			}
		}

		return implode(', ', $vals);
	}

	/**
	* Builds a REPLACE query
	* @param string $table The table
	* @return $this
	*/
	public function replace(string $table)
	{
		$this->reset();

		$this->sql = "REPLACE INTO {$table}";

		return $this;
	}

	/**
	* Builds a DELETE query
	* @return $this
	*/
	public function delete()
	{
		$this->reset();

		$this->sql = "DELETE";

		return $this;
	}

	/**
	* Adds a LEFT JOIN statement
	* @param string $table The table to join
	* @param string $using The field used in the USING part, if any
	* @param string $on Custom sql to add in the ON part of the join statement, if $using is empty
	* @return $this
	*/
	public function leftJoin(string $table, string $using = '', string $on = '')
	{
		$this->sql.= " LEFT JOIN {$table}" . $this->getJoinSql($using, $on);

		return $this;
	}

	/**
	* Adds a LEFT JOIN statement
	* @param string $table The table to join
	* @param string $using The field used in the USING part, if any
	* @param string $on Custom sql to add in the ON part of the join statement, if $using is empty
	* @return $this
	*/
	public function rightJoin(string $table, string $using = '', string $on = '')
	{
		$this->sql.= " RIGHT JOIN {$table}" . $this->getJoinSql($using, $on);

		return $this;
	}

	/**
	* Builds the USING or OR part of a join
	* @param string $using The field used in the USING part, if any
	* @param string $on Custom sql to add in the ON part of the join statement, if $using is empty
	*/
	protected function getJoinSql(string $using, string $on) : string
	{
		if ($using) {
			return " USING ({$using})";
		} elseif ($on) {
			return " ON {$on}";
		}

		return '';
	}

	/**
	* Returns a random key
	* @param string $key The key
	* @return string
	*/
	protected function getKey(string $key) : string
	{
		return $key . '_' . mt_rand(0, 999999999);
	}

	/**
	* Returns the delimitator, between where/having parts
	* @param string $type The delimitator's type
	* @param string $delimitator The delimitator
	* @return string
	*/
	protected function getDelimitator(string $type, string $delimitator) : string
	{
		$add = false;
		if (empty($this->has[$type])) {
			$this->has[$type] = true;
			$delimitator = $type;
		}

		return ' ' . $delimitator;
	}

	/**
	* Builds a condition
	* @param string $col The name of the column
	* @param string $value The value
	* @param string $operator The operator
	* @param string $alias If specified, will use the alias, instead of the column as a prepared statement key
	* @param bool $escape_col If true, the column name will be escaped using ``
	* @return string
	*/
	protected function getCondition(string $col, string $value, string $operator = '=', string $alias = '', bool $escape_col = true) : string
	{
		$col_esc = $col;
		if ($escape_col) {
			$col_esc = $this->escapeColumn($col);
		}

		$key = $col;
		if ($alias) {
			$key = $alias;
		}

		if (isset($this->params[$key])) {
			$key = $this->getKey($key);
		}

		$this->params[$key] = $value;

		return " {$col_esc} {$operator} :{$key}";
	}

	/**
	* Builds multiple conditions
	* @param array conditions Array containing the conditions, in the format column => value. If value is an array: a) column => [value1, value2, value3..] OR b) column => ['value' => value, 'operator' => operator]
	* @param string $delimitator The delimitator to use between parts. By default AND is used.
	* @return string
	*/
	protected function getConditions(array $conditions, string $delimitator = 'AND') : string
	{
		if (!$conditions) {
			return '';
		}

		$parts = [];
		foreach ($conditions as $col => $value) {
			$key = $col;
			$operator = '=';
			$operator_suffix = '';
			$col_esc = $this->escapeColumn($col);

			if (is_array($value) && !isset($value['value']) && !isset($value['operator']) && !isset($value['function'])) {
				//this is an array of values; build an IN list
				$in_list = $this->getIn($value, false, $params);
				$this->addParams($params);

				$parts[] = $col_esc . $in_list;
				continue;
			} else {
				if (isset($this->params[$key])) {
					$key = $this->getKey($key);
				}

				if (is_array($value)) {
					$parts[] = $col_esc . $this->getValueFromArray($value, $key);
				} else {
					$parts[] = $col_esc . ' ' . $operator . ' :' . $key;

					$this->params[$key] = $value;
				}
			}
		}

		return ' ' . implode(' ' . $delimitator . ' ', $parts);
	}

	/**
	* Builds a WHERE statement
	* @param string|array $where Either array or string. If array the format must be: column => value or column => [value,operator]. If string, the name of the column
	* @param string $value The value, if $where is specified as string
	* @param string $operator The operator: =/</> etc...
	* @param string $delimitator The delimitator to use between parts. By default AND is used.
	* @return $this
	*/
	public function where(string|array $where, string $value = '', string $operator = '=', string $delimitator = 'AND')
	{
		if (!$where) {
			return $this;
		}

		$this->sql.= $this->getDelimitator('WHERE', $delimitator);

		if (is_array($where)) {
			$this->sql.= $this->getConditions($where, $delimitator);
		} else {
			$this->sql.= $this->getCondition($where, $value, $operator);
		}

		return $this;
	}

	/**
	* Returns a WHERE IN(...) statement
	* @param string $col The column
	* @param array $in_array Array with the elements to place in the in list
	* @param bool $is_int If true,will treat the elements from $in_array as int values
	* @param string $delimitator The delimitator to use between parts. By default AND is used.
	* @return $this
	*/
	public function whereIn(string $col, array $in_array, bool $is_int = true, string $delimitator = 'AND')
	{
		$this->sql.= $this->getDelimitator('WHERE', $delimitator);

		$col_esc = $this->escapeColumn($col);
		$in_list = $this->getIn($in_array, $is_int, $params);

		if (!$is_int) {
			$this->addParams($params);
		}

		$this->sql.= " " . $col_esc . $in_list;

		return $this;
	}

	/**
	* Returns a WHERE statment by directly appending the $sql
	* @param string $sql The sql to add
	* @param string $delimitator The delimitator to use between parts. By default AND is used.
	* @return $this
	*/
	public function whereSql(string $sql, string $delimitator = 'AND')
	{
		$this->sql.= $this->getDelimitator('WHERE', $delimitator);

		$this->sql.= " " . $sql;

		return $this;
	}

	/**
	* Returns a IN(...) list
	* @param array $in_array Array with the elements to place in the list
	* @param bool $is_int If true, will treat the elements from the list as int values
	* @param array $params Variable which will receive the values to be used in the prepared statement [out]
	* @return string
	*/
	public function getIn(array $in_array, bool $is_int = true, ?array &$params = [])
	{
		return ' IN(' . $this->getInList($in_array, $is_int, $params) . ')';
	}

	/**
	* Returns an items list
	* @param array $list_array Array with the elements to place in the list
	* @param bool $is_int If true, will treat the elements from the list as int values
	* @param array $params Variable which will receive the values to be used in the prepared statement [out]
	* @return string
	*/
	public function getInList(array $list_array, bool $is_int = true, ?array &$params = []) : string
	{
		static $index = 0;
		$params = [];
		$is_empty = '0';

		if (!$list_array) {
			return $is_empty;
		}

		if (!$is_int) {
			$index++;
			$is_empty = '';
		}

		$i = 1;
		$vals = [];

		foreach ($list_array as $val) {
			if ($is_int) {
				$vals[] = (int)$val;
			} else {
				$key = 'in_' . $index . '_' . $i;

				$vals[] = ':' . $key;
				$params[$key] = $val;
			}

			$i++;
		}

		return implode(',', $vals);
	}

	/**
	* Returns either '= $values', if count($values) is 1 OR 'IN($values)' otherwise
	* @param int|string|array $values The values
	* @param bool $is_int If true, will treat the elements from $in_array as int values
	* @param array $params Variable which will receive the values to be used in the prepared statement [out]
	* @return string
	*/
	public function getEqualOrIn(int|string|array $values, bool $is_int = true, ?array &$params = []) : string
	{
		$params = [];

		if (!is_array($values)) {
			$values = [$values];
		}

		if (count($values) > 1) {
			return $this->getIn($values, $is_int, $params);
		}

		$val = reset($values);

		if ($is_int) {
			return ' = ' . (int)$val;
		} else {
			static $index = 0;
			$index++;

			$key = 'eq_' . $index;

			$params[$key] = $val;

			return ' = :' . $key;
		}
	}

	/**
	* Builds a HAVING statement
	* @param string|array $where Either array or string. If array the format must be: column => value or column => [value,operator,alias]. If string, the name of the column
	* @param string $value The value
	* @param string $operator
	* @param string alias
	* @param string $delimitator The delimitator to use between parts. By default AND is used.
	* @return $this
	*/
	public function having(string|array $where, string $value = '', string $operator = '=', string $alias = '', string $delimitator = 'AND')
	{
		if (!$where) {
			return $this;
		}

		$this->sql.= $this->getDelimitator('HAVING', $delimitator);

		if (is_array($where)) {
			$this->sql.= $this->getConditions($where, $delimitator);
		} else {
			$this->sql.= $this->getCondition($where, $value, $operator, $alias, false);
		}

		return $this;
	}

	/**
	* Returns an ORDER BY statement
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @return $this
	*/
	public function orderBy(string $order_by, string $order = '')
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
	* Returns a GROUP BY statement
	* @param string $group_by The group by column
	* @return $this
	*/
	public function groupBy(string $group_by)
	{
		if (!$group_by) {
			return $this;
		}

		$group_by = $this->escapeColumn($group_by);

		$this->sql.= " GROUP BY {$group_by}";

		return $this;
	}

	/**
	* Returns a LIMIT statement
	* @param int $count The number of items
	* @param int int The offset, if any
	* @return $this
	*/
	public function limit(int $count, int $offset = 0)
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
	* Returns LIMIT corresponding to the current page
	* @param int $page The page number of the current page
	* @param int $page_items Items per page
	* @param int $total_items The total number of items.
	* @return $this
	*/
	public function pageLimit(int $page = 0, int $page_items = 0, int $total_items = 0)
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

		$this->sql.= " LIMIT {$offset}, {$page_items}";

		return $this;
	}

	/******************** GET METHODS ***********************************/

	/**
	* Executes the query and returns the results
	* @see \Mars\Db::get()
	*/
	public function get(string $key_field = '', ?string $field = null, bool $load_array = false, string $class_name = '') : array
	{
		$this->app->db->readQuery($this);

		return $this->app->db->get($key_field, $field, $load_array, $class_name);
	}

	/**
	* Executes the query and returns the results as an array.
	* @see \Mars\Db::getArray()
	*/
	public function getArray(string $key_field = '', ?string $field = null) : array
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getArray($key_field, $field);
	}

	/**
	* Executes the query and returns the results as objects
	* @see \Mars\Db::getObjects()
	*/
	public function getObjects(string $class_name = '') : array
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getObjects($class_name);
	}

	/**
	* Executes the query and returns the first column from the generated results
	* @see \Mars\Db::getFields()
	*/
	public function getFields() : array
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getFields();
	}

	/**
	* Executes the query and returns a list from the generated results.
	* @see \Mars\Db::getList()
	*/
	public function getList(string $key_field = '', string $field = '') : array
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getList($key_field, $field);
	}

	/**
	* Executes the query and returns the first row generated by a query.
	* @see \Mars\Db::getRow()
	*/
	public function getRow(bool $load_array = false, string $class_name = '')
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getRow($load_array, $class_name);
	}

	/**
	* Alias for get_object
	* @see \Mars\Db::getObject()
	*/
	public function getObject(string $class_name = '') : ?object
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getObject($class_name);
	}

	/**
	* Executes the query and returns the first column from the first row
	* @see \Mars\Db::getResult()
	*/
	public function getResult() : ?string
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getResult();
	}

	/**
	* Executes the query and return of a count query
	* @see \Mars\Db::getCount()
	* @return int
	*/
	public function getCount() : int
	{
		$this->app->db->readQuery($this);

		return $this->app->db->getCount();
	}
}
