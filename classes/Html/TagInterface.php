<?php
/**
* The Html Interface
* @package Mars
*/

namespace Mars\Html;

/**
* The Html Interface
*/
interface TagInterface
{
	/**
	* Returns the html code of a tag
	* @return string The html code
	*/
	public function get() : string;
}
