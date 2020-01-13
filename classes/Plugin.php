<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars;

/**
* The Plugin Class
* Object corresponding to a plugin extension
*/
class Plugin extends \Mars\Extensions\Basic
{
	/**
	* @var int $pid The plugin's id
	*/
	public int $pid = 0;

	/**
	* @var array $hooks Array listing the defined hooks
	*/
	protected array

 $hooks = [];

	/**
	* @internal
	*/
	protected static string $type = 'plugin';

	/**
	* @internal
	*/
	protected static string $base_dir = 'plugins';

	/**
	* Builds the plugin
	* @param string $name The name of the plugin
	* @param int $pid The plugin's id
	*/
	public function __construct(string $name, int $pid)
	{
		$this->app = $this->getApp();

		$this->pid = $pid;

		$this->load($name);

		$this->addHooks();
	}

	/**
	* Adds the hooks
	*/
	protected function addHooks()
	{
		$this->app->plugins->addHooks($this->pid, $this->hooks);
	}
}
