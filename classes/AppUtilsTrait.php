<?php
/**
* The App Utils Trait
* @package Mars
*/

namespace Mars;

/**
* The App Utils Trait
* Trait containing utility methods
*/
trait AppUtilsTrait
{
	/**
	* Returns a language string
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string The language string
	*/
	public static function __(string $str, array $replace = []) : string
	{
		$str = static::$instance->lang->strings[$str] ?? $str;

		if ($replace) {
			$str = str_replace(array_keys($replace), $replace, $str);
		}

		return $str;
	}

	/**
	* Returns a string based on the count of $items.
	* @param string $str_single If count($items) == 1 will return $this->app->lang->strings[$str_single]
	* @param string $str_multi If count($items) == 1 will return $this->app->lang->strings[$str_multi]. Will also replace {COUNT} with the actual count number
	* @param Countable $items The items to count
	* @param string $count_str The part which will be replaced with the count number. Default: {COUNT}
	* @return string
	*/
	public static function __c(string $str_single, string $str_multi, \Countable $items, string $count_str = '{COUNT}') : string
	{
		$count = count($items);
		if ($count == 1) {
			return static::__($str_single, []);
		} else {
			return static::__($str_multi, [$count_str => $count]);
		}
	}

	/**
	* Escapes a language string. Shorthand for e(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string
	*/
	public static function __e(string $str, array $replace = []) : string
	{
		return static::e(static::__($str, $replace));
	}

	/**
	* Javascript escapes a language string. Shorthand for ejs(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string
	*/
	public function __ejs(string $str, array $replace = []) : string
	{
		return static::ejs(static::__($str, $replace));
	}

	/**
	* Adds a slash at the end of a path, if it's not already there
	* @param string $path The path
	* @return string The path
	*/
	public static function fixPath(string $path) : string
	{
		if (!$path) {
			return '';
		}

		return rtrim($path, '/') . '/';
	}

	/**
	* Converts a string to a class name. Eg: some-action => SomeAction
	* @param string $str The string to convert
	* @return string The class name
	*/
	public static function getClass(string $str) : string
	{
		$str = preg_replace('/[^a-z0-9\- ]/i', '', $str);
		$str = str_replace(' ', '-', $str);

		$str = ucwords($str, '-');
		$str = str_replace('-', '', $str);

		return $str;
	}

	/**
	* Determines if the property exists
	* @param array|object $data The data to return the property from
	* @param string $name The name of the property/index
	* @return mixed The property
	*/
	public static function hasProperty(array|object $data, string $name) : bool
	{
		if (is_array($data)) {
			return isset($data[$name]);
		} else {
			return isset($data->$name);
		}
	}

	/**
	* Returns a property of an object or an array value
	* @param array|object $data The data to return the property from
	* @param string $name The name of the property/index
	* @return mixed The property
	*/
	public static function getProperty(array|object $data, string $name)
	{
		if (is_array($data)) {
			return $data[$name] ?? null;
		} else {
			return $data->$name ?? null;
		}
	}

	/**
	* Returns a list of properties of an object or an array value
	* @param array|object $data The data to return the property from
	* @param array $properties The name of the properties to return
	* @return array The properties
	*/
	public static function getProperties(array|object $data, array $properties = []) : array
	{
		$properties_array = [];

		if ($properties) {
			foreach ($properties as $name) {
				if (static::hasProperty($data, $name)) {
					$properties_array[$name] = static::getProperty($data, $name);
				}
			}
		} else {
			$properties_array = (array)$data;
		}

		return $properties_array;
	}

	/**
	* Removes the specified properties from the data
	* @param array $properties The name of the properties to remove
	* @return array The properties
	*/
	public static function filterProperties(array|object $data, array $properties) : array
	{
		return static::unset((array)$data, $properties);
	}

	/**
	* Returns an array from an array/object/iterator
	* @param mixed $array The array
	* @return array
	*/
	public static function array($array) : array
	{
		if (!$array) {
			return [];
		}

		if (is_array($array)) {
			return $array;
		} elseif (is_iterable($array)) {
			return iterator_to_array($array);
		} elseif (is_object($array)) {
			return get_object_vars($array);
		} else {
			return (array)$array;
		}
	}

	/**
	* Unsets from $array the specified keys
	* @param array $array The array
	* @param string|array The keys to unset
	* @return array The array
	*/
	public static function unset(array $array, string|array $keys) : array
	{
		$keys = (array)$keys;

		foreach ($keys as $key) {
			if (isset($array[$key])) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	* Pads a number with a leading 0 if it's below 10. Eg: if $number = 6 returns 06
	* @param int $number The number
	* @return string The number with a leading 0
	*/
	public static function padInt(int $number) : string
	{
		return str_pad($number, 2, '0', STR_PAD_LEFT);
	}
}
