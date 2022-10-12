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
		$this->engine = new Templates;
		var_dump($this->engine);
		die;
		parent::__construct($app->config->theme, $app);
		
		$this->engine = new Templates;

		$this->app->plugins->run('system_theme_construct', $this);
	}
}
