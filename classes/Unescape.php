<?php
/**
* The Unescape Class
* @package Mars
*/

namespace Mars;

/**
 * The Unescape Class
 * Unescapes values
 */
class Unescape
{
	use AppTrait;

	/**
	 * Converts html entitites back to characters
	 * @param string $value The value
	 * @return string The unescaped value
	 */
	public function html(?string $value) : string
	{
		return htmlspecialchars_decode($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
	}
}
