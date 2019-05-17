<?php
/**
* The Uri Class
* @package Mars
*/

namespace Mars;

/**
* The Uri Class
* Functionality for building & handling urls
*/
class Uri
{
	use AppTrait;

	/**
	* @var string url_amp The delimitator used when building urls
	*/
	public $url_amp = '&';

	/**
	* Builds an url appending $params to $url
	* @param string $base_url The url to which params will be appended.
	* @param array $params Array containing the values to be appended. Specified as $name=>$value
	* @param bool $remove_empty_params If true, will remove from the query params the params with value = ''
	* @return string Returns the built url
	*/
	public function build(string $base_url = '', array $params = [], bool $remove_empty_params = true) : string
	{
		$separator = '?';
		if (strpos($base_url, '?') !== false) {
			$separator = $this->url_amp;
		}

		$params_str = '';
		$params_array = [];

		foreach ($params as $name => $value) {
			if (is_array($value)) {
				if ($remove_empty_params) {
					if (!$value) {
						continue;
					}
				}

				foreach ($value as $val) {
					$params_array[] = urlencode($name) . '[]' . '=' . urlencode($val);
				}
			} else {
				if ($remove_empty_params) {
					if ($value === '') {
						continue;
					}
				}

				$params_array[] = urlencode($name) . '=' . urlencode($value);
			}
		}

		$params_str = implode($this->url_amp, $params_array);

		if ($params_array) {
			$url = $base_url . $separator . $params_str;
		} else {
			$url = $base_url;
		}

		return $url;
	}

	/**
	* Builds an url by appendding the $parts to $base_url.
	* @param string $base_url The base url
	* @param array $parts Array with the parts to append to base_url
	* @return string Returns the $url
	*/
	public function buildPath(string $base_url, array $parts) : string
	{
		$parts = [];
		foreach ($url_parts as $part) {
			$parts[] = rawurlencode($part);
		}

		return App::sl($base_url) . implode('/', $parts);
	}

	/**
	* Determines if $param exists as a query param in $url
	* @param string $url The url to search for param\
	* @param string $param The param's name
	* @return bool True if exists, false otherwise
	*/
	public function inQuery(string $url, string $param) : bool
	{
		$pos = strpos($url, '?');
		if ($pos === false) {
			return false;
		}

		$query_str = substr($url, $pos + 1);

		parse_str($query_str, $arr);

		if (isset($arr[$param])) {
			return true;
		}

		return false;
	}

	/**
	* Converts an url from https:// to http://
	* @param string $url The url
	* @return string The https url
	*/
	public function toHttp(string $url) : string
	{
		if (strpos($url, 'http://') === 0) {
			return $url;
		}

		return 'http://' . substr($url, 8);
	}

	/**
	* Converts an url from http:// to https://
	* @param string $url The url
	* @return string The https url
	*/
	public function toHttps(string $url) : string
	{
		if (strpos($url, 'http://') !== 0) {
			return $url;
		}

		return 'https://' . substr($url, 7);
	}

	/**
	* Adds http:// at the beginning of $url, if it isn't already there
	* @param string $url The url
	* @return string Returns the $url prefixed by http://
	*/
	public function addHttp(string $url) : string
	{
		if (!$url) {
			return '';
		}

		$url = trim($url);

		if (strpos($url, 'http://') === 0) {
			return $url;
		} elseif (strpos($url, 'https://') === 0) {
			return 'http://' . substr($url, 8);
		} else {
			return 'http://' . $url;
		}
	}

	/**
	* Adds https:// at the beginning of $url, if it isn't already there
	* @param string $url The url
	* @return string Returns the $url prefixed by https://
	*/
	public function addHttps(string $url) : string
	{
		if (!$url) {
			return '';
		}

		$url = trim($url);

		if (strpos($url, 'https://') === 0) {
			return $url;
		} elseif (strpos($url, 'http://') === 0) {
			return 'https://' . substr($url, 7);
		} else {
			return 'https://' . $url;
		}
	}

	/**
	* Adds www. to the url if it doesn't already have it
	* @param string $url The url
	* @return string The url with www. added
	*/
	public function addWww(string $url) : string
	{
		$parts = parse_url($url);
		$scheme = $parts['scheme'];

		if (strpos($url, $scheme . '://www.') === 0) {
			return $url;
		} else {
			return str_replace($scheme . '://', $scheme . '://www.', $url);
		}
	}

	/**
	* Strips the www. part from an url
	* @param string $url The url
	* @return string The url without www.
	*/
	public function stripWww(string $url) : string
	{
		$parts = parse_url($url);
		$scheme = $parts['scheme'];
		$host = $parts['host'];

		if (strpos($host, 'www.') === 0) {
			return $scheme . '://' . substr($host, 4);
		} else {
			return $url;
		}
	}

	/**
	* Adds the scheme to an url
	* @param string $url The url
	* @param string $scheme The scheme to add. http or https. If empty, it will be determined based on the current document url
	* @return string The url
	*/
	public function addScheme(string $url, string $scheme = '') : string
	{
		if (!$scheme) {
			$scheme = 'http';
			if ($this->app->is_https) {
				$scheme = 'https';
			}
		}

		if (strpos($url, 'https:') === 0 || strpos($url, 'http:') === 0) {
			return $url;
		}

		//does the url have :// ?
		if (strpos($url, '//') !== 0) {
			$url = '//' . $url;
		}

		return $scheme . ':' . $url;
	}

	/**
	* Strips the scheme [http or https] from an url. https://google.com -> ://google.com
	* @param string $url The url
	* @return string The url without the scheme
	*/
	public function stripScheme(string $url) : string
	{
		if (strpos($url, 'https:') === 0) {
			return substr($url, 6);
		} elseif (strpos($url, 'http:') === 0) {
			return substr($url, 5);
		}

		return $url;
	}

	/**
	* Returns javascript:void(0)
	* @return string
	*/
	public function getEmpty() : string
	{
		return 'javascript:void(0)';
	}

	/**
	* Adds the ajax param to an url
	* @param string $base_url The url to the params will be appended
	* @param string $response_param The response param. Defaults to 'response'
	* @return string Returns the $url
	*/
	public function addAjax(string $base_url, string $response_param = '') : string
	{
		if (!$response_param) {
			$response_param = 'response';
		}

		return $this->build($base_url, [$response_param  => 'ajax']);
	}
}
