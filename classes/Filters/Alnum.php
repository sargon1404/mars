<?php
/**
* The Alphanumeric Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Alphanumeric Filter Class
 */
class Alnum
{
	/**
	 * @see \Mars\Filter::alnum()
	 */
	public function filter(string $value, bool $space = false) : string
	{
		$pattern = "/[^0-9a-z]/i";
		if ($space) {
			$pattern = "/[^0-9a-z ]/i";
		}

		return preg_replace($pattern, '', trim($value));
	}
}
