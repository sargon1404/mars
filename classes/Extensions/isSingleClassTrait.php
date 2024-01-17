<?php
/**
* The isSingleClassTrait Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The isSingleClassTrait Trait
 * Trait implementing functionality for extensions consisting of a single class file
 */
trait isSingleClassTrait
{
	/**
	 * Builds the extension
	 * @param App $app The app object
	 */
	public function __construct(App $app = null)
	{
		$this->app = $app ?? $this->getApp();

		//extract the plugin's name and module from it's path
		$rc = new \ReflectionClass($this);
		$filename = $rc->getFileName();

		$modules_path = $this->app->extensions_path . '/' . Module::getBaseDir();
		$rel_filename = $this->app->file->getRel($filename, $modules_path);

		$parts = explode('/', $rel_filename);
		
		$module_name = $parts[0];

		$name = '';
		if (count($parts) > 3) {
			$name = $parts[2];
		}

		$this->__constructModule($module_name, $name, $app);
	}

}
