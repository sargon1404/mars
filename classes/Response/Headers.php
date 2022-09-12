<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;
use Mars\Elements;

/**
* The Headers Response Class
* Handles the response headers
*/
class Headers extends Elements
{
	use \Mars\AppTrait;

	/**
	* Builds the Cookie Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	* @see \Mars\Elements::add()
	* {@inheritdoc}
	*/
	public function add(string $name, string $value) : static
	{
		return parent::add($name, $name . ': ' . $value);
	}

	/**
	* Outputs the headers
	*/
	public function output()
	{
		foreach ($this->elements as $header) {
			header($header);
		}
	}
}
