<?php
/**
* The Checkbox Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Checkbox Class
* Renders a checkbox
*/
class Checkbox extends \Mars\Html\Tag
{
	/**
	* @var string $label The checkbox's label, if any
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

		$html = "<input type=\"checkbox\"{$attributes}>";
		if ($label) {
			$html.= "<label for=\"{$attributes_list['id']}\">" . $this->app->escape->html($label) . '</label>';
		}
		$html.= "\n";

		return $html;
	}
}
