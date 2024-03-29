<?php
/**
* The Booter Class
* @package Mars
*/

namespace Mars;

use Mars\Alerts\Errors;
use Mars\Alerts\Info;
use Mars\Alerts\Messages;
use Mars\Alerts\Warnings;

/**
 * The Booter Class
 * Initializes the system's required classes
 */
class AppBooter
{
	use AppTrait;

	/**
	 * Initializes the minimum number of objects needed to server content from the cache
	 */
	public function minimum()
	{
		$this->app->timer = new Timer($this->app);
		$this->app->uri = new Uri($this->app);
		$this->app->config = new Config($this->app);
		$this->app->serializer = new Serializer($this->app);
		$this->app->memcache = new Memcache($this->app);
	}

	/**
	 * Initializes the libraries
	 */
	public function libraries()
	{
		require_once($this->app->libraries_path . '/php/vendor/autoload.php');
	}

	/**
	 * Initializes the db & sql objects
	 */
	public function db()
	{
		$this->app->db = new Db($this->app);
		$this->app->sql = new Sql($this->app);
	}

	/**
	 * Initializes the base objects
	 */
	public function base()
	{
		$this->app->log = new Log($this->app);
		$this->app->time = new Time($this->app);
		$this->app->json = new Json($this->app);
		$this->app->random = new Random($this->app);
		$this->app->filter = new Filter($this->app);
		$this->app->escape = new Escape($this->app);
		$this->app->unescape = new Unescape($this->app);
		$this->app->format = new Format($this->app);
		$this->app->validator = new Validator($this->app);
		$this->app->file = new File($this->app);
		$this->app->dir = new Dir($this->app);
		$this->app->html = new Html($this->app);
		$this->app->ui = new Ui($this->app);
		$this->app->text = new Text($this->app);
		$this->app->debug = new Debug($this->app);
	}

	/**
	 * Initializes the environment objects
	 * @return static
	 */
	public function env()
	{
		$this->app->accelerator = new Accelerator($this->app);
		$this->app->session = new Session($this->app);
		$this->app->device = new Device($this->app);
		$this->app->request = new Request($this->app);
		$this->app->response = new Response($this->app);
		$this->app->cache = new Cache($this->app);
		$this->app->registry = new Registry($this->app);
	}

	/**
	 * Initializes the document properties
	 */
	public function document()
	{
		$this->app->document = new Document($this->app);
		$this->app->screens = new Screens($this->app);
		$this->app->errors = new Errors;
		$this->app->info = new Info;
		$this->app->messages = new Messages;
		$this->app->warnings = new Warnings;
	}

	/**
	 * Initializes the system objects
	 */
	public function system()
	{
		$this->app->lang = new System\Language($this->app);
		$this->app->theme = new System\Theme($this->app);

		$this->app->router = new Router($this->app);
		$this->app->bin = new Bin($this->app);

		$this->app->plugins = new System\Plugins($this->app);
		$this->app->plugins->load();
	}
}
