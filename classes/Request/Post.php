<?php
/**
* The POST Request Class
* @package Mars
*/

namespace Mars\Request;

use Mars\App;

/**
* The POST Request Class
* Handles the $_POST interactions
*/
class Post extends Base
{
	use \Mars\AppTrait;

	/**
	* Builds the Post Request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->data = &$_POST;
	}
}
