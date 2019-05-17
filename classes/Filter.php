<?php
/**
* The Filter Class
* @package Mars
*/

namespace Mars;

/**
* The Filter Class
* Filters values
*/
class Filter
{
	use AppTrait;

	/**
	* Returns the filtered $value based on $filter
	* @param mixed $value The value to filter
	* @param string $filter The filter type to be applied. Eg: int/float/url/email etc..
	* @return mixed The filtered value
	*/
	public function value($value, string $filter = '')
	{
		switch ($filter) {
			case 'e':
			case 'escape':
			case 'html':
				return $this->app->escape->html($value);
			case 'i':
			case 'int':
				return $this->int($value);
			case 'iabs':
				return abs($this->int($value));
			case 'ipos':
				return $this->intPos($value);
			case 'f':
			case 'fl':
			case 'float':
				return $this->float($value);
			case 'fabs':
				return abs($this->float($value));
			case 'fpos':
				return $this->floatPos($value);
			case 'trim':
				return $this->trim($value);
			case 'id':
				return $this->id($value);
			case 'ids':
				return $this->ids($value);
			case 'abs':
				return $this->abs($value);
			case 'url':
				return $this->url($value);
			case 'slug':
				return $this->slug($value);
			case 'file':
			case 'filename':
				return $this->filename($value);
			case 'filepath':
				return $this->filepath($value);
			case 'strip_tags':
			case 'strip':
			case 'no_tags':
				return $this->stripTags($this->string($value));
			case 'alpha':
				return $this->alpha($value);
			case 'alphanumeric':
				return $this->alphanumeric($value);
			default:
				return $this->string($value);
		}
	}

	/**
	* Filters values from array $values_array using $filter_array. $filter_array must be in the format key=>filter_type [where key is the key in $values_array which will be filtered]
	* @param array $values_array Array containing the filter values. Each key in $values_array must have a corresponding entry in $filter_array
	* @param array $filter_array Array containing the filter types. Must be in the format key=>filter_type [where key is the key in $values_array which will be filtered]
	* @return array The filtered values
	*/
	public function values(array &$values_array, array $filter_array) : array
	{
		if (!$filter_array) {
			return $values_array;
		}

		foreach ($filter_array as $key => $filter) {
			if (!isset($values_array[$key])) {
				continue;
			}

			$values_array[$key] = $this->value($values_array[$key], $filter);
		}

		return $values_array;
	}

	/**
	* Returns the default value of a filter
	* @param string $filter The filter type to be applied. Eg: int/float/url/email etc..
	*/
	public function defaultValue(string $filter)
	{
		switch ($filter) {
			case 'i':
			case 'int':
			case 'f':
			case 'fl':
			case 'float':
			case 'id':
				return 0;
			case 'ids':
				return [];
			default:
				return '';
		}
	}

	/**
	* Filters all non-alphanumeric chars from $value
	* @param mixed $value The value (string|array)
	* @param bool $space If true, will allow spaces
	* @return mixed The filtered string value
	*/
	public function alphanumeric($value, bool $space = false)
	{
		if (is_array($value)) {
			return array_map([$this, 'alphanumeric'], $value, $space);
		}

		$pattern = "/[^0-9a-z]/i";
		if ($space) {
			$pattern = "/[^0-9a-z ]/i";
		}

		return preg_replace($pattern, '', trim($value));
	}

	/**
	* Filters all non-a-z chars from $value
	* @param mixed $value The value (string|array)
	* @param bool $space If true, will allow spaces
	* @return mixed The filtered string value
	*/
	public function alpha($value, bool $space = false)
	{
		if (is_array($value)) {
			return array_map([$this, 'alpha'], $value, $space);
		}

		$pattern = "/[^a-z]/i";
		if ($space) {
			$pattern = "/[^a-z ]/i";
		}

		return preg_replace($pattern, '', trim($value));
	}

