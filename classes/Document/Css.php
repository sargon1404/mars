<?php
/**
* The Css Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends Urls
{
	/**
	* Builds the javascript object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->version = $this->app->config->javascript_version;
	}

	/**
	* @see \Mars\Document\Urls::outputUrl()
	* {@inheritdoc}
	*/
	public function outputUrl(string $url, $version = true, bool $async = false, bool $defer = false)
	{
		$url = $this->getUrl($url, $version);

		echo '<link rel="stylesheet" type="text/css" href="' . $this->app->escape->html($url) . '" />' . "\n";

		return $this;
	}
}
