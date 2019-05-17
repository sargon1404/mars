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
	* @see \Mars\Document\Urls::outputUrl()
	* {@inheritDoc}
	*/
	public function outputUrl(string $url, bool $async = false, bool $defer = false)
	{
		echo '<link rel="stylesheet" type="text/css" href="' . App::e($url) . '" />' . "\n";

		return $this;
	}
}
