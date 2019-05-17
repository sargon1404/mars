<?php
/**
* The System's Theme Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
* The System's Theme Class
*/
class Theme extends \mars\extensions\Basic
{
	use \Mars\Theme;

	/**
	* Builds the theme
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		parent::__construct($this->app->config->theme);
	}
}
