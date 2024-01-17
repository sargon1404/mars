<?php
/**
* The Php Session Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App;

/**
 * The Php Session Class
 * Session driver which uses the default php implementation
 */
class Php implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	 * Builds the Php Session driver
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if ($this->app->config->session_save_path) {
			session_save_path($this->app->config->session_save_path);
		}

		if ($this->app->config->session_cookie_path || $this->app->config->session_cookie_domain) {
			session_set_cookie_params(0, $this->app->config->session_cookie_path, $this->app->config->session_cookie_domain);
		}

		if ($this->app->config->session_name) {
			session_name($this->app->config->session_name);
		}
	}
}
