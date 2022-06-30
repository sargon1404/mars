<?php
/**
* The Percentage Format Class
* @package Mars
*/

namespace Mars\Formats;

/**
* The Percentage Format Class
*/
class Percentage extends Format
{
	/**
	* @see \Mars\Formats\Format::get()
	* {@inheritdoc}
	*/
	public function get(string $value, ...$params) : string
	{
		$total = $params[0] ?? 100;
		$decimals = $params[1] ?? 4;

		if (!$value || !$total) {
			return 0;
		}

		$result = ($value * 100) / $total;

		return round($result, $decimals);
	}
}
