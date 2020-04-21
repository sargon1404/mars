<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\{App, Templates};

/**
* The System's Theme Class
*/
class Theme extends \Mars\extensions\Basic
{
	use \Mars\Theme;

	/**
	* Builds the theme
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->engine = new Templates;

		parent::__construct($this->app->config->theme);

		$this->app->plugins->run('system_theme_construct', $this);
	}
}
