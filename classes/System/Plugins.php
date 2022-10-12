<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
* The Plugins Class
* Class implementing the Plugins functionality
*/
class Plugins
{
	use \Mars\Extensions\PluginsTrait;

	/**
	* @var string $namespace The namespace used to load plugins
	*/
	protected static string $namespace = "App\\Extensions\\Plugins\\";
}
