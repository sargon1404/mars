<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;
use Mars\Templates;

/**
* The System's Theme Class
*/
class Theme extends \Mars\Extensions\Theme
{
	/**
	* Builds the theme
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app->config->theme, $app);

		$this->templates = new Templates($app);

		include($this->path . 'init.php');

		$this->app->plugins->run('system_theme_construct', $this);
	}
}
