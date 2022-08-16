<?php
/**
* The Supported Handlers Trait
* @package Mars
*/

namespace Mars;

/**
* The Supported Handlers Trait
* Trait implementing supported handlers functionality.
* Classes using this trait must set these properties:
* protected array $supported_handlers = [];
*/
trait HandlersTrait
{
	/**
	* @var array $supported_rules The list of supported rules in the name => class format
	*/
	//protected array $supported_handlers = [];

	/**
	* @var array $handlers Array storing the handler objects
	*/
	protected array $handlers = [];

	/**
	* @var string $handler_method The method called on a handler object when getValue is called
	*/
	protected string $handler_method = 'get';

	/**
	* Adds a supported handler
	* @param string $name The name of the handler
	* @param string $class The class which will handle it
	* @return static
	*/
	public function addSupportedHandler(string $name, string $class) : static
	{
		$this->supported_handlers[$name] = $class;

		if (isset($this->handlers[$name])) {
			unset($this->handlers[$name]);
		}

		return $this;
	}

	/**
	* Removes a supported handler
	* @param string $name The name of the handler
	* @return static
	*/
	public function removeSupportedRule(string $name) : static
	{
		if ($this->supported_handlers[$name]) {
			unset($this->supported_handlers[$name]);
		}

		if (isset($this->handlers[$name])) {
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
	protected function getHandler(string $name, ...$args)
	{
		if (isset($this->handlers[$name])) {
			return $this->handlers[$name];
		}
		if (!isset($this->supported_handlers[$name])) {
			throw new \Exception("Unknown handler '{$name}'");
		}

		if (is_string($this->supported_handlers[$name])) {
			$class = $this->supported_handlers[$name];

			$args[] = $this->app;
			$this->handlers[$name] = new $class(...$args);
		} elseif (is_array($this->supported_handlers[$name])) {
			$this->handlers[$name] = [$this, reset($this->supported_handlers[$name])];
		} else {
			$this->handlers[$name] = $this->supported_handlers[$name];
		}

		return $this->handlers[$name];
	}

	/**
	* @param string $name The name of the handler
	* @param mixed $args Arguments to pass to the handler
	* @return mixed
	*/
	protected function getValue(string $name, ...$args)
	{
		$handler = $this->getHandler($name);

		$func = $handler;
		if (is_object($handler)) {
			$func = [$handler, $this->handler_method];
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
