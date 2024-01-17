<?php
/**
* The Plugin Class
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
 * The Plugin Class
 * Object corresponding to a plugin extension
 */
abstract class Plugin extends Extension
{
	use PluginTrait;
}
