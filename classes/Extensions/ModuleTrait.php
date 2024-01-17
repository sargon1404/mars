<?php
/**
* The Module Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The Module Trait
 * Contains the functionality of a module
 */
trait ModuleTrait
{
	use \Mars\Extensions\Abilities\LanguagesTrait;
	use \Mars\Extensions\Abilities\TemplatesTrait;

	/**
	 * @internal
	 */
	protected static string $type = 'module';

	/**
	 * @internal
	 */
	protected static string $base_dir = 'modules';

	/**
	 * @internal
	 */
	protected static string $namespace = "Modules";
}
