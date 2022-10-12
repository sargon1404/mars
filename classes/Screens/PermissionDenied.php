<?php
/**
* The Permission Denied Screen
* @package Mars
*/

namespace Mars\Screens;

/**
* The Permission Denied Screen
* Displays the Permission Denied screen
*/
class PermissionDenied
{
	use \Mars\AppTrait;

	/**
	* Displays the Permission Denied screen
	*/
	public function output()
	{
		echo 'Permission denied!' . "\n";
		die;
	}
}