	/**
	* Filters a string value
	* @param mixed $value The value (string|array)
	* @return mixed The filtered string value
	*/
	public function string($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'string'], $value);
		}

		return (string)$value;
	}

	/**
	* Filters an int value
	* @param mixed $value The value (int|array)
	* @return mixed The filtered int value
	*/
	public function int($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'int'], $value);
		}

		return (int)$value;
	}

	/**
	* Filters an int positive number
	* @param mixed $value The value (int|array)
	* @param int $min If $value is <= 0 will return $min
	* @return mixed The filtered int value
	*/
	public function intPos($value, int $min = 1)
	{
		if (is_array($value)) {
			return array_map([$this, 'intPos'], $value);
		}

		$value = (int)$value;
		if ($value <= 0) {
			$value = $min;
		}

		return $value;
	}

	/**
	* Filters a float value
	* @param mixed $value The value (int|array)
	* @return mixed The filtered float value
	*/
	public function float($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'float'], $value);
		}

		return (float)str_replace(',', '.', $value);
	}

	/**
	* Filters a float positive number
	* @param mixed $value The value (int|array)
	* @param float $min If $value is <= 0 will return $min
	* @return mixed The filtered float value
	*/
	public function floatPos($value, float $min = 1)
	{
		if (is_array($value)) {
			return array_map([$this, 'floatPos'], $value);
		}

		$value = $this->float($value);
		if ($value <= 0) {
			$value = $min;
		}

		return $value;
	}

	/**
	* Returns the absolute value of $value
	* @param mixed $value The value (int|float|array)
	* @return mixed The absolute value of $value
	*/
	public function abs($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'abs'], $value);
		}

		return abs($value);
	}

	/**
	* Trims a value
	* @param mixed $value The value (string|array)
	* @return mixed The filtered value
	*/
	public function trim($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'trim'], $value);
		}

		return trim($value);
	}

	/**
	* Strips the tags from $value
	* @param mixed $value The value (string|array)
	* @return string The filtered value
	*/
	public function stripTags($value)
	{
		if (is_array($value)) {
			return array_map([$this, 'stripTags'], $value);
		}

		return strip_tags($value);
	}

	/**
	* Filters an id value
	* @param mixed $value The value
	* @return int The filtered ID value
	*/
	public function id($value) : int
	{
		if (is_array($value)) {
			$value = reset($value);
		}

		return abs((int)$value);
	}

	/**
	* Filters IDs values
	* @param mixed $values Array or comma delimited values
	* @return array The filtered IDS value
	*/
	public function ids($values) : array
	{
		if (!is_array($values)) {
			$values = explode(',', $values);
		}

		foreach ($this->values as $key => $value) {
			$values[$key] = abs((int)$value);
		}

		return $values;
	}

	/**
	* Checks that $value is in the $min_value - $max_value interval. If it is, it returns $value. If not returns $default_value
	* @param numeric $value The value
	* @param numeric $min_value The min. value
	* @param numeric $max_value The max. value
	* @param numeric $default_value The value to return if $value is not in the $min-$max interval
	* @return numeric The value
	*/
	public function interval($value, $min_value, $max_value, $default_value)
	{
		if ($value >= $min_value && $value <= $max_value) {
			return $value;
		} else {
			return $default_value;
		}
	}

	/**
	* Filters an url
	* @param mixed $url The url to filter (string|array)
	* @return string The filtered url
	*/
	public function url($url)
	{
		if (is_array($url)) {
			return array_map([$this, 'url'], $url);
		}

		return filter_var(trim($url), FILTER_SANITIZE_URL);
	}

	/**
	* Filters an email address
	* @param string $email The email to filter (string|array)
	* @return string The filtered email
	*/
	public function email(string $email)
	{
		if (is_array($email)) {
			return array_map([$this, 'email'], $email);
		}

		return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
	}

	/**
	* Filters a slug param
	* @param mixed $param The param to escape
	* @param bool $allow_slash If true will allow slashes in the returned value
	* @return string The filtered slug
	*/
	public function slug($param, bool $allow_slash = false)
	{
		if (is_array($param)) {
			return array_map([$this, 'slug'], $param, $allow_slash);
		}

		$original_param = $param;

		$reg = '/[^0-9a-zA-Z_-]+/';
		if ($allow_slash) {
			$reg = '/[^0-9a-zA-Z_\/-]+/';
		}

		$param = strtolower(trim($param));
		$param = str_replace([' ', ':', ',', "'", '`', '@', '|', '"', '_'], '-', $param);
		$param = preg_replace($reg, '', $param);

		//replace multiple dashes with just one
		$param = preg_replace('/-+/', '-', $param);

		$param = urlencode($param);

		if ($allow_slash) {
			$param = str_replace('%2F', '/', $param);
		}

		$param = trim($param, '-');

		$param = $this->app->plugins->filter('filterSlug', $param, $original_param, $allow_slash, $this);

		return $param;
	}

	/**
	* Filters a file name for invalid characters
	* @param mixed $filename The filename to filter
	* @param bool $is_path If true will treat $filename as a path rather than just a filename
	* @return string The filtered filename
	*/
	public function filename($filename, bool $is_path = false)
	{
		if (is_array($filename)) {
			return array_map([$this, 'filename'], $filename);
		}

		$max_chars = 300;
		$filename = trim($filename);
		$search =[
			'../', './', '/..', '/.', '..\\', '.\\', '\\..', '\\.' ,'php:',
			'<', '>', '[', ']', '(', ')', '{', '}', '\\', '*', '?', ':', ';',
			'$', '%', '*', '+', '#', '~', '&', '\'' ,'`', '=', '|', '!', chr(0)
		];

		if ($is_path) {
			//replace multiple slashes with just one
			$filename = preg_replace('/\/+/', '/', $filename);
		} else {
			$filename = basename($filename);
			$search[] = '/';
		}

		//filter the non-allowed chars
		$filename = str_replace($search, '', $filename);

		//filter non-ascii chars
		$reg = '/[\x00-\x1F\x80-\xFF]/';
		$filename = preg_replace($reg, '', $filename);

		//does the filename have more than 1 extension?
		$pos = strrpos($filename, '.');
		$part1 = substr($filename, 0, $pos);
		$part2 = substr($filename, $pos);

		$part1 = str_replace('.', '_', $part1);
		if (!$is_path) {
			$part1 = substr($part1, 0, $max_chars);
		}

		return $part1 . $part2;
	}

	/**
	* Alias for filename
	* @param string $filename The filename to filter
	* @return string The filtered filename
	*/
	public function file(string $filename) : string
	{
		return $this->filename($filename, false);
	}

	/**
	* Filters a filepath
	* @param string $filepath The filepath
	* @return string The filtered filepath
	*/
	public function filepath(string $filepath) : string
	{
		return $this->filename($filepath, true);
	}

	/**
	* Removes from $value the $remove_value element
	* @param array $value Array with the values
	* @param mixed $remove_value The value(s) to remove (string|array)
	* @return array Array with the filtered values
	*/
	public function remove(array $value, $remove_value) : array
	{
		if (!is_array($remove_value)) {
			$remove_value = [$remove_value];
		}

		foreach ($value as $i => $val) {
			if (in_array($val, $remove_value)) {
				unset($value[$i]);
			}
		}

		return $value;
	}

	/**
	* Removes from $value the elements which aren't found in $allowed
	* @param mixed $value The values (string|array)
	* @param mixed $allowed Array with the allowed elements (string|array)
	* @param mixed $not_exists_value The value returned if $value isn't included in $allowed
	* @return mixed Array with the filtered values
	*/
	public function exists($value, $allowed, $not_exists_value = false)
	{
		if (!is_array($allowed)) {
			$allowed = [$allowed];
		}

		if (is_array($value)) {
			foreach ($value as $i => $val) {
				if (!in_array($val, $allowed)) {
					unset($value[$i]);
				}
			}

			return $value;
		} else {
			if (in_array($value, $allowed)) {
				return $value;
			} else {
				return $not_exists_value;
			}
		}
	}
}
