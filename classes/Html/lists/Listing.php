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
	* @var array $items the List's items
	*/
	public array $items = [];

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		$attributes = $this->getAttributes($this->attributes);

		$html = "<{$this->tag}{$attributes}>" . "\n";
		$html.= $this->getItems($this->items);
		$html.= "</{$this->tag}>" . "\n";

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
			$html.= "<li>" . $this->escape($item) . "</li>\n";
		}

		return $html;
	}
}
