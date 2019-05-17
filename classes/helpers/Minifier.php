<?php
/**
* The Minifier Class
* @package Mars
*/

namespace Mars\Helpers;

/**
* The Asset Minifier Class
* Minifies assets content
*/
class Minifier
{
	/**
	* Minifies html code
	* @param string $code The html code to minify
	* @return string The minified html code
	*/
	public function minifyHtml(string $code) : string
	{
		$minifier = new \voku\helper\HtmlMin;

		return $minifier->minify($code);
	}
}
