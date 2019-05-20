<?php
/**
* The Controller Class
* @package Mars
*/

namespace Mars;

use Mars\Response\Ajax;

/**
* The Controller Class
* Implements the Controller functionality of the MVC pattern
*/
abstract class Controller
{
	use AppTrait;
	use ReflectionTrait;

	/**
	* @var Model $model The model object
	*/
	public $model = null;

	/**
	* @var View $view The view object
	*/
	public $view = null;

	/**
	* @var string $url The controller's url
	*/
	public $url = '';

	/**
	* @var string $default_method Default method to be executed on dispatch/route, if the requested method doesn't exist or is not public
	*/
	public $default_method = 'index';

	/**
	* @var string $default_error_method Method to be executed on dispatch/route, if the requested method returns true
	*/
	public $default_ok_method = 'index';

	/**
	* @var string $default_error_method Method to be executed on dispatch/route, if the requested method returns false
	*/
	public $default_error_method = 'index';

	/**
	* @var string $current_method The name of the currently executed method
	*/
	public $current_method = '';

	/**
	* @var string $site_url Alias for $this->app->site_url
	*/
	protected $site_url = '';

	/**
	* @var object $request The request object. Alias for $this->app->request
	*/
	protected $request = null;

	/**
	* @var object $filter The filter object. Alias for $this->app->filter
	*/
	protected $filter = null;

	/**
	* @var object $escape Alias for $this->app->escape
	*/
	protected $escape = null;

	/**
	* @var object $errors The errors object. Alias for $this->app->errors
	*/
	protected $errors = null;

	/**
	* @var object $messages The messages object. Alias for $this->app->messages
	*/
	protected $messages = null;

	/**
	* @var object $notifications The notifications object. Alias for $this->app->notifications
	*/
	protected $notifications = null;

	/**
	* @var object $warnings The warnings object. Alias for $this->app->warnings
	*/
	protected $warnings = null;

	/**
	* Builds the controller
	*/
	public function __construct()
	{
		$this->app = $this->getApp();

		$this->prepare();
		$this->init();
	}

	/**
	* Prepares the controller's properties
	*/
	protected function prepare()
	{
		$this->request = $this->app->request;
		$this->filter = $this->app->filter;
		$this->escape = $this->app->escape;
		$this->errors = $this->app->errors;
		$this->messages = $this->app->messages;
		$this->warnings = $this->app->warnings;
		$this->notifications = $this->app->notifications;

		$this->site_url = $this->app->site_url;
		$this->url = $this->app->url;
	}

	/**
	* Inits the controller. Method which can be overriden in custom controllers to init the models/views etc..
	*/
	protected function init()
	{
	}

	/**
	* Sets the default_ok_method and default_error_method to the same method
	* @param string $method The name of the method
	* @return $this
	*/
	public function setDefaultMethods(string $method)
	{
		$this->default_ok_method = $method;
		$this->default_error_method = $method;

		return $this;
	}

	/**
	* Sets the default method to be executed, if the requested one doesn't exist/is not public
	* @param string $method The name of the method
	* @return $this
	*/
	public function setDefaultMethod(string $method)
	{
		$this->default_method = $method;

		return $this;
	}

	/**
	* Sets the ok method. Called after the main method, if it returns true
	* @param string $method The name of the method
	* @return $this
	*/
	public function setDefaultOkMethod(string $method)
	{
		$this->default_ok_method = $method;

		return $this;
	}

	/**
	* Sets the error method. Called after the main method, if it returns false
	* @param string $method The name of the method
	* @return $this
	*/
	public function setDefaultErrorMethod(string $method)
	{
		$this->default_error_method = $method;

		return $this;
	}

	/**
	* Calls method $method.
	* Calls it only if it exists and it's public. If not will call the $default_method method.
	* If the method returns true, $default_ok_method will be called afterwards.
	* If it returns false, $default_error_method is called.
	* No method is called, if the method doesn't return a value
	* @param string $method The name of the method
	* @param array $params Params to be passed to the method, if any
	*/
	public function dispatch(string $method = '', array $params = [])
	{
		if ($method) {
			if (method_exists($this, $method)) {
				if ($this->canDispatch($method)) {
					$this->route($method, $params);

					return;
				}
			} elseif (isset($this->$method)) {
				//call a dynamic added method,if any
				if ($this->$method instanceof \Closure) {
					call_user_func_array($this->$method, [$this]);

					return;
				}
			}
		}

		//call the default method
		$this->call($this->default_method);
	}

	/**
	* Calls method $method, if it's callable, then the default_ok(error)_method based on what value the method returns.
	* If the method returns nothing no additional method is called
	* @param string $method The name of the method
	* @param array $params Params to be passed to the method, if any
	*/
	protected function route(string $method, array $params = [])
	{
		$ret = $this->call($method, $params);

		//call the ok/error methods if the first call returns true or false
		if ($ret === true) {
			$this->call($this->default_ok_method);
		} elseif ($ret === false) {
			$this->call($this->default_error_method);
		} elseif (is_array($ret) || is_object($ret)) {
			//output the return data as json code
			$this->sendData($ret);
		}
	}

	/**
	* Calls a method of the controller
	* @param string $method The name of the method
	* @return mixed Returns whatever $method returns
	* @param array $params Params to be passed to the method, if any
	*/
	protected function call(string $method, array $params = [])
	{
		$this->current_method = $method;

		return call_user_func_array([$this, $method], $params);
	}

	/**
	* Checks if the $method can be called
	* @param string $method The name of the method
	* @return bool
	*/
	protected function canDispatch(string $method) : bool
	{
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

	/**
	* Sends $content as ajax content
	* @param string $content The content to output
	* @param array $data Data to send, if any
	* @param bool $send_content_on_error Will send the content even if there is an error
	*/
	protected function send(string $content = '', array $data = [], bool $send_content_on_error = false)
	{
		$response = new Ajax;
		if ($data) {
			$response_data = $response->get();
			$data = $response_data + $data;
		}

		$response->output($content, $data, $send_content_on_error);
	}

	/**
	* Sends $data as json code
	* @param mixed $data The response data to send
	*/
	protected function sendData($data)
	{
		$response = new Ajax;
		$response->send($data);
	}

	/**
	* Sends an error as ajax content
	* @param string $error The response error to send
	*/
	protected function sendError(string $error)
	{
		$response = new Ajax;
		$data = $response->getData();

		$data['ok'] = 0;
		$data['error'] = $error;

		$response->send($data);
	}

	/**
	* Sends a message as ajax content
	* @param string $message The response message to send
	*/
	protected function sendMessage(string $message)
	{
		$this->sendAlert($message, 'message');
	}

	/**
	* Sends a message as ajax content
	* @param string $message The response message to send
	*/
	protected function sendWarning(string $message)
	{
		$this->sendAlert($message, 'warning');
	}

	/**
	* Sends a message as ajax content
	* @param string $message The response message to send
	*/
	protected function sendNotification(string $message)
	{
		$this->sendAlert($message, 'notification');
	}

	/**
	* Sends an alert
	* @param string $message The response message to send
	* @param string $alert The alert's type
	*/
	protected function sendAlert(string $message, string $alert)
	{
		$response = new Ajax;
		$data = $response->getData();

		$data[$alert] = $message;

		$response->send($data);
	}

	/**
	* Alias for $this->view->render()
	*/
	public function render()
	{
		$this->view->render();
	}
}
