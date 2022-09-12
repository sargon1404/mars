<?php
/**
* The Property Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Document Property Class
* Stores the value of a document's property. Eg: title
*/
abstract class Property
{
	/**
	* @var string $value The property's value
	*/
	protected string $value = '';

	/**
	* Sets the value of the property
	* @param string $value The new value
	* @return static
	*/
	public function set(string $value) : static
	{
		$this->value = $value;

		return $this;
	}

	/**
	* Returns the value of the property
	* @return string
	*/
	public function get() : string
	{
		return $this->value;
	}
}
