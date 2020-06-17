<?php
/**
* The Radio Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\App;

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
	* {@inheritDocs}
	*/
	public function get() : string
	{
		$this->attributes['id'] = $this->attributes['id'] ?? $this->getIdName($this->attributes['name']);

		$attributes = $this->getAttributes($this->attributes);

		$html = "<input type=\"radio\"{$attributes}>";
		if ($this->label) {
			$html.= "<label for=\"{$this->attributes['id']}\">" . App::e($this->label) . '</label>';
		}
		$html.= "\n";

		return $html;
	}
}
