<?php
/**
* The App Functions Trait
* @package Mars
*/

namespace Mars;

/**
* The App Functions
* Contains the App static functions
*/
trait AppFunctionsTrait
{
	/**
	* Converts special chars. to html entitites
	* @param string $value The value to escape
	* @return string The escaped value
	*/
	public static function e(?string $value) : string
	{
		return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
	}

	/**
	* Double escapes a value
	* @param string $value The value to escape
	* @param bool $nl2br If true, will apply nl2br to value
	* @return string The double escaped value
	*/
	public static function ex2(?string $value, bool $nl2br = true) : string
	{
		$value = static::e(static::e($value));

		if ($nl2br) {
			return nl2br($value);
		}

		return $value;
	}

	/**
	* Decodes the html special entities
	* @param string $value The value to decode
	* @return string The decoded value
	*/
	public static function de(?string $value) : string
	{
		return htmlspecialchars_decode($value);
	}

	/**
	* Escapes and outputs $value
	* @param string $value The value to escape and output
	*/
	public static function ee(?string $value)
	{
		echo static::e($value);
	}

	/**
	* Escapes text meant to be written as javascript code. Replaces ' with \' and \n with empty space
	* @param string $value The value to escape
	* @param bool $escape_html If true will call html escape the value
	* @return string The escaped value
	*/
	public static function ejs(string $value, bool $escape_html = true) : string
	{
		$value = str_replace(['\\', "'", '"', "\n", "\r"], ['\\\\', "\\'", '\\"', '', ''], $value);

		if ($escape_html) {
			$value = static::e($value);
		}

		return $value;
	}

	/**
	* Escapes text which will be used inside javascript <script> tags
	* @param string $value The value to escape
	* @param bool $nl2br If true, will apply nl2br to value
	* @return string The escaped value
	*/
	public static function ejsc(string $value, bool $nl2br = true) : string
	{
		if ($nl2br) {
			$value = nl2br($value);
		}

		return static::ejs($value, false);
	}

	/**
	* Returns a language string
	* @param string|array $str The string index as defined in the languages file. Either string or array
	* @param array $replace Array with key & values to be used for to search & replace
	* @return string|array The language string(s)
	*/
	public static function __(string|array $str, array $replace = []) : string|array
	{
		$search_array = [];
		$replace_array = [];
		if ($replace) {
			$search_array = array_keys($replace);
			$replace_array = $replace;
		}

		if (is_array($str)) {
			$strings = [];
			foreach ($str as $string) {
				$string = static::$instance->lang->strings[$string] ?? $string;

				if ($replace) {
					$string = str_replace($search_array, $replace_array, $string);
				}

				$strings[] = $string;
			}

			return $strings;
		} else {
			$str = static::$instance->lang->strings[$str] ?? $str;

			if ($replace) {
				$str = str_replace($search_array, $replace_array, $str);
			}

			return $str;
		}
	}

	/**
	* Alias for AppFunctionsTrait::__()
	* @see AppFunctionsTrait::__()
	*/
	public static function str($str, $replace = [])
	{
		return static::__($str, $replace);
	}

	/**
	* Returns a string based on the count of $items.
	* @param array $items The items to count
	* @param string $str_single If count($items) == 1 will return $this->app->lang->strings[$str_single]
	* @param string $str_multi If count($items) == 1 will return $this->app->lang->strings[$str_multi]. Will also replace {COUNT} with the actual count number
	* @param string $count_str The part which will be replaced with the count number. Default: {COUNT}
	* @return string
	*/
	public static function strc(array $items, string $str_single, string $str_multi, string $count_str = '{COUNT}') : string
	{
		$count = count($items);
		if ($count == 1) {
			return static::str($str_single, []);
		} else {
			return static::str($str_multi, [$count_str => $count]);
		}
	}

	/**
	* Escapes a language string. Shorthand for e(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search&replace
	* @return string
	*/
	public static function estr(string $str, array $replace = []) : string
	{
		return static::__($str, $replace);
	}

	/**
	* Javascript escapes a language string. Shorthand for ejs(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search&replace
	* @return string
	*/
	public function ejsstr(string $str, array $replace = []) : string
	{
		return static::ejs(static::__($str, $replace));
	}

	/**
	* Encodes data
	* @param mixed $data The data to encode
	* @return string The encoded string
	*/
	public static function encode($data) : string
	{
		if (!$data) {
			return '';
		}

		return json_encode($data);
	}

	/**
	* Decodes a string
	* @param string $string The string to decode
	* @return mixed The decoded data
	*/
	public static function decode(string $string)
	{
		if (!$string) {
			return '';
		}

		return json_decode($string, true);
	}

	/**
	* Returns a random string
	* @param int $max The maximum number of chars. the string should have
	* @return string A random string
	*/
	public static function randStr(int $max = 20) : string
	{
		$str = bin2hex(random_bytes($max));

		return substr($str, 0, $max);
	}

	/**
	* Returns a random number
	* @param int $min Lowest value to be returned
	* @param int $max Highest value to be returned
	* @return int A random number
	*/
	public static function randInt(int $min = 0, int $max = 0) : int
	{
		return random_int($min, $max);
	}

	/**
	* If $data is not empty will serialize it. Otherwise will return $default_value
	* @param mixed $data The data to serialize
	* @param mixed $default_value The value to return if $data is empty
	* @param bool $encode If true, the serialized content will be base64 encoded
	* @return string The serialized string
	*/
	public static function serialize($data, $default_value = '', bool $encode = true) : string
	{
		if ($data) {
			if ($encode) {
				return base64_encode(serialize($data));
			} else {
				return serialize($data);
			}
		} else {
			return $default_value;
		}
	}

