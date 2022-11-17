<?php
/**
* The View Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\App;
use Mars\Escape;
use Mars\Filter;
use Mars\Format;
use Mars\Html;
use Mars\Text;
use Mars\Ui;
use Mars\Uri;

use Mars\System\Plugins;
use Mars\Extensions\Extension;

/**
* The View Class
* Implements the View functionality of the MVC pattern
*/
abstract class View
{
	use \Mars\AppTrait;

	/**
	* @var string $template The name of the template which will be rendered when render() is called
	*/
	protected string $template = '';

	/**
	* @var string $path The controller's parents's dir. Alias for $this->parent->path
	*/
	public string $path = '';

	/**
	* @var string $url The controller's parent's url. Alias for $this->parent->url
	*/
	public string $url = '';

	/**
	* @var string $url_static The controller's parent's url static. Alias for $this->parent->url_static
	*/
	public string $url_static = '';

	/**
	* @var Extension $parent The parent extension
	*/
	protected Extension $parent;

	/**
	* @var Controller $controller The controller
	*/
	protected Controller $controller;

	/**
	* @var object $model The model
	*/
	protected object $model;

	/**
	* @var Html $html Alias for $this->app->html
	*/
	protected Html $html;

	/**
	* @var Escape $escape Alias for $this->app->escape
	*/
	protected Escape $escape;

	/**
	* @var Filter $filter Alias for $this->app->filter
	*/
	protected Filter $filter;

	/**
	* @var Format $format Alias for $this->app->format
	*/
	protected Format $format;

	/**
	* @var Uri $uri Alias for $this->app->uri
	*/
	public Uri $uri;

	/**
	* @var Ui $ui Alias for $this->app->ui
	*/
	protected Ui $ui;

	/**
	* @var Text $uri Alias for $this->app->text
	*/
	protected Text $text;

	/**
	* @var Plugins $plugins Alias for $this->app->plugins
	*/
	protected Plugins $plugins;

	/**
	* Builds the View
	* @param Controller $controller The controller the view belongs to
	* @param App $app the app object
	*/
	public function __construct(Controller $controller, App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->controller = $controller;
		$this->model = $this->controller->model;
		$this->parent = $this->controller->parent;
		if ($this->parent) {
			$this->path = $this->parent->path;
			$this->url = $this->parent->url;
			$this->url_static = $this->parent->url_static;
		}

		$this->prepare();
		$this->init();
	}

	/**
	* Prepares the view
	*/
	protected function prepare()
	{
		$this->html = $this->app->html;
		$this->escape = $this->app->escape;
		$this->filter = $this->app->filter;
		$this->format = $this->app->format;
		$this->text = $this->app->text;
		$this->ui = $this->app->ui;
		$this->uri = $this->app->uri;
		$this->plugins = $this->app->plugins;
	}

	/**
	* Inits the view. Method which can be overriden in custom views to init properties etc..
	*/
	protected function init()
	{
	}

	/**
	* Sets the title of the current page
	* @param string $title The title
	* @return static
	*/
	protected function setTitle(string $title) : static
	{
		$this->app->title->set($title);

		return $this;
	}

	/**
	* Renders a template.
	* @param array $vars Vars to pass to the template, if any
	*/
	public function render(array $vars = [])
	{
		$method = $this->controller->current_method;
		if (!$method) {
			$method = 'index';
		}

		if ($this->canDispatch($method)) {
			$this->$method();
		}

		//add the view's public properties as theme vars
		$this->app->theme->addVars(get_object_vars($this));
		$this->app->theme->addVar('view', $this);

		$template = $this->getTemplate($method);

		$this->parent->render($template, $vars);
	}

	/**
	* Returns the name of a template to load
	* @param string $method The currently executed method
	* @return string The template's name
	*/
	protected function getTemplate(string $method) : string
	{
		if ($this->template) {
			return $this->template;
		}

		$template = preg_replace('/([A-Z])/', '-$1', $method);
		$template = strtolower($template);

		return $template;
	}

	/**
	* Sets the name of the template to render
	* @param string $template The name of the template
	* @return static
	*/
	protected function setTemplate(string $template) : static
	{
		$this->template = $template;

		return $this;
	}

	/**
	* Determines if a method call can be dispatched
	* @param string $method The name of the method
	* @return bool
	*/
	protected function canDispatch(string $method) : bool
	{
		if (!method_exists($this, $method)) {
			return false;
		}

		$rm = new \ReflectionMethod($this, $method);

		if ($rm->isConstructor() || $rm->isDestructor()) {
			return false;
		}

		if (!$rm->isPublic()) {
			return false;
		}

		return true;
	}
}
