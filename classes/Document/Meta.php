<?php
/**
* The Meta Tag Class
* @package Mars
*/

namespace Mars\Document;

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
	* @return $this
	*/
	public function outputTag(string $name, string $content)
	{
		echo '<meta name="' . $this->app->escape->html($name) . '" content="' . $this->app->escape->html($content) . '" />' . "\n";

		return $this;
	}

	/**
	* Will add the specified meta data in <head></head>
	* @param string $name The name of the meta tag
	* @param string $content The content of the meta tag
	* @return $this
	*/
	public function add(string $name, string $content)
	{
		parent::add($name, $content);

		return $this;
	}
}
