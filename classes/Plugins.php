<?php
/**
* The Plugins "Class"
* @package Mars
*/

namespace Mars;

/**
* The Plugins "Class"
* Trait implementing the Plugins functionality
*/
trait Plugins
{
	/**
	* @var array $exec_time The execution time for all plugins is stored here. Set only if debug is enabled
	*/
	public $exec_time = [];

	/**
	* @var bool $enabled Will be set to true if plugins are enabled
	*/
	public $enabled = false;

	/**
	* @var array $plugins Array holding the plugin objects
	*/
	protected $plugins = [];

	/**
	* @var array $hooks Registered hooks
	*/
	protected $hooks = [];

	/**
	* @var string $output_prefix The prefix to use when using the output function
	*/
	protected $output_prefix = '';

	/**
	* @var null $output_obj Object to pass as the first argument when output calls are made
	*/
	protected $output_obj = null;

	/**
	* @var string $namespace The namespace used to load plugins
	*/
	protected static $namespace = "plugins\\";

	/**
	* Returns the list of loaded plugins
	* @return array
	*/
	public function getPlugins() : array
	{
		return $this->plugins;
	}

	/**
	* Registers hooks for execution
	* @param int $pid The id of the plugin which registers the hooks
	* @param array $hooks The names of the hooks at which the plugin will be attached
	* @return $this
	*/
	public function addHooks(int $pid, array $hooks)
	{
		if (!$this->enabled) {
			return $this;
		}

		foreach ($hooks as $hook) {
			if (!isset($this->hooks[$hook])) {
				$this->hooks[$hook] = [];
			}

			$this->hooks[$hook][] = $pid;
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
	* Executes the hooks
	* @param string $hook The name of the hook
	* @param array $args The arguments to be passed to the plugins
	* @param int $return_arg The index of the argument used as a return value, if any
	* @return mixed The value returned by the plugin
	*/
	protected function exec(string $hook, array &$args, int $return_arg = null)
	{
		if (!$this->enabled || !$this->hooks || !isset($this->hooks[$hook])) {
			if ($return_arg !== null) {
				return $args[$return_arg];
			}

			return;
		}

		if ($this->app->config->development_plugins) {
			\var_dump($hook);
		}

		$ret = null;
		$orig_value = null;

		//store the original value of the return arg
		if ($return_arg !== null) {
			$orig_value = $args[$return_arg];
		}

		foreach ($this->hooks[$hook] as $pid) {
			if ($this->app->config->debug) {
				$this->startTimer();
			}

			$p_ret = call_user_func_array([$this->plugins[$pid], $hook], $args);

			if ($p_ret !== null) {
				if ($return_arg !== null) {
					$args[$return_arg] = $p_ret;
				}

				$ret = $p_ret;
			}

			if ($this->app->config->debug) {
				$this->endTimer($pid);
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
	* @param int $pid The plugin's id
	*/
	protected function endTimer(int $pid)
	{
		if (!isset($this->exec_time[$pid])) {
			$this->exec_time[$pid] = 0;
		}

		$this->exec_time[$pid]+= $this->app->timer->end('plugin_run');
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
	* Similar to run, but intended to be used in templates.
	* The first argument is always the output object (usually the view)
	* @param string $hook The name of the hook
	* @param mixed $args The arguments to be passed to the plugins
	*/
	public function output(string $hook, &...$args)
	{
		array_unshift($args, $this->output_obj);

		return $this->exec($this->output_prefix . $hook, $args);
	}

	/**
	* Sets the plugin's output data
	* @param string $prefix The prefix
	* @param object $obj The object
	* @return $this
	*/
	public function setOutputData($prefix, $obj)
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
