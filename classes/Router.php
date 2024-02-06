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
	 * @var array $routes_list The defined routes list
	 */
	protected array $routes_list = [];
	
	/**
	 * @var Handlers $routes The routes handlers
	 */
	public readonly Handlers $routes;
	
	/**
	 * @var array $routes_types The list of supported routes
	 */
	protected array $routes_types = [
		'block' => '\Mars\Routers\Block',
		'template' => '\Mars\Routers\Template',
		'page' => '\Mars\Routers\Page',
	];
	
	/**
	 * Constructs the routes object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->routes = new Handlers($this->routes_types, $this->app);
	}

	/**
	 * Adds a route
	 * @param string $type The type: get/post/put/delete
	 * @param string $route The route to handle
	 * @param mixed The action. Can be a closure, a string or a controller
	 * @return static
	 */
	public function add(string $type, string $route, $action) : static
	{
		$this->routes_list[$type][$route] = $action;

		return $this;
	}

	/**
	 * Outputs the content based on the matched route
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
	}

	/**
	 * Outputs the content of a route
	 * @param array $route The route
	 */
	protected function output($route)
	{
		[$route, $params] = $route;

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
		} elseif ($route instanceof \Closure) {
			echo call_user_func_array($route, [$this, $this->app]);
		} elseif (is_object($route)) {
			$route->output();
		}
	}

	/**
	 * Returns the route matching the current request
	 * @return mixed
	 */
	protected function getRoute()
	{
		$method = $this->app->method;
		if (!isset($this->routes_list[$method])) {
			return null;
		}

		$routes = $this->routes_list[$method];
		$path = $this->getPath();

		foreach ($routes as $route_path => $route) {
			//get the route params
			$params = [];
			$params_keys = [];
			$route_path = preg_quote($route_path, '|');

			$route_path = preg_replace_callback('/\\\{([a-z0-9_]*)\\\}/is', function ($match) use (&$params_keys) {
				$params_keys[] = $match[1];

				return '(.*)';
			}, $route_path);

			if (preg_match("|^{$route_path}$|is", $path, $matches)) {
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
	 * Handles a get request
	 * @param string $route The route to handle
	 * @param mixed The action. Can be a closure, a string, a controller
	 * @return static
	 */
	public function get(string $route, $action) : static
	{
		return $this->add('get', $route, $action);
	}

	/**
	 * Handles a get request
	 * @param string $route The route to handle
	 * @param mixed The action. Can be a closure, a string, a controller
	 * @returnstatic
	 */
	public function post(string $route, $action) : static
	{
		return $this->add('post', $route, $action);
	}

	/**
	 * Handles a get request
	 * @param string $route The route to handle
	 * @param mixed The action. Can be a closure, a string, a controller
	 * @return static
	 */
	public function put(string $route, $action) : static
	{
		return $this->add('put', $route, $action);
	}

	/**
	 * Handles a get request
	 * @param string $route The route to handle
	 * @param mixed The action. Can be a closure, a string, a controller
	 * @return static
	 */
	public function delete(string $route, $action) : static
	{
		return $this->add('delete', $route, $action);
	}

	/**
	 * Handles a block request
	 * @param string $route The route to handle
	 * @param string $module_name The module the block belongs to
	 * @param string $name The block's name
	 * @return static
	 */
	public function block(string $route, string $module_name, string $name = '') : static
	{
		return $this->setRoute($route, 'block', $module_name, $name);
	}
	
	/**
	 * Handles a template request
	 * @param string $route The route to handle
	 * @param string $template The template's name
	 * @param string $title The title tag of the page
	 * @param array $meta Meta data of the page
	 * @return static
	 */
	public function template(string $route, string $template, string $title = '', array $meta = []) : static
	{
		return $this->setRoute($route, 'template', $template, $title, $meta);
	}

	/**
	 * Handles a page request
	 * @param string $route The route to handle
	 * @param string $template The page's template's name
	 * @param string $title The title tag of the page
	 * @param array $meta Meta data of the page
	 * @return static
	 */
	public function page(string $route, string $template, string $title = '', array $meta = []) : static
	{
		return $this->setRoute($route, 'page', $template, $title, $meta);
	}
	
	/**
	 * Sets a route
	 * @param string $route The route to handle
	 * @param string $handler The handler's name
	 * @param mixed $args Arguments to pass to the handler's constructor
	 */
	protected function setRoute(string $route, string $handler, ...$args) : static
	{
		$obj = $this->routes->get($handler, ...$args);

		$this->add('get', $route, $obj);
		$this->add('post', $route, $obj);

		return $this;
	}

	/**
	 * Handles the 404 not found cases
	 */
	public function notFound()
	{
		header('HTTP/1.0 404 Not Found', true, 404);
		die;
	}
}
