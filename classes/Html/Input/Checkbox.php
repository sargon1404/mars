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
	* @see \Mars\Html\TagInterface::html()
	* {@inheritdoc}
	*/
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$label = $properties['label'] ?? '';

		$attributes = $this->generateIdAttribute($attributes);

		$html = parent::html($text, $attributes, $properties);
		if ($label) {
			$html.= (new Label($this->app))->html($label, ['for' => $attributes['id']]);
		}

		return $html;
	}
}
