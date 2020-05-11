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
	* @var string $title The plugin's title
	*/
	public string $title = '';

	/**
	* @var array $hooks Array listing the defined hooks in the format [hook_name => method]
	*/
	protected array $hooks = [];

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
	* @param App $app The app object
	* @param string $name The name of the plugin
	*/
	public function __construct(App $app, string $name)
	{
		$this->app = $app;
		$this->name = $name;
		$this->title = $name;

		$this->prepare();

		$this->addHooks();
	}

	/**
	* Adds the hooks
	*/
	protected function addHooks()
	{
		$this->app->plugins->addHooks($this->name, $this->hooks);
	}
}
