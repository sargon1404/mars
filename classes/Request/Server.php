<?php
/**
* The SERVER Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
 * The SERVER Class
 * Handles the $_SERVER interactions
 */
class Server extends Base
{
	use \Mars\AppTrait;

	/**
	 * Builds the Server Request object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_SERVER;
	}
}
