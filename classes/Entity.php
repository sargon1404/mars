<?php
/**
* The Entity Class
* @package Mars
*/

namespace Mars;

/**
* The Entity Class
* Contains the functionality of a basic object
*/
class Entity extends \stdClass
{
	/**
	* Builds an object
	* @param array|object $data The entity's data
	*/
	public function __construct(array|object $data = [])
	{
		$this->set($data);
	}

	/**
	* Returns true if the object has the property set
	* @param string $name The name of the property
	* @return bool
	*/
	public function has(string $name) : bool
	{
		return isset($this->$name);
	}

	/**
	* Sets the object's properties
	* @param array|object $data The data
	* @return static
	*/
	public function set(array|object $data) : static
	{
		if (!$data) {
			return $this;
		}

		foreach ($data as $name => $val) {
			$this->$name = $val;
		}

		return $this;
	}

	/**
	* Adds $data, if it doesn't already exist
	* @param array|object $data The data
	* @return static
	*/
	public function add(array|object $data) : static
	{
		if (!$data) {
			return $this;
		}

		foreach ($data as $name => $val) {
			if (!isset($this->$name)) {
				$this->$name = $val;
			}
		}

		return $this;
	}

	/**
	* Alias for set
	* @see \Mars\Entity::set
	* {@inheritdoc}
	*/
	public function assign(array|object $data) : static
	{
		return $this->set($data);
	}

	/**
	* Returns the object properties as an array
	* @param array $properties Array listing the properties which should be returned. If empty, all properties of the object are returned
	* @return array The object's data/properties
	*/
	public function get(array $properties = []) : array
	{
		if ($properties) {
			$data = [];

			foreach ($properties as $name) {
				$data[$name] = $this->$name;
			}

			return $data;
		}

		return App::array($this);
	}
}
