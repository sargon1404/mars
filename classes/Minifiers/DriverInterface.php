<?php
/**
* The Minifiers Driver Interface
* @package Mars
*/

namespace Mars\Minifiers;

/**
* The Minifiers Driver Interface
*/
interface DriverInterface
{
	/**
	* Minifiers the content
	* @param string $content The content to minify
	* @return string The minified content
	*/
	public function minify(string $content) : string;
}
