<?php
/**
* The List Class
* @package Mars
*/

namespace Mars\Html\Lists;

/**
 * The List Class
 * Renders a list
 */
abstract class Listing extends \Mars\Html\Tag
{
	/**
	 * @see \Mars\Html\TagInterface::html()
	 * {@inheritdoc}
	 */
	public function html(string $text = '', array $attributes = [], array $items = []) : string
	{
		$html = $this->open($attributes);
		$html.= $this->getItems($items);
		$html.= $this->close();

		return $html;
	}

	/**
	 * Returns the item's html code
	 * @param array $items The items
	 * @return string The html code
	 */
	public function getItems(array $items) : string
	{
		$html = '';

		foreach ($items as $item) {
			$html.= "<li>" . $item . "</li>\n";
		}

		return $html;
	}
}
