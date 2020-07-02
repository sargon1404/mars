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
	public function outputUrl(string $url, $version = true, bool $async = false, bool $defer = false)
	{
		$url = $this->getUrl($url, $version);

		$async_str = '';
		$defer_str = '';
		if ($async) {
			$async_str = ' async';
		}
		if ($defer) {
			$defer_str = ' defer';
		}

		echo '<script type="text/javascript" src="' . App::e($url) . '"' . $async_str . $defer_str . '></script>' . "\n";

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

	/**
	* Returns a javascript array from $data
	* @param array|object $data The data to convert to a javascript array
	* @param bool $quote If true will put quotes around the array's elements
	* @param string $key If specified will use array[$key] rather than the array itself
	* @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
	* @return string The javascript array
	*/
	public function toArray($data, bool $quote = true, string $key = '', array $dont_quote_array = [])
	{
		$data = App::toArray($data);

		if (!$data) {
			return '[]';
		}

		$elements = [];
		foreach ($data as $val) {
			if ($key && is_array($val)) {
				$val = $data[$key];
			}

			$elements[] = $val;
		}

		return '[' . $this->getList($elements, $quote, $dont_quote_array) . ']';
	}

	/**
	* Returns a javascript object from $data
	* @param array|object $data The data to convert to a javascript object
	* @param bool $quote If true will put quotes around the array's elements
	* @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
	* @return string The javascript object
	*/
	public function toObject($data, bool $quote = true, array $dont_quote_array = [])
	{
		$data = App::toArray($data);

		if (!$data) {
			return '{}';
		}

		$elements = [];
		foreach ($data as $key => $val) {
			if (is_array($val)) {
				$val = $this->toArray($val, $quote);
			} else {
				$val = App::ejsc($val);

				if ($quote) {
					if (!$dont_quote_array || !in_array($key, $dont_quote_array)) {
						$val = "'{$val}'";
					}
				}
			}

			$elements[] = $key . ': ' . $val;
		}

		return '{' . implode(', ', $elements) . '}';
	}

	/**
	* Returns a javascript function params list from $array
	* @param array|object $data The data to convert to a javascript params list
	* @param bool $quote If true will put quotes around the array's elements
	* @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
	* @return string The javascript object
	*/
	public function getParams($data, bool $quote = true, array $dont_quote_array = [])
	{
		$data = App::toArray($data);

		if (!$data) {
			return '()';
		}

		return '(' . $this->getList($data, $quote, $dont_quote_array) . ')';
	}

	/**
	* Returns a javascript list from $array
	* @param array|object $data The data to convert to a javascript list
	* @param bool $quote If true will put quotes around the array's elements
	* @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
	* @return string The javascript object
	*/
	public function getList($data, bool $quote = true, array $dont_quote_array = []) : string
	{
		$data = App::toArray($data);

		if (!$data) {
			return '';
		}

		$elements = [];
		foreach ($data as $key => $val) {
			$val = App::ejsc($val);

			if ($quote) {
				if (!$dont_quote_array || !in_array($key, $dont_quote_array)) {
					$val = "'{$val}'";
				}
			}

			$elements[] = $val;
		}

		return implode(', ', $elements);
	}
}
