<?php
/**
* The Text Class
* @package Mars
*/

namespace Mars;

/**
* The Text Class
* Text processing functionality
*/
class Text
{
	use AppTrait;

	/**
	* Returns the first $max_length characters from text. If strlen($text) > $max_length will append $replace_with
	* @param string $text The text to cut
	* @param int $max_length The max number of characters
	* @param string $replace_with Will replace the removed/cut text with this value
	* @param bool $strip_tags If true will strip the tags of $text
	* @return string The cut text
	*/
	public function cut(string $text, int $max_length = 40, string $replace_with = '...', bool $strip_tags = true) : string
	{
		if ($strip_tags) {
			$text = strip_tags($text);
		}

		if (strlen($text) > $max_length) {
			return substr($text, 0, $max_length) . $replace_with;
		} else {
			return $text;
		}
	}

	/**
	* Cuts characters from the middle of $text
	* @param string $text The text to cut
	* @param int $max_length The max number of characters
	* @param string $replace_with Will replace the removed/cut text with this value
	* @return string The cut text
	*/
	public function cutMiddle(string $text, int $max_length = 40, string $replace_with = '...') : string
	{
		$count = strlen($text);
		if ($count <= $max_length) {
			return $text;
		}

		$prefix = ceil($max_length * 2) / 3;
		$suffix = $max_length - $prefix;
		$skip = $count - ($prefix + $suffix);

		return substr($text, 0, $prefix) . $replace_with . substr($text, $prefix + $skip);
	}

	/**
	* Returns the parsed and filtered text html code from $text
	* @param string $text The $text to parse & filter
	* @param bool $parse_links If true, will parse links
	* @param bool $parse_nofollow If true, will apply the rel="nofollow" attribute to links
	* @return string The parsed and filtered text
	*/
	public function parse(string $text, bool $parse_links = true, bool $parse_nofollow = false) : string
	{
		$text = $this->filter($text);

		$parser = $this->getParserObj();
		if ($parse_nofollow) {
			$text = $parser->parseNofollow($text);
		}

		if ($parse_links) {
			$text = $parser->parseLinks($text, $parse_nofollow);
		}

		$text = $this->app->plugins->filter('text_parse', $text, $this);

		return $text;
	}

	/**
	* @internal
	*/
	protected function getParserObj()
	{
		return new Text\Parser;
	}


}
