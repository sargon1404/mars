<?php
/**
* The Title Class
* @package Mars
*/

namespace Mars\Document;

/**
 * The Title Class
 * Stores the title of the document
 */
class Title extends Property
{
	/**
	 * Returns the value of the property
	 * @return string
	 */
	public function get() : string
	{
		$parts = [
			$this->app->config->title_prefix,
			$this->value,
			$this->app->config->title_suffix
		];

		$parts = array_filter($parts);

		return implode($this->app->config->title_separator, $parts);
	}
}
