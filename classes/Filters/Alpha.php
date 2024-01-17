<?php
/**
* The Alphabetic Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
 * The Alphabetic Filter Class
 */
class Alpha
{
	/**
	 * @see \Mars\Filter::alpha()
	 */
	public function filter(string $value, bool $space = false) : string
	{
		$pattern = "/[^a-z]/i";
		if ($space) {
			$pattern = "/[^a-z ]/i";
		}

		return preg_replace($pattern, '', trim($value));
	}
}
