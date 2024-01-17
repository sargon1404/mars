<?php
/**
* The isInsideAModule Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The isInsideAModule Trait
 * Initializes the module the extension belongs to
 */
trait isInsideAModuleTrait
{
	/**
	 * @param Module $module The module the extension belongs to
	 */
	public Module $module;

	/**
	 * Builds the extension
	 * @param string $module_name The name of the module the extension belongs to
	 * @param string $name The name of the exension
	 * @param App $app The app object
	 */
	public function __construct(string $module_name, string $name = '', App $app = null)
	{
		$this->app = $app ?? $this->getApp();

		$this->name = $name;

		if (!$this->name) {
			$parts = explode('/', $module_name);
			if (count($parts) > 1) {
				[$module_name, $this->name] = $parts;
			}
		}

		$this->module = new Module($module_name, $this->app);

		$this->prepare();
	}

	/**
	 * {@inheritdoc}
	 * @see \Mars\Extensions\ExtensionTrait::getRootPath()
	 */
	public function getRootPath() : string
	{
		return $this->module->path;
	}

	/**
	 * {@inheritdoc}
	 * @see \Mars\Extensions\ExtensionTrait::getRootUrl()
	 */
	public function getRootUrl() : string
	{
		return $this->module->url;
	}

	/**
	 * {@inheritdoc}
	 * @see \Mars\Extensions\ExtensionTrait::getRootUrlStatic()
	 */
	public function getRootUrlStatic() : string
	{
		return $this->module->url_static;
	}

	/**
	 * {@inheritdoc}
	 * @see \Mars\Extensions\ExtensionTrait::getRootNamespace()
	 */
	public function getRootNamespace() : string
	{
		return $this->module->getRootNamespace() . App::getClass($this->module->name) . '\\' . static::$namespace . '\\';
	}
}
