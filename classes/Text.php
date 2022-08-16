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
	use HandlersTrait;

	/**
	* @var array $supported_rules The list of supported rules
	*/
	protected array $supported_handlers = [
		'parser' => '\Mars\Text\Parser'
	];

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

		if (mb_strlen($text) > $max_length) {
			return mb_substr($text, 0, $max_length) . $replace_with;
		} else {
			return $text;
		}
	}

	/**
	* Cuts characters from the middle of $text
	* @param string $text The text to cut
	* @param int $max_length The max number of characters
	* @param string $replace_with Will replace the removed/cut text with this value
	* @param bool $strip_tags If true will strip the tags of $text
	* @return string The cut text
	*/
	public function cutMiddle(string $text, int $max_length = 40, string $replace_with = '...', bool $strip_tags = true) : string
	{
		if ($strip_tags) {
			$text = strip_tags($text);
		}

		$count = mb_strlen($text);
		if ($count <= $max_length) {
			return $text;
		}

		$prefix = (int)(ceil($max_length * 2) / 3);
		$suffix = $max_length - $prefix;
		$skip = $count - ($prefix + $suffix);

		return mb_substr($text, 0, $prefix) . $replace_with . mb_substr($text, $prefix + $skip);
	}

	/**
	* Parses the text for links and rel="nofollow"
	* @param string $text The $text to parse
	* @param bool $parse_links If true, will parse links
	* @param bool $parse_nofollow If true, will apply the rel="nofollow" attribute to links
	* @return string The parsed text
	*/
	public function parse(string $text, bool $parse_links = true, bool $parse_nofollow = false) : string
	{
		$parser = $this->getHandler('parser');

		$text = $parser->parse($text, $parse_links, $parse_nofollow);

		return $this->app->plugins->filter('text_parse', $text, $parse_links, $parse_nofollow, $this);
	}
}
