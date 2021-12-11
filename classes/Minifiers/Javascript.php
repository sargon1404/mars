<?php
/**
* The Javascript Minifier
* @package Mars
*/

namespace Mars\Minifiers;

/**
* The Javascript Minifier
*/
class Javascript implements DriverInterface
{
	/**
	* @see \Mars\Minifiers\DriverInterface::minify()
	* {@inheritdoc}
	*/
	public function minify(string $content) : string
	{
		$minifier = new \MatthiasMullie\Minify\JS;
		$minifier->add($content);

		return $minifier->minify();
	}
}
