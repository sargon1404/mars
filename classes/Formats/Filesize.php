<?php
/**
* The Filesize Format Class
* @package Mars
*/

namespace Mars\Formats;

/**
* The Filesize Format Class
*/
class Filesize
{
	/**
	* @see \Mars\Format::filesize()
	*/
	public function format(int|array $bytes, int $digits = 2) : string|array
	{
		$gb_limit = 1024 * 768;

		$bytes = $bytes / 1024;

		if ($bytes > $gb_limit) {
			return round($bytes / 1024 / 1024, $digits) . ' GB';
		} else {
			$kb_limit = 768;

			if ($bytes > $kb_limit) {
				return round($bytes / 1024, $digits) . ' MB';
			} else {
				return round($bytes, $digits) . ' KB';
			}
		}
	}
}
