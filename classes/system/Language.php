<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
* The System's Language Class
*/
class Language extends \Mars\Extensions\Basic
{
	use \Mars\Language;

	/**
	* Builds the language
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		parent::__construct($this->app->config->lang);
	}
}
