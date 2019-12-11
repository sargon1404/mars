<?php
/**
* The View Class
* @package Mars
*/

namespace Mars;

/**
* The View Class
* Implements the View functionality of the MVC pattern
*/
abstract class View
{
	use AppTrait;
	use ReflectionTrait;

	/**
	* @var string $url The url of the controller to which the view belongs.
	*/
	public string $url = '';
	
	/**
	* @var string $site_url Alias for $this->app->site_url
	*/
	public string $site_url = '';

	/**
	* @var string $layout The name of the layout (subdir) from where the template is rendered
	*/
	protected string $layout = '';

	/**
	* @var string $template The name of the template which will be rendered when render() is called
	*/
	protected string $template = '';

	/**
	* @var string $current_method The name of the currently executed method
	*/
	protected string $current_method = '';

	/**
	* @var string $dirname The dirname of the view. Populated only after render is called
	*/
	protected string $dirname = '';
	
	/**
	* @var Controller $controller The controller
	*/
	protected Controller $controller;

	/**
	* @var Model $model The model
	*/
	protected Model $model;

	/**
	* @var Html $html Alias for $this->app->html
	*/
	public Html $html;

	/**
	* @var Filter $filter Alias for $this->app->filter
	*/
	public Filter $filter;

	/**
	* @var Escape $escape Alias for $this->app->escape
	*/
	public Escape $escape;

	/**
	* @var Format $format Alias for $this->app->format
	*/
	public Format $format;

	/**
	* @var Uri $uri Alias for $this->app->format
	*/
	public Uri $uri;

	/**
	* @var Text $uri Alias for $this->app->text
	*/
	public Text $text;

	/**
	* Builds the View
	* @param Controller $controller The controller the view belongs to
	*/
	public function __construct(Controller $controller)
	{
		$this->app = $this->getApp();

		$this->prepare($controller);
		$this->init();
	}

	/**
	* Prepares the view
	* @param Controller $controller The controller the view belongs to
	*/
	protected function prepare(Controller $controller)
	{
		$this->html = $this->app->html;
		$this->ui = $this->app->ui;
		$this->filter = $this->app->filter;
		$this->escape = $this->app->escape;
		$this->format = $this->app->format;
		$this->text = $this->app->text;
		$this->uri = $this->app->uri;

		$this->controller = $controller;
		$this->model = $this->controller->model;

		$this->site_url = $this->app->site_url;
		$this->url = $this->controller->url;
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
	* @return $this
	*/
	public function setTitle(string $title)
	{
		$this->app->title->set($title);

		return $this;
	}

	/**
	* Sets the name of the layout to use when rendering the template.
	* @param string $layout The name of the layout
	* @return $this
	*/
	public function setLayout(string $layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	* Returns the name of the layout
	* @return string
	*/
	public function getLayout() : string
	{
		return $this->layout;
	}

	/**
	* Sets the name of the template to render
	* @param string $template The name of the template
	* @return $this
	*/
	public function setTemplate(string $template)
	{
		$this->template = $template;

		return $this;
	}

	/**
	* Returns the content of a template, loaded from the templates folder.
	* @param string $template The name of the template to load. If left empty, the template with the same name as the controller's current method will be loaded
	* @param string $layout The name of the layout (subfolder)
	* @return string The template's content
	*/
	public function getTemplate(string $template = '', string $layout = '') : string
	{
		if (!$template) {
			$template = $this->controller->current_method;
		}
		if ($layout) {
			$layout = $this->layout;
		}

		$filename = $this->getTemplateFilename($template, $layout);

		return $this->app->theme->getTemplateFromFilename($filename);
	}

	/**
	* Returns the filename of a template
	* @param string $template The name of the template
	* @param string $layout The name of the layout
	* @return string The filename
	*/
	protected function getTemplateFilename(string $template, string $layout) : string
	{
		var_dump("sdfdsfs");
		die;
		return $this->app->theme->buildTemplateFilename($this->dirname . App::EXTENSIONS_DIRS['templates'] . $layout, $template);
	}

	/**
	* Loads and outputs a template
	* @param string $template The name of the template to load. If left empty, the template with the same name as the controller's current method will be loaded
	* @param string $layout The name of the layout (subfolder)
	*/
	public function renderTemplate(string $template = '', string $layout = '')
	{
		echo $this->getTemplate($template, $layout);
	}

	/**
	* Renders a template.
	* If the view is of type View each param is automatically added as theme variables.
	* If the view extends class View the params. are passed as method params. to the method named the same as the current controller method and it's the job of that method to add the theme variable
	* @param array $data Data to pass to the response handler, if any
	* @return $this
	*/
	public function render(array $data = [])
	{
		$this->renderPrepare();

		ob_start();
		$this->renderTemplate($this->template, $this->layout);
		$content = ob_get_clean();

		$this->app->response->output($content, $data);

		return $this;
	}

	/**
	* Sends an ajax response.
	* @param array $data The response data to send. If empty, it will be automatically built
	* @param bool $send_content_on_error Will send the content even if there is an error
	*/
	public function send(array $data = [], bool $send_content_on_error = false)
	{
		$this->renderPrepare();

		ob_start();
		$this->renderTemplate($this->template, $this->layout);
		$this->sendPrepare();
		$content = ob_get_clean();

		$response = new response\Ajax;
		$response->output($content, $data, $send_content_on_error);
	}

	/**
	* @internal
	*/
	protected function sendPrepare()
	{
	}

	/**
	* Prepares the view for rendering
	*/
	protected function renderPrepare()
	{
		$rc = new \ReflectionClass($this);

		$class_name = $rc->getName();
		$class_parent = $rc->getParentClass();
		$this->dirname = App::sl($this->app->file->dirname($rc->getFileName()));

		if ($class_parent) {
			$this->current_method = $this->controller->current_method;
			if (!$this->current_method) {
				$this->current_method = 'index';
			}

			if ($this->canDispatch($this->current_method)) {
				$method = $this->current_method;
				$this->$method();
			}
		}

		//add the view as a theme var
		$this->app->theme->addVar('view', $this);

		//add the view's public properties as theme vars
		$this->app->theme->addVars(get_object_vars($this));
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

		if ($rm->getDeclaringClass()->isAbstract()) {
			return false;
		}

		if ($rm->isConstructor() || $rm->isDestructor()) {
			return false;
		}

		if (!$rm->isPublic()) {
			return false;
		}

		return true;
	}
}
