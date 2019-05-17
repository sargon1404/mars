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
class Html
{
	use \Mars\AppTrait;

	/**
	* Outputs $content
	* @param string $content The content to output
	* @param array $data Not used
	*/
	public function output(string $content, array $data = [])
	{
		echo $content;
	}
}
