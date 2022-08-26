<?php
/**
* The Handlers Class
* @package Mars
*/

namespace Mars;

/**
* The Handlers Class
* Encapsulates a list of suported handlers
*/
class Handlers
{
	use AppTrait;

	/**
	* @var string $method The method called on a handler object when getValue is called
	*/
	public string $method = 'get';

	/**
	* @var bool $store If true, the handlers will be stored in $this->handlers
	*/
	public bool $store = true;

	/**
	* @var array $list The list of supported handlers in the name => class format
	*/
	protected array $list = [];

	/**
	* @var array $handlers Array storing the handler objects, if $store is true
	*/
	protected array $handlers = [];

	/**
	* Builds the handler object
	* @param array $list The list of supported handlers
	* @param string $method The method called on a handler object when getValue is called
	* @param bool $store If true, the handlers will be stored in $this->handlers
	* @param App $app The app object
	*/
	public function __construct(array $list, string $method = 'get', bool $store = true, App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->list = $list;
		$this->method = $method;
		$this->store = $store;
	}

	/**
	* Returns the list of supported handlers
	* @return array
	*/
	public function getList() : array
	{
		return $this->list;
	}

	/**
	* Adds a supported handler
	* @param string $name The name of the handler
	* @param string $class The class which will handle it
	* @return static
	*/
	public function add(string $name, string $class) : static
	{
		$this->list[$name] = $class;

		if ($this->store && isset($this->handlers[$name])) {
			unset($this->handlers[$name]);
		}

		return $this;
	}

	/**
	* Alias for add()
	* @see Handlers::add()
	*/
	public function set(string $name, string $class) : static
	{
		return $this->add($name, $class);
	}

	/**
	* Removes a supported handler
	* @param string $name The name of the handler
	* @return static
	*/
	public function remove(string $name) : static
	{
		if ($this->list[$name]) {
			unset($this->list[$name]);
		}

		if ($this->store && isset($this->handlers[$name])) {
			unset($this->handlers[$name]);
		}

		return $this;
	}

	/**
	* Returns the handler
	* @param string $name The name of the handler
	* @param mixed $args Arguments to pass to the handler's constructor
	* @return mixed The handler
	*/
	public function get(string $name, ...$args)
	{
		if ($this->store && isset($this->handlers[$name])) {
			return $this->handlers[$name];
		}
		if (!isset($this->list[$name])) {
			throw new \Exception("Unknown handler '{$name}'");
		}

		$handler = null;
		if (is_string($this->list[$name])) {
			$class = $this->list[$name];

			$args[] = $this->app;
			$handler = new $class(...$args);
		} elseif (is_array($this->list[$name])) {
			$handler = [$this, reset($this->list[$name])];
		} else {
			$handler = $this->list[$name];
		}

		if ($this->store) {
			$this->handlers[$name] = $handler;
		}

		return $handler;
	}

	/**
	* Calls the handler and return the value
	* @param string $name The name of the handler
	* @param mixed $args Arguments to pass to the handler
	* @return mixed
	*/
	public function getValue(string $name, ...$args)
	{
		$handler = $this->get($name);

		$func = $handler;
		if (is_object($handler)) {
			$func = [$handler, $this->method];
		}

		return call_user_func_array($func, $args);
	}

	/**
	* Returns a value
	* @param mixed $value The value
	* @param mixed $name The name of the handler
	* @param mixed $args The arguments to pass to the handler, if any
	*/
	public function getMultiValue($value, string $name, ...$args) : mixed
	{
		return $this->map($value, function ($value) use ($name, $args) {
			$args = array_merge([$value], $args);

			return $this->getValue($name, ...$args);
		});
	}

	/**
	* Maps a value [scalar|array] to a callback
	* @param mixed $value The value
	* @param callable $callback The callback function
	*/
	public function map($value, callable $callback)
	{
		if (is_array($value)) {
			return array_map($callback, $value);
		}

		return $callback($value);
	}
}
