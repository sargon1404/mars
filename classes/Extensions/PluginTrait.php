<?php
/**
* The Plugin Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The Plugin Trait
 * Trait implementing the Plugin functionality
 */
trait PluginTrait
{
	use isInsideAModuleTrait, isSingleClassTrait {
		isInsideAModuleTrait::__construct as __constructModule;
		isSingleClassTrait::__construct as __constructParent;
	}
	use \Mars\Extensions\Abilities\LanguagesTrait;
	use \Mars\Extensions\Abilities\TemplatesTrait;
	
	/**
	 * @var string $title The plugin's title
	 */
	public string $title = '';

	/**
	 * @var array $hooks Array listing the defined hooks in the format [hook_name => method]
	 */
	protected array $hooks = [];

	/**
	 * @internal
	 */
	protected static string $type = 'plugin';

	/**
	 * @internal
	 */
	protected static string $base_dir = 'plugins';

	/**
	 * @internal
	 */
	protected static string $namespace = "Plugins";

	/**
	 * Builds the plugin
	 * @param App $app The app object
	 */
	public function __construct(App $app = null)
	{
		$this->__constructParent($app);

		$this->addHooks();
	}

	/**
	 * Adds the hooks
	 */
	protected function addHooks()
	{
		$this->app->plugins->addHooks($this, $this->hooks);
	}
}
