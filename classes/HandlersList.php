<?php
/**
* The Handlers List Class
* @package Mars
*/

namespace Mars;

/**
* The Handlers List Class
* Encapsulates a list of suported handlers
*/
abstract class HandlersList
{
	/**
	* @var array $list The list of supported handlers in the name => class format
	*/
	protected array $list = [];


	/**
	* Returns the list of supported handlers
	* @return array
	*/
	public function getList() : array
	{
		return $this->list;
	}

	/**
	* Adds a supported handler
	* @param string $name The name of the handler
	* @param string $class The class which will handle it
	* @return static
	*/
	public function add(string $name, string $class) : static
	{
		$this->list[$name] = $class;

		return $this;
	}

	/**
	* Alias for add()
	* @see HandlersList::add()
	*/
	public function set(string $name, string $class) : static
	{
		return $this->add($name, $class);
	}

	/**
	* Removes a supported handler
	* @param string $name The name of the handler
	* @return static
	*/
	public function remove(string $name) : static
	{
		if ($this->list[$name]) {
			unset($this->list[$name]);
		}

		return $this;
	}
}
