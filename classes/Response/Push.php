<?php
/**
* The Server Push Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;

/**
 * The Server Push Class
 * Pushes assets if http2
 */
class Push
{
	use \Mars\AppTrait;
	use \Mars\Lists\ListTrait {
		\Mars\Lists\ListTrait::add as addToList;
		\Mars\Lists\ListTrait::remove as removeFromList;
	}

	/**
	 * @var bool $enabled True if the http2 push is enabled
	 */
	protected bool $enabled = false;

	/**
	 * Builds the Push object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->is_bin && $this->app->config->http2_push && $this->app->is_http2) {
			$this->enabled = true;
		}
	}

	/**
	 * Adds an url to the http2 push list
	 * @param string $url The url to add
	 * @param string $type The url's type
	 * @return static
	 */
	public function add(string $url, string $type) : static
	{
		if (!$this->enabled) {
			return $this;
		}

		return $this->addToList($url, $type);
	}

	/**
	 * Removes an url from the http2 push list
	 * @param string $url The url to remove
	 * @return static
	 */
	public function remove(string $url) : static
	{
		if (!$this->enabled) {
			return $this;
		}

		return $this->removeFromList($url);
	}

	/**
	 * Outputs the headers
	 */
	public function output()
	{
		if (!$this->enabled) {
			return;
		}

		foreach ($this->list as $url => $type) {
			header("Link: <{$url}>; rel=preload; as= {$type}", false);
		}
	}
}
