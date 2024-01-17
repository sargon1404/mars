<?php
/**
* The Block Router Class
* @package Mars
*/

namespace Mars\Routers;

use Mars\App;

/**
 * The Block Router Class
 * Routes to a block
 */
class Block
{
	use \Mars\AppTrait;

	/**
	 * @var string $module_name The module's name
	 */
	protected string $module_name = '';

	/**
	 * @var string $name The block's name
	 */
	protected string $name = '';
	
	/**
	 * Builds the Block object
	 * @param string $name The block's name
	 * @param App $app The app object
	 */
	public function __construct(string $module_name, string $name, App $app)
	{
		$this->module_name = $module_name;
		$this->name = $name;
		$this->app = $app;
	}
	
	public function output()
	{
		$block = new \Mars\Extensions\Block($this->module_name, $this->name, $this->app);
		$block->output();
	}
}
