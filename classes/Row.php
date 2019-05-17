<?php
/**
* The Row Class
* @package Mars
*/

namespace Mars;

/**
* The Row Class
* Contains the functionality of a database row
*/
class Row extends Entity
{
	/**
	* Method to be called if the object is returned by PDO::fetchObject
	*/
	public function initialize()
	{
		$this->prepare();
	}

	/**
	* Child classes can implement this method to prepare the object when it's loaded
	*/
	protected function prepare()
	{
	}
}
