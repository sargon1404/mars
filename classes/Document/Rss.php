<?php
/**
* The Rss tag Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
* The Document's Rss tag Class
* Stores the <link rel="alternate" type="application/rss+xml"> tags of the document
*/
class Rss extends Tags
{
	/**
	* Outputs a rss tag
	* @param string $url The url of the rss file.
	* @param string $title The title of the feed
	* @return $this
	*/
	public function outputTag(string $url, string $title)
	{
		echo '<link rel="alternate" type="application/rss+xml" title="' . App::e($title) . '" href="' . App::e($url) . '" />' . "\n";

		return $this;
	}

	/**
	* Loads $rss_url as a rss
	* @param string $url The url of the rss file.
	* @param string $title The title of the feed
	* @return $this
	*/
	public function add(string $url, string $title)
	{
		parent::add($url, $title);

		return $this;
	}
}
