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
class Handlers extends HandlersList
{
	use AppTrait;

	/**
	* @var bool $store If true, the handlers will be stored in $this->handlers
	*/
	public bool $store = true;

	/**
	* @var string $interface_name The interface the driver must implement
	*/
	protected string $interface_name = '';

	/**
	* @var array $handlers Array storing the handler objects, if $store is true
	*/
	protected array $handlers = [];

	/**
	* Builds the handler object
	* @param array $list The list of supported handlers
	* @param App $app The app object
	*/
	public function __construct(array $list, App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->list = $list;
	}

	/**
	* Sets the interface the handler must implement
	* @param string $interface_name The name of the interface
	* @return static
	*/
	public function setInterface(string $interface_name) : static
	{
		$this->interface_name = $interface_name;

		return $this;
	}

	/**
	* Determines if the handlers will be stored for future uses
	* @param bool $store If true, the handlers will be stored in $this->handlers
	* @return static
	*/
	public function setStore(bool $store) : static
	{
		$this->store = $store;

		return $this;
	}

	/**
	* @see \Mars\HandlersList::add()
	* {@inheritdoc}
	*/
	public function add(string $name, string $class) : static
	{
		if ($this->store && isset($this->handlers[$name])) {
			unset($this->handlers[$name]);
		}

		return parent::add($name, $class);
	}

	/**
	* @see \Mars\HandlersList::remove()
	* {@inheritdoc}
	*/
	public function remove(string $name) : static
	{
		if ($this->store && isset($this->handlers[$name])) {
			unset($this->handlers[$name]);
		}

		return parent::remove($name);
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

			if ($this->interface_name) {
				if (!$handler instanceof $this->interface_name) {
					throw new \Exception("Handler {$class} must implement interface {$this->interface_name}");
				}
			}
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
