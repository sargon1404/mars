<?php
/**
* The Meta Tag Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
* The Document's Meta Tag Class
* Stores the meta tags used by a document
*/
class Meta extends Tags
{
	/**
	* Outputs a meta tag
	* @param string $name The name of the meta tag
	* @param string $content The content of the meta tag
	* @return static
	*/
	public function outputTag(string $name, string $content) : static
	{
		echo '<meta name="' . $this->app->escape->html($name) . '" content="' . $this->app->escape->html($content) . '">' . "\n";

		return $this;
	}
}
