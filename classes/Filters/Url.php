<?php
/**
* The Url Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Url Filter Class
*/
class Url extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $url, ...$params) : string
	{
		return filter_var($url, FILTER_SANITIZE_URL);
	}
}
