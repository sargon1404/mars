<?php
/**
* The System's Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\Plugin;

/**
* The System's Plugins Class
* Container for the system's plugins
*/
class Plugins
{
	use \Mars\AppTrait;
	use \Mars\Plugins;

	/**
	* Builds the plugins object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (defined('DISABLE_PLUGINS')) {
			return;
		}

		$this->enabled = true;
	}

	/**
	* Loads the plugins
	*/
	public function load()
	{
		if (!$this->enabled) {
			return;
		}
		if (!isset($this->app->config->plugins)) {
			return;
		}

		$this->loadPlugins($this->app->config->plugins);
	}

	/**
	* Loads plugins
	* @param array Array with the plugin objects to load
	*/
	protected function loadPlugins(array $plugins)
	{
		if (!$plugins) {
			return;
		}

		$namespace = $this->app->extensions_namespace . static::$namespace;

		$pid = 1;
		foreach ($plugins as $name) {
			$class = $namespace . App::namespace($plugin->name) . "\\" . App::strToClass($plugin->name);

			$plugin = new $class($name, $pid);

			if (!$plugin instanceof Plugin) {
				throw new \Exception("Class {$class} must extend class Plugin");
			}

			$this->plugins[$pid] = $plugin;

			$pid++;
		}
	}
}
