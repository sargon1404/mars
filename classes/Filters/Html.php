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
class Html
{
	use \Mars\AppTrait;

	/**
	 * Builds the Html filter object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;

		require_once($this->app->libraries_path . '/php/vendor/ezyang/htmlpurifier/library/HTMLPurifier.includes.php');
	}

	/**
	 * @see \Mars\Filter::html()
	 */
	public function filter(string $html, string $allowed_elements = null, string $allowed_attributes = null, string $encoding = 'UTF-8') : string
	{
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

		$this->app->plugins->run('filters_html_filter_config', $config, $allowed_attributes, $allowed_elements, $this);

		$purifier = new \HTMLPurifier($config);
		$html = $purifier->purify($html);

		return $this->app->plugins->filter('filters_html_filter', $html, $this);

		return $html;
	}
}
