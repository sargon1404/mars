<?php
/**
* The Entities Class
* @package Mars
*/

namespace Mars;

/**
* The Entities Class
* Container of multiple objects
*/
class Entities implements \Iterator, \Countable
{
	/**
	* @var array $data Array containing the objects
	*/
	public array $data = [];

	/**
	* @var int $count The number of loaded objects
	*/
	public int $count = 0;

	/**
	* @var string $class The class of the loaded objects
	*/
	protected static string $class = '\Mars\Entity';

	/**
	* @internal
	*/
	protected int $current = 0;

	/**
	* @internal
	*/
	protected bool $loaded = false;

	/**
	* Builds the objects
	* @param iterable $data The data to load the objects from
	*/
	public function __construct(iterable $data = [])
	{
		$this->setData($data);
	}

	/**
	* Returns the class name of the objects
	* @return string The class name
	*/
	public function getClass() : string
	{
		return static::$class;
	}

	/**
	* Determines if there are loaded objects
	* @return bool
	*/
	public function has() : bool
	{
		if ($this->count) {
			return true;
		}

		return false;
	}

	/**
	* Returns an object
	* @param int $id The id of the object to return
	* @return object
	*/
	public function get(int $id)
	{
		return $this->getObject($id);
	}

	/**
	* Builds an object of $class_name from $data
	* @param mixed $data The data
	*/
	public function getObject($data)
	{
		$class_name = $this->getClass();

		return new $class_name($data);
	}

	/**
	* Assigns data as properties.
	* @param mixed $data The properties (array,object)
	* @return $this
	*/
	public function assign($data)
	{
		$data = App::toArray($data);

		foreach ($data as $name => $value) {
			$this->$name = $value;
		}

		return $this;
	}

	/**
	* Returns the data/objects
	* @return array
	*/
	public function getData() : iterable
	{
		return $this->data;
	}

	/**
	* Sets the data/objects
	* @param iterable $data The data to load the objects from
	* @param bool $convert If true, will convert the object to a instance of $class_name
	* @return $this
	*/
	public function setData(iterable $data, bool $convert = true)
	{
		$this->data = [];
		$this->current = 0;
		$this->loaded = true;
		$this->count = count($data);

		$class_name = $this->getClass();

		$i = 0;
		foreach ($data as $obj) {
			if ($convert) {
				$this->data[$i] = new $class_name($obj);
			} else {
				$this->data[$i] = $obj;
			}

			$i++;
		}

		return $this;
	}

	/**
	* Updates an object from the collection
	* @param int $index The index of the object to update
	* @param mixed $data The new data of the object
	* @return bool Returns true if the data was updated, false if the index wasn't found
	*/
	public function updateData(int $index, $data) : bool
	{
		if (!isset($this->data[$index])) {
			return false;
		}

		$this->data[$index] = $data;

		return true;
	}

	/**
	* Adds $data to the existing data/objects
	* @param mixed $data The data to add (iterable | object)
	* @return $this
	*/
	public function addData($data)
	{
		if (!is_iterable($data)) {
			$data = [$data];
		}

		$i = $this->count;
		foreach ($data as $obj) {
			$this->data[$i] = $obj;
			$i++;
		}

		$this->current = 0;
		$this->loaded = true;
		$this->count+= count($data);

		return $this;
	}

	/**
	* Builds the data from an array list. An easy way to build multiple objects each having one property
	* @param array $data The data Eg: ['apples','oranges']
	* @param string $name The name of the property to which the data elements will be assigned
	* @return $this
	*/
	public function build(array $data, string $name)
	{
		$this->data = [];
		$this->current = 0;
		$this->loaded = true;
		$this->count = count($data);

		$class_name = $this->getClass();

		$i = 0;
		foreach ($data as $value) {
			$this->data[$i] = new $class_name;
			$this->data[$i]->$name = $value;
			$i++;
		}

		return $this;
	}
	
	public function load() : array
	{
		$this->loaded = true;
		
		return [];
	}

	/**
	* Outputs an object's property
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
	* Outputs an object's property. Doesn't escape it
	* @param string $name The name of the property
	*/
	public function outputRaw(string $name)
	{
		$this->outputVar($name, false);
	}

	/**
	* Returns the count of loaded objects
	* Implements the Countable interface
	* @return int
	*/
	public function count() : int
	{
		return $this->count;
	}

	/**
	* Implements the Iterator interface
	* @internal
	*/
	public function current()
	{	
		if (!$this->loaded) {
			$this->load();
		}
		
		return $this->data[$this->current];
	}

	/**
	* Implements the Iterator interface
	* @internal
	*/
	public function key()
	{		
		return $this->current;
	}

	/**
	* Implements the Iterator interface
	* @internal
	*/
	public function next()
	{
		$this->current++;
	}

	/**
	* Implements the Iterator interface
	* @internal
	*/
	public function rewind()
	{
		if (!$this->loaded) {
			$this->load();
		}
		
		$this->current = 0;
	}

	/**
	* Implements the Iterator interface
	* @internal
	*/
	public function valid()
	{
		if ($this->current >= $this->count) {
			return false;
		}

		return true;
	}
}
