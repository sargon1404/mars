<?php
/**
* The GET Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
* The GET Request Class
* Handles the $_GET interactions
*/
class Get extends Base
{
	use \Mars\AppTrait;

	/**
	* Builds the Get Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_GET;
	}
}
