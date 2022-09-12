<?php
/**
* The Tags Class
* @package Mars
*/

namespace Mars\Document;

use Mars\Elements;

/**
* The Document Tags Class
* Stores the custom header html tags used by a document
*/
abstract class Tags extends Elements
{
	use \Mars\AppTrait;

	/**
	* Outputs a tag
	* @param string $name The name of the tag
	* @param string $value The value of the tag
	*/
	abstract public function outputTag(string $name, string $value);

	/**
	* Outputs the tags
	* @return static
	*/
	public function output() : static
	{
		foreach ($this->elements as $name => $value) {
			$this->outputTag($name, $value);
		}

		return $this;
	}
}
