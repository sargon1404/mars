<?php
/**
* The Errors Class
* @package Mars
*/

namespace Mars\Alerts;

/**
* The Errors Class
* Errors container
*/
class Errors extends \Mars\Alerts\Alerts
{
	/**
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->alerts) {
			return false;
		}

		return true;
	}
}
