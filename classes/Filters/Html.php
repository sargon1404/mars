<?php
/**
* The Html Filter Class
* @package Mars
*/

namespace Mars\Filters;

use Mars\App;

/**
* The Html Filter Class
*/
class Html extends Filter
{
	/**
	* Builds the Html filter object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		require_once($this->app->libraries_path . 'php/vendor/ezyang/htmlpurifier/library/HTMLPurifier.includes.php');
	}

	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get($html, ...$params) : string
	{
		$allowed_elements = $params[0] ?? null;
		$allowed_attributes = $params[1] ?? null;
		$encoding = $params[2] ?? 'UTF-8';

		if ($allowed_elements === null) {
			$allowed_elements = $allowed_elements = $this->app->config->html_allowed_elements;
		}
		if ($allowed_attributes === null) {
			$allowed_attributes = $this->app->config->html_allowed_attributes;
		}

		$config = \HTMLPurifier_Config::createDefault();

		$config->set('Core.Encoding', $encoding);
		$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		$config->set('AutoFormat.RemoveEmpty', true);
		$config->set('Attr.AllowedRel', 'nofollow,follow');
		$config->set('Attr.AllowedFrameTargets', '_blank');
		$config->set('Attr.EnableID', true);

		if ($allowed_elements !== null) {
			$config->set('HTML.AllowedElements', $allowed_elements);
		}
		if ($allowed_attributes !== null) {
			$config->set('HTML.AllowedAttributes', $allowed_attributes);
		}

		//$this->app->plugins->run('filters_html_get_config', $config, $allowed_attributes, $allowed_elements, $this);

		$purifier = new \HTMLPurifier($config);
		$html = $purifier->purify($html);

		//return $this->app->plugins->filter('filters_html_get', $html, $this);

		return $html;
	}
}
