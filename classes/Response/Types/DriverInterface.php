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
	* @param bool $send_content_on_error Will send the content even if there is an error
	*/
	public function output(string $content, $data = [], bool $send_content_on_error = false);
}
