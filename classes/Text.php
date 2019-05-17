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
	* @var string allowed_attributes List of allowed attributes when filtering
	*/
	protected $allowed_attributes = '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title';

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

		$text = $this->app->plugins->filter('textParse', $text, $this);

		return $text;
	}

	/**
	* @internal
	*/
	protected function getParserObj()
	{
		return new Text\Parser;
	}

	/**
	* Filters text using htmlpurifier
	* @param string $text The $text to filter
	* @param string $allowed_attributes The allowed attributes
	* @param array $allowed_elements Array containing the allowed html elements. If null, no elements are allowed. If it's an empty array all elements are allowed
	* @param string $encoding The encoding of the text
	* @return string The filtered text
	*/
	public function filter(string $text, string $allowed_attributes = '', ?array $allowed_elements = [], string $encoding = 'UTF-8') : string
	{
		if (!$text) {
			return '';
		}

		if (!$allowed_attributes) {
			$allowed_attributes = $this->allowed_attributes;
		}

		static $html_purifier_init = false;
		if (!$html_purifier_init) {
			require_once($this->libraries_dir . 'php/vendor/masterjoa/htmlpurifier-standalone/HTMLPurifier.standalone.php');

			$html_purifier_init = true;
		}

		$config = \HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', $encoding);
		$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		$config->set('HTML.AllowedAttributes', $allowed_attributes);
		$config->set('Attr.AllowedRel', 'nofollow,follow');
		$config->set('Attr.AllowedFrameTargets', '_blank');
		$config->set('Attr.EnableID', true);

		$this->app->plugins->run('textFilterConfig', $config, $allowed_attributes, $this);

		if ($allowed_elements === null) {
			$config->set('HTML.AllowedElements', []);
			$config->set('HTML.AllowedAttributes', '');
		} elseif ($allowed_elements) {
			$config->set('HTML.AllowedElements', $allowed_elements);
		}

		$purifier = new \HTMLPurifier($config);
		$clean_text = $purifier->purify($text);

		$clean_text = $this->app->plugins->filter('textFilter', $clean_text, $text, $this);

		return $clean_text;
	}
}
