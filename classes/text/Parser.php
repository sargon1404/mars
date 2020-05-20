<?php
/**
* The Text Parser Class
* @package Mars
*/

namespace Mars\Text;

/**
* The Text Parser Class
* Parses text
*/
class Parser
{
	/**
	* @internal
	*/
	protected bool $parse_links_nofollow = false;

	/**
	* Converts all text links (http://domain.com) into the html equivalent (<a href="http://domain.com">http://domain.com</a>)
	* @param string $text The $text to parse
	* @param bool $parse_nofollow If true,will set rel="nofollow" for all parsed links
	* @return string The parsed text
	*/
	public function parseLinks(string $text, bool $parse_nofollow = false) : string
	{
		$this->parse_links_nofollow = $parse_nofollow;

		$pattern = '/\b(?<!=")(https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|](?!.*".*>)(?!.*<\/a>)/i';

		return preg_replace_callback($pattern, [$this, 'parse_links_callback'], $text);
	}

	/**
	* Callback for parse_links
	* @internal
	*/
	protected function parseLinksCallback(array $match) : string
	{
		$rel = '';
		$url = $this->parseLinksCallbackGetUrl($match[0]);

		if ($this->parse_links_nofollow) {
			$rel = ' rel="nofollow"';
		}

		return '<a href="' . $url . '"' . $rel . '>' . $url . '</a>';
	}

	/**
	* @internal
	*/
	protected function parseLinksCallbackGetUrl(string $url) : string
	{
		return trim($url);
	}

	/**
	* Adds rel="nofollow" to all links inside $text
	* @param string $text The $text to parse
	* @return string The parsed text
	*/
	public function parseNofollow(string $text) : string
	{
		return preg_replace_callback('/<a(.*)href="(.*)"(.*)>/isU', [$this, 'parse_nofollow_callback'], $text);
	}

	/**
	* @internal
	*/
	protected function parseNofollowCallback($match) : string
	{
		if (str_contains(strtolower($match[1]), 'rel="nofollow"') || str_contains(strtolower($match[3]), 'rel="nofollow"')) {
			return $match[0];
		}

		return "<a{$match[1]}href=\"{$match[2]}\"{$match[3]} rel=\"nofollow\">";
	}
}
