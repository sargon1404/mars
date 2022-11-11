<?php
/**
* The Headers Response Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;

/**
* The Headers Response Class
* Handles the response headers
*/
class Headers
{
	use \Mars\AppTrait;
	use \Mars\Lists\ListTrait;

	/**
	* Builds the Cookie Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->config->custom_headers) {
			$this->list = $this->app->config->custom_headers;
		}
	}

	/**
	* Outputs the headers
	*/
	public function output()
	{
		foreach ($this->list as $name => $value) {
			header("{$name}: $value");
		}
	}
}
