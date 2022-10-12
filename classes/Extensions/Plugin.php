<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
* The Plugin Class
* Object corresponding to a plugin extension
*/
class Plugin extends Extension
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
	public function __construct(string $name, App $app = null)
	{
		$this->title = $name;
		
		parent::__construct($name, $app);

		$this->addHooks();
	}

	/**
	* Adds the hooks
	*/
	protected function addHooks()
	{
		$this->app->plugins->addHooks($this, $this->hooks);
	}
}
