<?php
/**
* The Escape Class
* @package Mars
*/

namespace Mars;

/**
* The Escapes Class
* Escape values
*/
class Escape
{
	use AppTrait;

	/**
	* Converts special chars. to html entitites
	* @see \Mars\App::e()
	*/
	public function html(?string $value) : string
	{
		return App::e($value);
	}

	/**
	* Decodes the html special entities
	* @see \Mars\App::de()
	*/
	public function htmlDecode(?string $value) : string
	{
		return App::de($value);
	}

	/**
	* Escapes text meant to be written as javascript code. Replaces ' with \' and \n with empty space
	* @see \Mars\App::ejs()
	*/
	public function js(string $value, bool $escape_html = true) : string
	{
		return App::ejs($value, $escape_html);
	}

	/**
	* Escapes text which will be used inside javascript <script> tags
	* @see \Mars\App::ejsc()
	*/
	public function jsCode(string $value, bool $nl2br = true) : string
	{
		return App::ejsc($value, $nl2br);
	}

	/**
	* Escapes an url. It breaks into parts and calls rawurlencode/urlencode on the different segments
	* @param string $url The url to escape
	* @return string The clean url
	*/
	public function url(string $url) : string
	{
		$url_parts = parse_url($url);
		if (!$url_parts) {
			return '';
		}

		if ($url_parts['scheme'] == 'javascript') {
			return '';
		}

		$parts = [];
		$parts[] = $url_parts['scheme'] . ':/';
		if (isset($url_parts['host'])) {
			$parts[] = $url_parts['host'];
		}

		if (isset($url_parts['path'])) {
			$path_parts = explode('/', trim($url_parts['path'], '/'));

			foreach ($path_parts as $part) {
				$parts[] = rawurlencode($part);
			}
		}

		$clean_url = implode('/', $parts);

		if (!empty($url_parts['query'])) {
			$clean_url.= '?';

			$parts = [];
			$params_parts = explode('&', $url_parts['query']);

			foreach ($params_parts as $part) {
				$pair = explode('=', $part);

				$p1 = urlencode($pair[0]);
				$p2 = '';
				if ($pair[1]) {
					$p2 = '=' . urlencode($pair[1]);
				}

				$parts[] = $p1 . $p2;
			}

			$clean_url.= implode('&', $parts);
		}

		return $clean_url;
	}

	/**
	* Breaks a filename into parts and calls rawurlencode on each part
	* @param string The filename to escape
	* @return string The escaped filename
	*/
	public function urlFilename(string $filename) : string
	{
		$parts = [];
		$filename_parts = explode('/', $filename);

		foreach ($filename_parts as $part) {
			$parts[] = rawurlencode($part);
		}

		return implode('/', $parts);
	}

	/**
	* Escapes a folder name by hiding the server dir
	* @param string $dir The folder
	* @return string
	*/
	public function dir(string $dir) : string
	{
		return str_replace($this->app->site_dir, '', $dir);
	}
}
