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
	public function get() : string
	{
		$this->attributes['id'] = $this->attributes['id'] ?? $this->getIdName($this->attributes['name']);

		$attributes = $this->getAttributes($this->attributes);

		$html = "<input type=\"checkbox\"{$attributes}>";
		if ($this->label) {
			$html.= "<label for=\"{$this->attributes['id']}\">" . $this->app->escape->html($this->label) . '</label>';
		}
		$html.= "\n";

		return $html;
	}
}
