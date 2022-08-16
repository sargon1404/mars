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
class Entities implements \IteratorAggregate
{
	use AppTrait;

	/**
	* @var array $data Array containing the objects
	*/
	protected array $data = [];

	/**
	* @var string $class The class of the loaded objects
	*/
	protected static string $class = '\Mars\Entity';

	/**
	* Builds the objects
	* @param iterable $data The data to load the objects from
	*/
	public function __construct(iterable $data = [])
	{
		$this->set($data);
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
	* Returns the number of loaded objects
	* @return int
	*/
	public function getCount() : int
	{
		return count($this->data);
	}

	/**
	* Determines if there are loaded objects
	* @return bool
	*/
	public function has() : bool
	{
		if ($this->data) {
			return true;
		}

		return false;
	}

	/**
	* Sets the data/objects
	* @param iterable $data The entities array
	* @return static
	*/
	public function set(iterable $data) : static
	{
		$this->data = $data;

		return $this;
	}

	/**
	* Adds $data to the existing data/objects
	* @param object|iterable $data The data to add
	* @return static
	*/
	public function add(object|iterable $data) : static
	{
		if (is_object($data)) {
			$data = [$data];
		}

		$this->data = array_merge($this->data, $data);

		return $this;
	}

	/**
	* Updates an object from the collection
	* @param int $id The index of the object to update
	* @param Entity $object The new object
	* @return bool Returns true if the data was updated, false if the index wasn't found
	*/
	public function update(int $id, Entity $object) : bool
	{
		if (isset($this->data[$id])) {
			$this->data[$id] = $object;

			return true;
		}

		return false;
	}

	/**
	* Returns the data/objects
	* @param int $id The id of the object to return. If null, all the objects are returned
	* @return mixed
	*/
	public function get(int $id = null)
	{
		if ($id === null) {
			return $this->data;
		}

		return $this->data[$id] ?? null;
	}

	/**
	* Builds an object of $this->class from $data
	* @param array|object $data The data
	*/
	public function getObject(array|object $data) : Entity
	{
		$class_name = $this->getClass();

		return new $class_name($data);
	}

	/**
	* Returns the iterator
	* @return \Traversable
	*/
	public function getIterator() : \Traversable
	{
		return $this->data;
	}
}
