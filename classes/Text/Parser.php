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
	use \Mars\AppTrait;

	/**
	* Parses the text for links and rel="nofollow"
	* @param string $text The $text to parse
	* @param bool $parse_links If true, will parse links
	* @param bool $parse_nofollow If true, will apply the rel="nofollow" attribute to links
	* @return string The parsed text
	*/
	public function parse(string $text, bool $parse_links = true, bool $parse_nofollow = false) : string
	{
		if ($parse_links) {
			$text = $this->parseLinks($text);
		}

		if ($parse_nofollow) {
			$text = $this->parseNofollow($text);
		}

		return $text;
	}

	/**
	* Converts all text links (https://domain.com) into the html equivalent (<a href="https://domain.com">https://domain.com</a>)
	* @param string $text The $text to parse
	* @return string The parsed text
	*/
	public function parseLinks(string $text)
	{
		$pattern = '/\b(?<!=")(https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|](?!.*".*>)(?!.*<\/a>)/i';

		return preg_replace_callback($pattern, function(array $match) {
			$url = trim($match[0]);

			return $this->app->html->a($url);
		}, $text);
	}

	/**
	* Adds rel="nofollow" to all links inside $text
	* @param string $text The $text to parse
	* @return string The parsed text
	*/
	public function parseNofollow(string $text) : string
	{
		return preg_replace_callback('/<a(.*)href="(.*)"(.*)>/isU', function(array $match) {
			if (str_contains(strtolower($match[1]), 'rel="nofollow"') || str_contains(strtolower($match[3]), 'rel="nofollow"')) {
				return $match[0];
			}

			return "<a{$match[1]}href=\"{$match[2]}\"{$match[3]} rel=\"nofollow\">";

		}, $text);
	}
}
