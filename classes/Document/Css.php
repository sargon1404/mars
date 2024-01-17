<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
 * The Document's Css Urls Class
 * Class containing the css urls/stylesheets used by a document
 */
class Css extends Urls
{
	/**
	 * {@inheritdoc}
	 */
	protected string $push_type = 'style';

	/**
	 * Builds the javascript object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->version = $this->app->config->css_version;
	}

	/**
	 * @see \Mars\Document\Urls::outputUrl()
	 * {@inheritdoc}
	 */
	public function outputUrl(string $url, bool $async = false, bool $defer = false)
	{
		echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url) . '" />' . "\n";

		return $this;
	}
}
