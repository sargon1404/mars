<?php
/**
* The ENV Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
* The ENV Class
* Handles the $_ENV interactions
*/
class Env extends Base
{
	use \Mars\AppTrait;

	/**
	* Builds the Server Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_ENV;
	}
}
