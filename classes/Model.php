<?php
/**
* The Model Class
* @package Mars
*/

namespace Mars;

/**
* The Model Class
* Implements the Model functionality of the MVC pattern
*/
abstract class Model extends Items
{
	use ReflectionTrait;

	/**
	* Builds the Model
	*/
	public function __construct()
	{
		parent::__construct();

		$this->init();
	}

	/**
	* Inits the model. Method which can be overriden in custom models to init properties etc..
	*/
	protected function init()
	{
	}
}
