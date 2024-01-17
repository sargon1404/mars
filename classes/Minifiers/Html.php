<?php
/**
* The Html Minifier
* @package Mars
*/

namespace Mars\Minifiers;

/**
 * The Html Minifier
 */
class Html implements DriverInterface
{
	/**
	 * @see \Mars\Minifiers\DriverInterface::minify()
	 * {@inheritdoc}
	 */
	public function minify(string $content) : string
	{
		$minifier = new \voku\helper\HtmlMin;

		return $minifier->minify($content);
	}
}
