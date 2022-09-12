<?php
/**
* The Elements Class
* @package Mars
*/

namespace Mars;

/**
* The Elements Class
* Container for an array
*/
class Elements
{
	use AppTrait;

	/**
	* @var array $elements Array containing the elements
	*/
	protected array $elements = [];

	/**
	* Returns an element, or all elements
	* @param string $name If specified, will return only this element
	*/
	public function get(string $name = '')
	{
		if (!$name) {
			return $this->elements;
		}

		return $this->elements[$name] ?? null;
	}

	/**
	* Adds an element
	* @param string $name The name
	* @param string $value The value
	* @return static
	*/
	public function add(string $name, string $value) : static
	{
		$this->elements[$name] = $value;

		return $this;
	}

	/**
	* Alias for add()
	* {@see Elements::add()}
	*/
	public function set(string $name, string $value) : static
	{
		return $this->add($name, $value);
	}

	/**
	* Removes a element
	* @param string $name The name of the element
	* @return static
	*/
	public function remove(string $name) : static
	{
		if (isset($this->elements[$name])) {
			unset($this->elements[$name]);
		}

		return $this;
	}
}
