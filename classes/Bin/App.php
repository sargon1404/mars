<?php
/**
* The Bin App Class
* @package Mars
*/

namespace Mars\Bin;

/**
* The Bin App Class
*/
class App extends \Mars\App
{
	/**
	* @var bool $is_bin True if the app is run as a bin script
	*/
	public bool $is_bin = true;

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritdoc}
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}
}
