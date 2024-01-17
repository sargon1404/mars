<?php
/**
* The Base Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The Base Request Class
 * Base class for the Request classes
 */
abstract class Base
{

	/**
	 * The data to read from
	 * @param array $data
	 */
	protected array $data = [];

	/**
	 * Determines if a variable with this name is set
	 * @param string $name The name of the  variable
	 * @return bool
	 */
	public function has(string $name) : bool
	{
		return isset($this->data[$name]);
	}

	/**
	 * Returns the value of a variable
	 * @param string $name The name of the variable
	 * @param string $filter The filter to apply to the value, if any. See class Filter for a list of filters
	 * @param bool $is_array If true, will force the returned value to an array
	 * @return mixed The value
	 */
	public function get(string $name, string $filter = '', bool $is_array = false)
	{
		$value = $this->data[$name] ?? '';

		if ($is_array) {
			$value = App::array($value);
		}

		$value = $this->app->filter->trim($value);

		if ($filter) {
			$value = $this->app->filter->value($value, $filter);
		}

		return $value;
	}

	/**
	 * Returns the raw value of a variable
	 * @param string $name The name of the variable
	 * @return mixed The value
	 */
	public function getRaw(string $name)
	{
		return $this->data[$name] ?? '';
	}

	/**
	 * Returns all the request data
	 * @return array
	 */
	public function getAll() : array
	{
		return $this->data;
	}

	/**
	 * Sets the value of a variable
	 * @param string $name The name of the variable
	 * @param mixed $value The value
	 * @return static
	 */
	public function set(string $name, $value) : static
	{
		$this->data[$name] = $value;

		return $this;
	}

	/**
	 * Unsets a variable
	 * @param string $name The name of the variable
	 * @return static
	 */
	public function unset(string $name) : static
	{
		if (isset($this->data[$name])) {
			unset($this->data[$name]);
		}

		return $this;
	}

	/**
	 * For each element in the $data array, will check if a corresponding value with the same name exists in the post/get data.
	 * If it does, will set the value in the $data to those values.
	 * @param array|object $fill The data to be filled
	 * @param array $filters Array listing the filters to apply to fields
	 * @param array $array_fields Fields which can be filled with arrays
	 * @param array $ignore_fields Fields to ignore when filling.
	 * @param string $key_prefix Key prefix to use on the request data used when filling, if any
	 * @return array|object Returns the filled $data
	 */
	public function fill(array|object $fill, array $filters = [], array $array_fields = [], array $ignore_fields = [], string $key_prefix = '') : array|object
	{
		if (!$fill) {
			return $fill;
		}

		$is_array = is_array($fill);

		foreach ($fill as $key => $val) {
			$index = $key_prefix . $key;

			if (in_array($key, $ignore_fields)) {
				continue;
			}
			if (!isset($this->data[$index])) {
				continue;
			}

			$filter = $filters[$key] ?? '';

			$value = $value = $this->get($index, $filter, in_array($index, $array_fields));

			if ($is_array) {
				$fill[$key] = $value;
			} else {
				$fill->$key = $value;
			}
		}

		return $fill;
	}
}
