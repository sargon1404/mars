<?php
/**
* The Css Minifier
* @package Mars
*/

namespace Mars\Minifiers;

/**
* The Css Minifier
*/
class Css implements DriverInterface
{
	/**
	* @see \Mars\Minifiers\DriverInterface::minify()
	* {@inheritdoc}
	*/
	public function minify(string $content) : string
	{
		$minifier = new \MatthiasMullie\Minify\CSS;
		$minifier->add($content);

		return $minifier->minify();
	}
}