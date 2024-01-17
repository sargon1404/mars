<?php
/**
* The Tags Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Document Tags Class
 * Stores the custom header html tags used by a document
 */
abstract class Tags
{
	use \Mars\AppTrait;
	use \Mars\Lists\ListTrait;

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
		foreach ($this->list as $name => $value) {
			$this->outputTag($name, $value);
		}

		return $this;
	}
}
