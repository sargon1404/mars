<?php
/**
* The Ajax Response Class
* @package Mars
*/

namespace Mars\Response\Types;

use Mars\App;

/**
 * The Ajax Response Class
 * Generates a json response
 */
class Ajax implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	 * @see \Mars\Response\DriverInterface::get()
	 * {@inheritdoc}
	 */
	public function get($content) : string
	{
		return \json_encode($content);
	}

	/**
	 * @see \Mars\Response\DriverInterface::output()
	 * {@inheritdoc}
	 */
	public function output(string $content)
	{
		header('Content-Type: application/json', true);

		echo $content;
	}
}
