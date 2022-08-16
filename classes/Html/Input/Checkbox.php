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
class Checkbox extends Input
{
	/**
	* {@inheritdoc}
	*/
	protected string $type = 'checkbox';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$label = $properties['label'] ?? '';

		$attributes = $this->generateIdAttribute($attributes);

		$html = parent::get($text, $attributes, $properties);
		if ($label) {
			$html.= (new Label($this->app))->get($label, ['for' => $attributes['id']]);
		}

		return $html;
	}
}
