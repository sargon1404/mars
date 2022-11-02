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
	* @var Plugins $plugins Alias for $this->app->plugins
	*/
	protected object $plugins;

	/**
	* Builds the Model
	*/
	public function __construct()
	{
		parent::__construct();

		$this->prepare();
		$this->init();
	}

	/**
	* Prepares the model's properties
	*/
	protected function prepare()
	{
		$this->plugins = $this->app->plugins;
	}

	/**
	* Inits the model. Method which can be overriden in custom models to init properties etc..
	*/
	protected function init()
	{
	}
}
