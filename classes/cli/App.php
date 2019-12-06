<?php
/**
* The Cli App Class
* @package Mars
*/

namespace Mars\Cli;

/**
* The Cli App Class
*/
class App extends \Mars\App
{
	/**
	* @var bool $is_cli True if the app is run as a cli script
	*/
	public bool $is_cli = true;

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritDoc}
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}
}
