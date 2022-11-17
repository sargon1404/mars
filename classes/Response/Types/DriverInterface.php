<?php
/**
* The Response Interface
* @package Mars
*/

namespace Mars\Response\Types;

/**
* The Response Interface
*/
interface DriverInterface
{
	/**
	* Returns the content as a string
	* @param mixed $content The content
	* @return string
	*/
	public function get($content) : string;

	/**
	* Outputs $content
	* @param string $content The content to output
	*/
	public function output(string $content);
}
