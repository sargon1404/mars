<?php
/**
* The Filesize Format Class
* @package Mars
*/

namespace Mars\Formats;

/**
* The Filesize Format Class
*/
class Filesize extends Format
{
	/**
	* @see \Mars\Formats\Format::get()
	* {@inheritdoc}
	*/
	public function get(string $value, ...$params) : string
	{
		$digits = $params[0] ?? 2;

		$gb_limit = 1024 * 768;

		$value = $value / 1024;

		if ($value > $gb_limit) {
			return round($value / 1024 / 1024, $digits) . ' GB';
		} else {
			$kb_limit = 768;

			if ($value > $kb_limit) {
				return round($value / 1024, $digits) . ' MB';
			} else {
				return round($value, $digits) . ' KB';
			}
		}
	}
}
