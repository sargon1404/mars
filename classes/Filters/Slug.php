<?php
/**
* The Slug Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Slug Filter Class
 */
class Slug
{
	use \Mars\AppTrait;

	/**
	 * @see \Mars\Filter::slug()
	 */
	public function filter(string $value, bool $allow_slash = false) : string
	{
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

		return $this->app->plugins->filter('filters_slug_filter', $value, $original_value, $allow_slash, $this);
	}
}
