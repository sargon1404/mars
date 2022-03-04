<?php
/**
* The Alphabetic Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Alphabetic Filter Class
*/
class Alpha extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $value, ...$params) : string
	{
		$space = $params[0] ?? false;

		$pattern = "/[^a-z]/i";
		if ($space) {
			$pattern = "/[^a-z ]/i";
		}

		return preg_replace($pattern, '', trim($value));
	}
}