	/**
	* Will unserialize a value
	* @param string $value The serialized value
	* @param mixed $default_value The value to return if $value is empty
	* @param string $decode If true, will base64 decode the result
	* @return mixed The unserialized data
	*/
	public static function unserialize($value, $default_value = [], bool $decode = true)
	{
		if (!$value) {
			return $default_value;
		}
		if (!is_string($value)) {
			return $value;
		}

		if ($decode) {
			return unserialize(base64_decode($value));
		} else {
			return unserialize($value);
		}
	}

	/**
	* Adds a slash at the end of the filename. Will add it, only if it's not already there
	* @param string $filename The filename
	* @return string The filename with an ending slash
	*/
	public static function sl(string $filename) : string
	{
		if (!$filename) {
			return '';
		}

		return rtrim($filename, '/') . '/';
	}

	/**
	* Removes a slash from the end of the filename
	* @param string $filename The filename
	* @return string The filename without the slash
	*/
	public static function usl(string $filename) : string
	{
		return rtrim($filename, '/');
	}

	/**
	* Returns the public properties of an object
	* @param object $object The object
	* @return array The properties
	*/
	public static function getObjectVars(object $object) : array
	{
		return get_object_vars($object);
	}

	/**
	* Returns an array out of a string/array
	* @param array|string $array The data to return the array from
	* @return array The array
	*/
	public static function getArray($array) : array
	{
		if (is_array($array)) {
			return $array;
		} else {
			return [$array];
		}
	}

	/**
	* Returns a property of an object or an array value
	* @param string $name The name of the property/index
	* @param array|object $data The data to return the property from
	* @return mixed The property
	*/
	public static function getProperty(string $name, array|object $data)
	{
		if (is_array($data)) {
			return $data[$name] ?? null;
		} else {
			return $data->$name ?? null;
		}
	}

	/**
	* Converts an array to a stdClass object
	* @param array $array The array to convert
	* @return object
	*/
	public static function toObject(array $array) : object
	{
		return (object)$array;
	}

	/**
	* Returns an array from a SplFixedArray object or from an array
	* @param mixed $array The array (array,SplFixedArray,Item)
	* @return array
	*/
	public static function toArray($array) : array
	{
		if (is_array($array)) {
			return $array;
		} elseif (is_object($array)) {
			return get_object_vars($array);
		} elseif (is_iterable($array)) {
			return iterator_to_array($array);
		} else {
			return (array)$array;
		}
	}

	/**
	* Merges all the sub-arrays from $array into a single array
	* @param array $array The array
	*/
	public static function arrayMerge(array $array) : array
	{
		$new_array = [];
		foreach ($array as $item) {
			$new_array = array_merge($new_array, $item);
		}

		return $new_array;
	}

	/**
	* Unsets from $array the specified keys
	* @param array $array The array
	* @param string|array The keys to unset
	* @return array The array
	*/
	public static function arrayUnset(array &$array, string|array $keys) : array
	{
		if (!is_array($keys)) {
			$keys = [$keys];
		}

		foreach ($keys as $key) {
			if (isset($array[$key])) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	* Pad a string with a leading space
	* @param string $string The string
	* @param bool $left If true, will pad the string from the length
	* @param int $length The number of chars to pad with
	* @param string $char The char to pad with
	* @return string
	*/
	public static function pad(string $string, bool $left = true, int $length = 1, string $char = ' ') : string
	{
		return str_pad($string, $length, $char, ($left ? STR_PAD_LEFT : STR_PAD_RIGHT));
	}

	/**
	* Pads a number with a leading 0 if it's below 10. Eg: if $number = 6 returns 06
	* @param int $number The number
	* @return string The number with a leading 0
	*/
	public static function padInt(int $number) : string
	{
		if ($number < 10) {
			return '0' . $number;
		}

		return $number;
	}

	/**
	* Converts a string to a class name. Eg: some-action => SomeAction
	* @param string $str The string to convert
	* @return string The class name
	*/
	public static function strToClass(string $str) : string
	{
		$str = preg_replace('/[^a-z0-9\- ]/i', '', $str);
		$str = str_replace(' ', '-', $str);

		$str = ucwords($str, '-');
		$str = str_replace('-', '', $str);

		return $str;
	}

	/**
	* Converts a string to a method name. Eg: some-action => someAction
	* @param string $str The string to convert
	* @return string The class name
	*/
	public static function strToMethod(string $str) : string
	{
		if (!$str) {
			return $str;
		}

		$str = static::strToClass($str);

		$str[0] = strtolower($str[0]);

		return $str;
	}

	/**
	* Does a print_r on $var and outputs <pre> tags
	* @param mixed $var The variable
	* @param bool $escape_html If true, will call html escape $var before calling print_r
	* @param bool $die If true, will call die after
	*/
	public static function pp($var, bool $escape_html = false, bool $die = true)
	{
		if ($escape_html) {
			$var = static::e($var);
		}

		echo '<pre>';
		\print_r($var);
		echo '</pre>';

		if ($die) {
			die;
		}
	}

	/**
	* Alias for pp
	* @see AppFunctionsTrait::pp()
	*/
	public static function print_r($var, bool $escape_html = false, bool $die = true)
	{
		static::pp($var, $escape_html, $die);
	}

	/**
	* Prints the debug backtrace
	*/
	public static function backtrace()
	{
		echo '<pre>';
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		echo '</pre>';

		die;
	}
}
