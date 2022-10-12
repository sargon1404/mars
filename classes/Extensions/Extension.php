<?php
/**
* The Extension Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
* The Extension Class
* Base class for all basic extensions
*/
abstract class Extension extends \Mars\Entity
{
	use \Mars\AppTrait;
	use ExtensionTrait;

	/**
	* Builds the extension
	* @param string $name The name of the exension
	* @param App $app The app object
	*/
	public function __construct(string $name, App $app = null)
	{
		$this->app = $app ?? $this->getApp();

		$this->name = $name;

		$this->prepare();
	}
}
