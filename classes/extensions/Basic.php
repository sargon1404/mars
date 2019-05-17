<?php
/**
* The Extension Basic Class
* @package Mars
*/

namespace Mars\Extensions;

/**
* The Extension Class
* Base class for all basic extensions
*/
abstract class Basic extends \Mars\Entity
{
	use \Mars\AppTrait;
	use Body;

	/**
	* Builds the extension
	* @param string $name The name of the exension
	*/
	public function __construct(string $name)
	{
		$this->app = $this->getApp();

		$this->name = $name;

		$this->prepare();
	}
}
