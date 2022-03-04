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
	use SupportedRulesTrait;

	/**
	* @var array $supported_rules The list of suported rules
	*/
	protected array $supported_rules = [
		'string' => ['string'],
		'int' => ['int'],
		'float' => ['float'],
		'abs' => ['abs'],
		'id' => ['id'],
		'trim' => ['trim'],
		'tags' => ['tags'],
		'html' => '\Mars\Filters\Html',
		'alpha' => '\Mars\Filters\Alpha',
		'alnum' => '\Mars\Filters\Alnum',
		'filename' => '\Mars\Filters\Filename',
		'filepath' => '\Mars\Filters\Filepath',
		'url' => '\Mars\Filters\Url',
		'email' => '\Mars\Filters\Email',
		'slug' => '\Mars\Filters\Slug',
		'interval' => '\Mars\Filters\Interval',
		'exists' => '\Mars\Filters\Exists',
	];

	/**
	* Filters a string value
	* @param $value The value to filter
	* @return string|array The filtered value
	*/
	public function string($value) : string|array
	{
		return $this->map($value, function($value) {
			return (string)$value;
		});
	}

	/**
	* Filters an int value
	* @param $value The value to filter
	* @return int|array The filtered value
	*/
	public function int($value) : int|array
	{
		return $this->map($value, function($value) {
			return (int)$value;
		});
	}

	/**
	* Filters a float value
	* @param $value The value to filter
	* @return float|array The filtered value
	*/
	public function float($value) : float|array
	{
		return $this->map($value, function($value) {
			return (float)$value;
		});
	}

	/**
	* Returns an absolue value
	* @param $value The value to filter
	* @return int|float|array The filtered value
	*/
	public function abs($value) : int|float|array
	{
		return $this->map($value, function($value) {
			return abs($value);
		});
	}

	/**
	* Trims a value
	* @param string|array $value The value
	* @return string|array The filtered value
	*/
	public function trim($value) : string|array
	{
		return $this->map($value, function($value) {
			return trim($value);
		});
	}

	/**
	* Strips the tags from $value
	* @param string|array $value The value
	* @param array|string|null $allowed_tags The tags which should not be removed, if any
	* @return string|array The filtered value
	*/
	public function tags($value, array|string|null $allowed_tags = null) : string|array
	{
		return $this->map($value, function($value) use ($allowed_tags) {
			return strip_tags($value, $allowed_tags);
		});
	}

	/**
	* Filters html using HtmlPurifier
	* @param string $html The $text to filter
	* @param string $allowed_elements String containing the allowed html elements. If null, it will be read from config->html_allowed_elements
	* @param string $allowed_attributes The allowed attributes. If null, it will be read from config->html_allowed_attributes
	* @param string $encoding The encoding of the text
	* @return string The filtered html
	*/
	public function html(string $html, ?string $allowed_elements = null, ?string $allowed_attributes = null, string $encoding = 'UTF-8') : string
	{
		return $this->value($html, 'html', $allowed_elements, $allowed_attributes, $encoding);
	}

	/**
	* Filters an id value
	* @param int|array $value The value
	* @return int|array The filtered ID value
	*/
	public function id(int|array $value) : int|array
	{
		return $this->map($value, function($value) {
			return abs((int)$value);
		});
	}

	/**
	* Alias for id()
	* @param array $value The value
	* @return array The filtered ID value
	*/
	public function ids(array $value) : array
	{
		return $this->id($value);
	}

	/**
	* Filters all non alphabetic chars.
	* @param string|array $value The value
	* @param bool $space If true, will allow spaces
	* @return string|array The filtered value
	*/
	public function alpha(string|array $value, bool $space = false) : string|array
	{
		return $this->value($value, 'alpha', $space);
	}

	/**
	* Filters all non-alphanumeric chars from $value
	* @param string|array  $value The value
	* @param bool $space If true, will allow spaces
	* @return string|array The filtered value
	*/
	public function alnum(string|array $value, bool $space = false) : string|array
	{
		return $this->value($value, 'alnum', $space);
	}

	/**
	* Filters a filename
	* @param string|array $value The filename to filter
	* @return string|array The filtered filename
	*/
	public function filename(string|array $value) : string|array
	{
		return $this->value($value, 'filename');
	}

	/**
	* Filters a filepath
	* !!Only the filename if filtered, the rest of the filepath is left untouched
	* @param string|array $value The filepath to filter
	* @return string|array The filtered filepath
	*/
	public function filepath(string|array $value) : string|array
	{
		return $this->value($value, 'filepath');
	}

	/**
	* Filters an url
	* @param string|array $url The url to filter
	* @return string|array The filtered url
	*/
	public function url(string|array $url) : string|array
	{
		return $this->value($url, 'url');
	}

	/**
	* Filters an email address
	* @param string|array $email The email to filter (string|array)
	* @return string|array The filtered email
	*/
	public function email(string|array $email) : string|array
	{
		return $this->value($email, 'email');
	}

	/**
	* Filters a url slug value
	* @param string|array $value The value to filter
	* @param bool $allow_slash If true will allow slashes in the returned value
	* @return string|array The filtered slug
	*/
	public function slug(string|array $value, bool $allow_slash = false) : string|array
	{
		return $this->value($value, 'slug', $allow_slash);
	}

	/**
	* Checks that $value is in the $min - $max interval. If it is, it returns $value. If not returns $default_value
	* @param int|float  $value The value
	* @param int|float  $min The min. value
	* @param int|float  $max The max. value
	* @param int|float  $default_value The value to return if $value is not in the $min - $max interval
	* @return int|float  The value
	*/
	public function interval(int|float $value, int|float $min, int|float $max, int|float $default_value) : int|float
	{
		return $this->value($value, 'interval', $min, $max, $default_value);
	}

	/**
	* Removes from $value the $remove_value element
	* @param array $values Array with the values
	* @param string|array $remove The value(s) to remove
	* @return array Array with the filtered values
	*/
	public function remove(array $values, string|array $remove) : array
	{
		return array_diff($values, App::array($remove));
	}

	/**
	* Removes from $value the elements which aren't found in $allowed
	* @param string|array $value The value(s)
	* @param string|array $allowed Array with the allowed elements
	* @param mixed $not_allowed_value The value returned if $value isn't included in $allowed
	* @return mixed Array with the filtered values
	*/
	public function allowed(string|array $value, string|array $allowed, mixed $not_allowed_value = null) : mixed
	{
		$allowed = App::array($allowed);

		if (is_array($value)) {
			return array_intersect($value, $allowed);
		} else {
			if (in_array($value, $allowed)) {
				return $value;
			} else {
				return $not_allowed_value;
			}
		}
	}
}
