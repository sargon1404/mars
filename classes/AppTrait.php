<?php
/**
* The Use App Trait
* @package Mars
*/

namespace Mars;

/**
* The App Trait
* Trait injecting/pulling the $app dependency into the current object
*/
trait AppTrait
{
	/**
	* @var App $app The app object
	*/
	protected App $app;

	/**
	* Builds the object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app ?? App::get();
	}

	protected function getApp() : App
	{
		return $this->app ?? App::get();
	}

	/**
	* Unsets the app property when serializing
	*/
	public function __sleep()
	{
		$data = get_object_vars($this);

		unset($data['app']);

		return array_keys($data);
	}

	/**
	* Sets the app property when unserializing
	*/
	public function __wakeup()
	{
		$this->app = App::get();
	}

	/**
	* Removes properties which shouldn't be displayed by var_dump/print_r
	*/
	public function __debugInfo()
	{
		$properties = get_object_vars($this);

		unset($properties['app']);

		return $properties;
	}
}
