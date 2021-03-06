<?php
/**
* The Booter Class
* @package Mars
*/

namespace Mars;

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter
{
	use AppTrait;

	/**
	* Initializes the minimum number of objects needed to server content from the cache
	* @return $this
	*/
	public function minimum()
	{
		$this->app->timer = new Timer($this->app);
		$this->app->uri = new Uri($this->app);

		$this->app->config = new Config($this->app);
		$this->app->config->read();

		$this->app->setData();

		$this->app->serializer = new Serializer($this->app);
		$this->app->memcache = new Memcache($this->app);
		$this->app->caching = new Caching($this->app);

		return $this;
	}

	/**
	* Initializes the libraries
	*/
	public function libraries()
	{
		require_once($this->app->libraries_dir . '/php/vendor/autoload.php');
	}

	/**
	* Initializes the db & sql objects
	* @return $this
	*/
	public function db()
	{
		$this->app->db = new Db($this->app);
		$this->app->sql = new Sql($this->app);

		$this->app->setDataAfterDb();

		return $this;
	}

	/**
	* Initializes the base objects
	* @return $this
	*/
	public function base()
	{
		$this->app->log = new Log($this->app);
		$this->app->time = new Time($this->app);
		$this->app->encoder = new Encoder($this->app);
		$this->app->random = new Random($this->app);
		$this->app->filter = new Filter($this->app);
		$this->app->escape = new Escape($this->app);
		$this->app->validator = new Validator($this->app);
		$this->app->format = new Format($this->app);
		$this->app->file = new File($this->app);
		$this->app->html = new Html($this->app);
		$this->app->ui = new Ui($this->app);
		$this->app->text = new Text($this->app);

		return $this;
	}

	/**
	* Initializes the environment objects
	* @return $this
	*/
	public function env()
	{
		$this->app->accelerator = new Accelerator($this->app);

		$this->app->session = new Session($this->app);
		$this->app->session->start();

		$this->app->device = new Device($this->app);
		$this->app->request = new Request($this->app);
		$this->app->response = new Response($this->app);
		$this->app->cache = new Cache($this->app);

		return $this;
	}

	/**
	* Initializes the document properties
	*/
	public function document()
	{
		$this->app->title = new Document\Title;
		$this->app->meta = new Document\Meta;

		$this->app->css = new Document\Css($this->app);
		$this->app->javascript = new Document\Javascript($this->app);
		$this->app->rss = new Document\Rss;

		$this->app->errors = new Alerts\Errors;
		$this->app->messages = new Alerts\Messages;
		$this->app->warnings = new Alerts\Warnings;
		$this->app->notifications = new Alerts\Notifications;
	}

	/**
	* Initializes the system objects
	* @return $this
	*/
	public function system()
	{
		$this->app->plugins = new System\Plugins($this->app);
		$this->app->plugins->load();

		$this->app->lang = new System\Language($this->app);
		$this->app->theme = new System\Theme($this->app);

		$this->app->router = new Router;

		return $this;
	}
}
