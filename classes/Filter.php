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

		'html' => ['escape'],
		'file' => '\Mars\Filters\File',
		'filename' => '\Mars\Filters\Filename',
		'slug' => '\Mars\Filters\Slug',
	];
	/**
	* Maps a value [scalar|array] to a callback
	*/
	protected function map($value, callable $callback)
	{
		if (is_array($value)) {
			return array_map($callback, $value);
		}

		return $callback($value);
	}

	/**
	* Returns a filtered value
	* @param string $filter The filter name
	* @param mixed $value The value to filter
	* @param mixed $args The arguments to pass to the filter, if any
	*/
	public function value($value, string $filter = 'string', ...$args) : mixed
	{
		return $this->map($value, function($value) use ($filter, $args) {
			$args = array_merge([$value], $args);

			return $this->getValue($filter, ...$args);
		});
	}

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
	* Filters a filename for invalid characters
	* @param string|array $value The filename to filter
	* @param bool $is_path If true will treat $value as a path rather than just a filename
	* @return string|array The filtered filename
	*/
	public function filename(string|array $value, bool $is_path = false) : string|array
	{
		return $this->value($value, 'filename', $is_path);
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
}
