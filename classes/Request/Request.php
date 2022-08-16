<?php
/**
* The REQUEST Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
* The REQUEST Request Class
* Handles the $_POST interactions
*/
class Request extends Base
{
	use \Mars\AppTrait;

	/**
	* Builds the Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_REQUEST;
	}
}
