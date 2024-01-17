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
	 * @param string $value The value to escape
	 * @return string The escaped value
	 */
	public function html(?string $value) : string
	{
		return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
	}

	/**
	 * Double escapes a value
	 * @param string $value The value to escape
	 * @param bool $nl2br If true, will apply nl2br to value
	 * @return string The double escaped value
	 */
	public function htmlx2(?string $value, bool $nl2br = true) : string
	{
		$value = $this->html($this->html($value));

		if ($nl2br) {
			return nl2br($value);
		}

		return $value;
	}

	/**
	 * Escapes text meant to be written as javascript code, when embeding it with html. Eg: <a href="" onclick="<code>">
	 * @param string $value The value to escape
	 * @return string The escaped value
	 */
	public function js(string $value) : string
	{
		return $this->html($value);
	}

	/**
	 * Escapes text which will be used inside javascript <script> tags
	 * @param string $value The value to escape
	 * @return string The escaped value
	 */
	public function jsString(string $value) : string
	{
		return str_replace(['\\', "'", '"', "\n", "\r"], ['\\\\', "\\'", '\\"', '', ''], $value);
	}

	/**
	 * Escapes a folder path by hiding the server path
	 * @param string $path The folder
	 * @return string
	 */
	public function path(string $path) : string
	{
		return str_replace($this->app->path, '', $path);
	}
}
