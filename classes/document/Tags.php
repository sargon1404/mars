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
	/**
	* @var array $tags_array Array with all the tags be outputed
	*/
	public array $tags_array = [];

	/**
	* Outputs a tag
	* @param string $name The name of the tag
	* @param string $value The value of the tag
	*/
	abstract public function outputTag(string $name, string $value);

	/**
	* Returns the list of tags
	* @return array
	*/
	public function get() : array
	{
		return $this->tags_array;
	}

	/**
	* Will add the specified tag data in <head></head> part of the document
	* @param string $name The name of the tag
	* @param string $value The value of the tag
	* @return $this
	*/
	public function add(string $name, string $value)
	{
		$this->tags_array[$name] = $value;

		return $this;
	}

	/**
	* Outputs the tags
	* @return $this
	*/
	public function output()
	{
		foreach ($this->tags_array as $name => $value) {
			$this->outputTag($name, $value);
		}

		return $this;
	}
}
