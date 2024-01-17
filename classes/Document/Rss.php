<?php
/**
* The Rss tag Class
* @package Mars
*/

namespace Mars\Document;

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
	 * @return static
	 */
	public function outputTag(string $url, string $title) : static
	{
		echo '<link rel="alternate" type="application/rss+xml" title="' . $this->app->escape->html($title) . '" href="' . $this->app->escape->html($url) . '">' . "\n";

		return $this;
	}

	/**
	 * Loads a rss url
	 * @param string $url The url of the rss file.
	 * @param string $title The title of the feed
	 * @return static
	 */
	public function load(string $url, string $title) : static
	{
		return $this->add($url, $title);
	}

	/**
	 * Unloads a rss url
	 * @param string $url The url of the rss file.
	 * @return static
	 */
	public function unload(string $url) : static
	{
		return $this->remove($url);
	}
}
