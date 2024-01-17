<?php
/**
* The List Trait
* @package Mars
*/

namespace Mars\Lists;

/**
 * The List Trait
 * Encapsulates a list
 */
trait ListTrait
{
	/**
	 * @var array $list The list of elements in the name => value format
	 */
	protected array $list = [];

	/**
	 * Returns an element, or all elements
	 * @param string $name If specified, will return only this element
	 */
	public function get(string $name = '')
	{
		if (!$name) {
			return $this->list;
		}

		return $this->list[$name] ?? null;
	}

	/**
	 * Adds an element
	 * @param string $name The name of the element
	 * @param string $value The value
	 * @return static
	 */
	public function add(string $name, string $value) : static
	{
		$this->list[$name] = $value;

		return $this;
	}

	/**
	 * Alias for add()
	 * @see ListTrait::add()
	 */
	public function set(string $name, string $value) : static
	{
		return $this->add($name, $value);
	}

	/**
	 * Removes an element
	 * @param string $name The name of the element
	 * @return static
	 */
	public function remove(string $name) : static
	{
		if (isset($this->list[$name])) {
			unset($this->list[$name]);
		}

		return $this;
	}
}
