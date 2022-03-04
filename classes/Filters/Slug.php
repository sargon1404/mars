<?php
/**
* The Slug Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Slug Filter Class
*/
class Slug extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $value, ...$params) : string
	{
		$allow_slash = $params[0] ?? false;

		$original_value = $value;

		$reg = '/[^0-9a-zA-Z_-]+/u';
		if ($allow_slash) {
			$reg = '/[^0-9a-zA-Z_\/-]+/u';
		}

		$value = strtolower(trim($value));
		$value = str_replace([' ', ':', ',', "'", '`', '@', '|', '"', '_', '#'], '-', $value);
		$value = preg_replace($reg, '', $value);

		//replace multiple dashes with just one
		$value = preg_replace('/-+/', '-', $value);

		$value = urlencode($value);

		if ($allow_slash) {
			$value = str_replace('%2F', '/', $value);
		}

		$value = trim($value, '-');

		return $value;
		//return $this->app->plugins->filter('filters_slug_get', $value, $original_value, $allow_slash, $this);
	}
}
