<?php
/**
* The Javascript Urls Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
 * The Document's Javascript Urls Class
 * Class containing the javascript urls/stylesheets used by a document
 */
class Javascript extends Urls
{
	/**
	 * {@inheritdoc}
	 */
	protected string $push_type = 'script';

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
	public function outputUrl(string $url, bool $async = false, bool $defer = false)
	{
		$async_str = '';
		$defer_str = '';
		if ($async) {
			$async_str = ' async';
		}
		if ($defer) {
			$defer_str = ' defer';
		}

		echo '<script type="text/javascript" src="' . $this->app->escape->html($url) . '"' . $async_str . $defer_str . '></script>' . "\n";

		return $this;
	}

	/**
	 * Encodes $data
	 * @param mixed $data The data to encode
	 * @return string The encoded data
	 */
	public function encode($data) : string
	{
		return json_encode($data);
	}

	/**
	 * Decodes $data
	 * @param string $data The data to decode
	 * @return mixed The decoded string
	 */
	public function decode(string $data)
	{
		return json_decode($data, true);
	}
}
