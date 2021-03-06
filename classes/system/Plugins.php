<?php
/**
* The Plugins Class
* @package Mars
*/

namespace Mars\System;

/**
* The Plugins Class
* Class implementing the Plugins functionality
*/
class Plugins
{
	use \Mars\Plugins;
		
	/**
	* @var string $namespace The namespace used to load plugins
	*/
	protected static string $namespace = "App\\Extensions\\Plugins\\";
}
