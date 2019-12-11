<?php
/**
* The Html Response Class
* @package Mars
*/

namespace Mars\Response;

/**
* The Html Response Class
* Generates a html response
*/
class Html implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* @see \Mars\Response\DriverInterface::output()
	* {@inheritDoc}
	*/
	public function output(string $content, $data = [], bool $send_content_on_error = false)
	{
		echo $content;
	}
}
