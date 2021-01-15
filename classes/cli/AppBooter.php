<?php
/**
* The Booter Class
* @package Mars
*/

namespace Mars\Cli;

use Mars\Cli;
use Mars\System\Plugins;
use Mars\System\Language;

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter extends \Mars\AppBooter
{
	/**
	* Initializes the system objects
	* @return $this
	*/
	public function system()
	{
		$this->app->plugins = new Plugins($this->app);
		$this->app->plugins->load();

		$this->app->lang = new Language($this->app);
		
		$this->app->cli = new Cli($this->app);

		return $this;
	}
}
