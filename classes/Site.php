<?php
/**
* The Site Class
* @package Mars
*/

namespace Mars;

/**
* The Site Class
* The system's site object
*/
class Site
{
	use AppTrait;

	/**
	* @var string $dir The location on the disk where the site is installed Eg: /var/www/mysite
	*/
	public string $dir = '';

	/**
	* @var string $url The site's url. Eg: http://mydomain.com/mars
	*/
	public string $url = '';

	/**
	* @var string $url_static The url from where static content is served
	*/
	public string $url_static = '';

	/**
	* @var string $url_rel The relative site url. Unlike $url it doesn't contain the scheme
	*/
	public string $url_rel = '';

	/**
	* Builds the site object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->dir = dirname(__DIR__, 3) . '/';
	}

	/**
	* Sets the urls
	*/
	public function setUrls()
	{
		$this->url = $this->getSiteUrl();
		$this->url_rel = $this->app->uri->stripScheme($this->url);
		$this->url_static = $this->getStaticUrl();
	}

	/**
	* Returns the site's base url
	* @return string The base url
	*/
	protected function getSiteUrl() : string
	{
		return $this->app->config->site_url;
	}

	/**
	* Returns the site's base static url
	* @return string The base static url
	*/
	protected function getStaticUrl() : string
	{
		if ($this->app->config->site_url_static) {
			return $this->app->config->site_url_static;
		}

		return $this->url_rel;
	}
}
