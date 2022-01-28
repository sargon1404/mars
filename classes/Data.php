<?php
/**
* The Data Class
* @package Mars
*/

namespace Mars;

/**
* The Data Class.
* Represent data stored in the format name => value
*/
abstract class Data
{
	use AppTrait;

	/**
	* Determines if a property with the given name exists and is not empty
	* @param string $name The name of the property
	* @return bool
	*/
	public function has(string $name) : bool
	{
		return empty($this->$name);
	}

	/**
	* Assigns the data to the object
	* @param array $data Array in the name=>value format
	* @return static
	*/
	public function assign(array $data) : static
	{
		foreach ($data as $name => $value) {
			$this->$name = $value;
		}

		return $this;
	}
}
