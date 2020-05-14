<?php
/**
* The Plugins Trait
* @package Mars
*/

namespace Mars;

/**
* The Plugins Trait
* Trait implementing the Plugins functionality
*/
trait Plugins
{
	/**
	* @var array $exec_time The execution time for all plugins is stored here. Set only if debug is enabled
	*/
	public array $exec_time = [];

	/**
	* @var bool $enabled Will be set to true if plugins are enabled
	*/
	public bool $enabled = false;

	/**
	* @var array $plugins Array holding the plugin objects
	*/
	protected array $plugins = [];

	/**
	* @var array $hooks Registered hooks
	*/
	protected array $hooks = [];

	/**
	* @var array $hooks_exec_time The execution time for all hooks is stored here. Set only if debug is enabled
	*/
	public array $hooks_exec_time = [];

	/**
	* @var array $exec_hooks Executed hooks. Set only if debug is enabled
	*/
	protected array $exec_hooks = [];

	/**
	* @var string $output_prefix The prefix to use when using the output function
	*/
	protected string $output_prefix = '';

	/**
	* @var object $output_obj Object to pass as the first argument when output calls are made
	*/
	protected ?object $output_obj = null;

	/**
	* Builds the plugins object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->enabled = $this->app->config->plugins_enable;
	}

	/**
	* Loads the plugins
	*/
	public function load()
	{
		if (!$this->enabled) {
			return;
		}

		$plugins = $this->app->config->plugins ?? [];
		if (!$plugins) {
			return;
		}

		foreach ($plugins as $name) {
			$class = static::$namespace . App::strToClass($name) . "\\" . App::strToClass($name);

			$plugin = new $class($this->app, $name);

			if (!$plugin instanceof Plugin) {
				throw new \Exception("Class {$class} must extend class Plugin");
			}

			$this->plugins[$name] = $plugin;
		}
	}

	/**
	* Returns the list of loaded plugins
	* @return array
	*/
	public function getPlugins() : array
	{
		return $this->plugins;
	}

	/**
	* Returns the list of executed hooks
	* @return array
	*/
	public function getHooks() : array
	{
		return $this->exec_hooks;
	}

	/**
	* Registers hooks for execution
	* @param string $name The name of the plugin which registers the hooks
	* @param array $hooks The names of the hooks at which the plugin will be attached
	* @return $this
	*/
	public function addHooks(string $name, array $hooks)
	{
		if (!$this->enabled) {
			return $this;
		}

		foreach ($hooks as $hook => $method) {
			if (!isset($this->hooks[$hook])) {
				$this->hooks[$hook] = [];
			}

			$this->hooks[$hook][] = [$name, $method];
		}

		return $this;
	}

	/**
	* Runs a hooks
	* @param string $hook The name of the hook
	* @param mixed $args The arguments to be passed to the plugins
	* @return mixed The value returned by the plugin
	*/
	public function run(string $hook, &...$args)
	{
		return $this->exec($hook, $args);
	}

	/**
	* Filters a value, by running the hooks. Unlike run(), the args are not passed by reference
	* @param string $hook The name of the hook
	* @param mixed $args The arguments to be passed to the plugins
	* @return mixed The filtered value
	*/
	public function filter(string $hook, ...$args)
	{
		return $this->exec($hook, $args, 0);
	}

	/**
	* Executes the hooks
	* @param string $hook The name of the hook
	* @param array $args The arguments to be passed to the plugins
	* @param int $return_arg The index of the argument used as a return value, if any
	* @return mixed The value returned by the plugin
	*/
	protected function exec(string $hook, array &$args, int $return_arg = null)
	{
		if (!$this->enabled) {
			if ($return_arg !== null) {
				return $args[$return_arg];
			}

			return null;
		}

		if ($this->app->config->debug) {
			$this->exec_hooks[] = $hook;
		}

		if (!$this->hooks || !isset($this->hooks[$hook])) {
			if ($return_arg !== null) {
				return $args[$return_arg];
			}

			return null;
		}

		$ret = null;
		$orig_value = null;

		//store the original value of the return arg
		if ($return_arg !== null) {
			$orig_value = $args[$return_arg];
		}

		foreach ($this->hooks[$hook] as $data) {
			if ($this->app->config->debug) {
				$this->startTimer();
			}

			[$name, $method] = $data;

			$p_ret = call_user_func_array([$this->plugins[$name], $method], $args);

			if ($p_ret !== null) {
				if ($return_arg !== null) {
					$args[$return_arg] = $p_ret;
				}

				$ret = $p_ret;
			}

			if ($this->app->config->debug) {
				$this->endTimer($name, $hook);
			}
		}

		//reset the value of the return arg, to the orignal value
		if ($return_arg !== null) {
			$args[$return_arg] = $orig_value;
		}

		return $ret;
	}

	/**
	* Starts the timer, if debug is on
	*/
	protected function startTimer()
	{
		$this->app->timer->start('plugin_run');
	}

	/**
	* Ends the timer and stores the elapes time in exec_time, if debug is on
	* @param string $name The plugin's name
	* @param string $hook The hook's name
	*/
	protected function endTimer(string $name, string $hook)
	{
		$time = $this->app->timer->end('plugin_run');

		$this->exec_time[$name] ??= 0;
		$this->hooks_exec_time[$hook] ??= 0;

		$this->exec_time[$name]+= $time;
		$this->hooks_exec_time[$hook] += $time;
	}

	/**
	* Similar to run, but intended to be used in templates.
	* The first argument is always the output object (usually the view)
	* @param string $hook The name of the hook
	* @param mixed $args The arguments to be passed to the plugins
	*/
	public function output(string $hook, &...$args)
	{
		array_unshift($args, $this->output_obj);

		$name = $this->output_prefix . '_' . $hook;

		return $this->exec($name, $args);
	}

	/**
	* Sets the plugin's output data
	* @param string $prefix The prefix
	* @param object $obj The object
	* @return $this
	*/
	public function setOutputData(string $prefix, object $obj)
	{
		$this->output_prefix = $prefix;
		$this->output_obj = $obj;

		return $this;
	}

	/**
	* Clears the output data
	* @return $this
	*/
	public function clearOutputData()
	{
		$this->output_prefix = '';
		$this->output_obj = null;

		return $this;
	}
}
