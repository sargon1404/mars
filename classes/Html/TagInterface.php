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
	* @param string $text The tag's text
	* @param array $attributes The tag's attributes
	* @param array $properties Extra properties to pass to the tag object
	* @return string The html code
	*/
	public function html(string $text = '', array $attributes = [], array $properties = []) : string;
}
