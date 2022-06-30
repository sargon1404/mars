<?php
/**
* The Radio Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Radio Class
* Renders a radio
*/
class Radio extends \Mars\Html\Tag
{
	/**
	* @var string $label The radio's label, if any
	*/
	public string $label = '';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$attributes_list = $this->generateIdAttribute($attributes);
		$attributes = $this->getAttributes($attributes_list);

		$label = $properties['label'] ?? '';

		$html = "<input type=\"radio\"{$attributes}>";
		if ($label) {
			$html.= "<label for=\"{$attributes_list['id']}\">" . $this->app->escape->html($label) . '</label>';
		}
		$html.= "\n";

		return $html;
	}
}
