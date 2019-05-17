<?php
/**
* The Route Class
* @package Mars
*/

namespace Mars;

/**
* The Route Class
* Implements the View functionality of the MVC pattern
*/
class Router
{
	use AppTrait;
	
	/**
	* @var array $params The params of the currently executed route
	*/
	public $params = [];

	/**
	* @var array $routes The defined routes
	*/
	protected $routes = [];

	/**
	* Adds a route
	* @param string $type The type: get/post/put/delete
	* @param string $route The route to handle
	* @param mixed The action. Can be a closure, a string or a controller
	* @return $this
	*/
	public function add(string $type, string $route, $action)
	{
		$this->routes[$type][$route] = $action;
		
		return $this;
	}
	
	/**
	* Outputs the content based on the matched route
	* @return $this
	*/
	public function execute()
	{
		$route = $this->getRoute();
		if (!$route) {
			$this->notFound();
		}

		$this->app->start();
		
		$this->output($route);
		
		$this->app->end();
		
		$this->app->output();
		
		return $this;
	}

	/**
	* Outputs the content of a route
	*/
	protected function output($route)
	{
		[$route, $params] = $route;
		
		$this->params = $params;
		
		if (is_string($route)) {
			$parts = explode('@', $route);
			
			$method = '';
			$class_name = $parts[0];
			if (isset($parts[1])) {
				$method = $parts[1];
			}

			$controller = new $class_name;
			if ($controller instanceof Controller) {
				$controller->dispatch($method, $params);
			} else {
				if ($method) {
					call_user_func_array([$controller, $method], $params);
				} else {
					throw new \Exception('No controller method to handle the route');
				}
			}
		} elseif (is_object($route) && $route instanceof \Closure) {
			echo call_user_func_array($route, $params);
		}
	}

	/**
	* Returns the route matching the current request
	* @return mixed
	*/
	protected function getRoute()
	{
		$method = $this->app->request->method;
		
		if (!isset($this->routes[$method])) {
			return null;
		}
			
		$routes = $this->routes[$method];
		$path = $this->getPath();

		foreach ($routes as $route_path => $route) {
			//get the route params
			$params = [];
			$params_keys = [];
			$route_path = preg_quote($route_path, '|');
				
			$route_path = preg_replace_callback('/\\\:([a-z0-9_]*)/is', function ($match) use (&$params_keys) {
				$params_keys[] = $match[1];
				
				return '(.*)';
			}, $route_path);

			if (preg_match("|{$route_path}|is", $path, $matches)) {
				foreach ($matches as $key => $val) {
					if (!$key) {
						continue;
					}
					
					$param_key = $params_keys[$key - 1];
					
					$params[$param_key] = $val;
				}

				return [$route, $params];
			}
		}
		
		return null;
	}
	
	/**
	* Returns the current path
	* @return string The current path
	*/
	protected function getPath() : string
	{
		$request_uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
		$script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
		
		$parts = array_diff_assoc($request_uri, $script_name);
		if (!$parts) {
			return '/';
		}
		
		return implode('/', $parts);
	}

	/**
	* Handles the 404 not found cases
	*/
	public function notFound()
	{
		header('HTTP/1.0 404 Not Found', true, 404);
		die;
	}

	/**
	* Handles a get request
	* @param string $route The route to handle
	* @param mixed The action. Can be a closure, a string, a controller
	* @return $this
	*/
	public function get(string $route, $action)
	{
		$this->add('get', $route, $action);
		
		return $this;
	}
	
	/**
	* Handles a get request
	* @param string $route The route to handle
	* @param mixed The action. Can be a closure, a string, a controller
	* @return $this
	*/
	public function post(string $route, $action)
	{
		$this->add('post', $route, $action);
	}
	
	/**
	* Handles a get request
	* @param string $route The route to handle
	* @param mixed The action. Can be a closure, a string, a controller
	* @return $this
	*/
	public function put(string $route, $action)
	{
		$this->add('put', $route, $action);
	}
	
	/**
	* Handles a get request
	* @param string $route The route to handle
	* @param mixed The action. Can be a closure, a string, a controller
	* @return $this
	*/
	public function delete(string $route, $action)
	{
		$this->add('delete', $route, $action);
	}
}
