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
class Entity
{
	/**
	* Builds an object
	* @param array|object $data If $data is an array, will assume the array contains the object's data. If $data is an object, will built the object from $data's properties. If null, will load the default values
	*/
	public function __construct(array|object $data = [])
	{
		$this->setData($data);
	}

	/**
	* Sets the object's properties
	* @param array|object $data The data
	* @return $this
	*/
	public function setData(array|object $data)
	{
		$data = App::array($data);

		foreach ($data as $name => $val) {
			$this->$name = $val;
		}

		return $this;
	}

	/**
	* Returns the object properties as an array
	* @param array $properties Array listing the properties which should be returned. If empty, all properties of the object are returned
	* @return array The object's data/properties
	*/
	public function getData(array $properties = []) : array
	{
		$data = [];

		if ($properties) {
			foreach ($properties as $name) {
				$data[$name] = $this->$name;
			}
		} else {
			$data = get_object_vars($this);
		}

		return $data;
	}

	/**
	* Assigns data as properties. Alias for set_data
	* @param array|object $data The data
	*/
	public function assign(array|object$data)
	{
		$this->setData($data);

		return $this;
	}

	/**
	* Adds multiple properties to the object, each having the value: $value, *IF* that property isn't yet set
	* @param array|object $data The data to add
	* @param mixed $value The value of the properties, if any
	* @return $this
	*/
	public function addData(array|object $data, $value = '')
	{
		$data = App::array($data);

		foreach ($data as $name) {
			if (!isset($this->$name)) {
				$this->$name = $value;
			}
		}

		return $this;
	}

	/**
	* Appends $data to a property named $name
	* @param string $name The name of the property
	* @param mixed $data The data to append
	* @return $this
	*/
	public function appendData(string $name, $data)
	{
		if (isset($this->$name)) {
			$this->$name.= $data;
		} else {
			$this->$name = $data;
		}

		return $this;
	}

	/**
	* Alias for get_data
	* @param array $properties Array listing the properties which should be returned. If empty, all properties of the object are returned
	* @return array The object's data/properties
	*/
	public function toArray(array $properties = []) : array
	{
		return $this->getData($properties);
	}

	/**
	* Returns true if the object has any properties
	* @return bool
	*/
	public function hasData() : bool
	{
		$data = get_object_vars($this);

		if ($data) {
			return true;
		}

		return false;
	}

	/**
	* Clones the data of object $obj into the current $obj
	* @param object $obj The object to clone
	* @param array $data Array listing the properties of $obj to clone. If empty, all properties of $obj are used
	* @return $this
	*/
	public function cloneData($obj, array $data = [])
	{
		if ($obj instanceof Entity && !$data) {
			$this->setData($obj);

			return $this;
		}

		if (!$data) {
			return $this;
		}

		foreach ($data as $name) {
			if (isset($obj->$name)) {
				$this->$name = $obj->$name;
			}
		}

		return $this;
	}

	/**
	* Outputs an object property
	* @param string $name The name of the property
	* @param bool $escape_html If true, will escape the property
	*/
	public function outputVar(string $name, bool $escape_html = true)
	{
		if ($escape_html) {
			echo App::e($this->$name);
		} else {
			echo $this->$name;
		}
	}

	/**
	* Outputs an object property. Doesn't escape it
	* @param string $name The name of the property
	*/
	public function outputRaw(string $name)
	{
		$this->outputVar($name, false);
	}
}
