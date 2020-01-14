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
	protected ?App $app = null;

	/**
	* Builds the object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		if ($app) {
			$this->app = $app;
		} else {
			$this->app = App::get();
		}
	}

	protected function getApp() : App
	{
		if ($this->app) {
			return $this->app;
		}

		return App::get();
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
	* Unsets the app object, before App::print_r and App::var_dump
	*/
	public function unsetApp()
	{
		unset($this->app);

		return $this;
	}

	/**
	* Don't use __debugInfo, as all properties will be shown by var_dump as public
	* Removes the app obj from the list of properties which are displayed by var_dump
	*/
	/*public function __debugInfo()
	{
		$properties = get_object_vars($this);

		unset($properties['app']);

		return $properties;
	}*/
}
