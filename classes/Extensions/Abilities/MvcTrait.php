<?php
/**
* The Extension's MVC Trait
* @package Venus
*/

namespace Mars\Extensions\Abilities;

use Mars\App;
use Mars\Mvc\Controller;
use Mars\Mvc\Model;
use Mars\Mvc\View;

/**
* The Extension's MVC Trait
* Trait implementing the MVC patter for extensions
*/
trait MvcTrait
{
	/**
	* @var Controller $controller The currently loaded controller of this extension
	*/
	public Controller $controller;

	/**
	* Returns a MVC class name
	* @param string $dir The dir from where to load the class
	* @param string $class_name The class name
	* @return string The class name
	*/
	protected function getMvcClass(string $dir, string $class_name) : string
	{
		$namespace_path = str_replace("/", "\\", ucfirst($dir));

		return $namespace_path . App::getClass($class_name);
	}

	/**
	* Loads the controller and returns the instance
	* @param string $controller The name of the controller
	* @param array $allowed_controllers Array with the allowed controler names
	* @return Controller The controller object
	*/
	public function getController(string $controller = '', array $allowed_controllers = []) : Controller
	{
		if ($allowed_controllers) {
			if (!in_array($controller, $allowed_controllers)) {
				$controller = '';
			}
		}

		if (!$controller) {
			$controller = $this->name;
		}

		$controller_class = $this->getMvcClass(App::EXTENSIONS_DIRS['controllers'], $controller);
		$class_name = static::$namespace . App::getClass($this->name) . "\\" . $controller_class;

		$this->controller = new $class_name($this, $this->app);

		return $this->controller;
	}

	/**
	* Loads the model and returns the instance
	* @param string $model The name of the model
	* @return object The model
	*/
	public function getModel(string $model = '') : object
	{
		if (!$model) {
			$model = $this->name;
		}

		$model_class = $this->getMvcClass(App::EXTENSIONS_DIRS['models'], $model);

		$class_name = static::$namespace . App::getClass($this->name) . "\\" . $model_class;

		return new $class_name($this->app);
	}

	/**
	* Loads the view and returns the instance
	* @param string $view The name of the view
	* @return View The view
	*/
	public function getView(string $view = '', Controller $controller = null) : View
	{
		if (!$view) {
			$view = $this->name;
		}

		$view_class = $this->getMvcClass(App::EXTENSIONS_DIRS['views'], $view);

		$class_name = static::$namespace . App::getClass($this->name) . "\\" . $view_class;

		return new $class_name($controller, $this->app);
	}
}
