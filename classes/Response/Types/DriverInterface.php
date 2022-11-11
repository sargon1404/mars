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
	* Outputs $content as json code
	* @param string $content The content to output
	* @param mixed $data The response data. if empty, if will be automatically built
	*/
	public function output(string $content, $data = []);
}
