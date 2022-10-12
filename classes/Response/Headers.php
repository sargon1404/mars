<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;

/**
* The Headers Response Class
* Handles the response headers
*/
class Headers
{
	use \Mars\AppTrait;
	use \Mars\Lists\ListTrait {
		add as addToList;
	}

	/**
	* Builds the Cookie Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	* @see \Mars\Lists\ListTrait::add()
	*/
	public function add(string $name, string $value) : static
	{
		return $this->addToList($name, $name . ': ' . $value);
	}

	/**
	* Outputs the headers
	*/
	public function output()
	{
		foreach ($this->list as $header) {
			header($header);
		}
	}
}
