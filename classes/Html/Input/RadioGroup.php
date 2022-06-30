<?php
/**
* The Radio Group Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Checkbox Class
* Renders a checkbox
*/
class RadioGroup extends \Mars\Html\Tag
{
	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$values = $properties['values'] ?? [];
		$checked = $properties['checked'] ?? '';

		if (!$values) {
			return '';
		}

		$html = '';
		$radio = new Radio($this->app);

		foreach ($values as $value => $label) {
			$html.= $radio->get('', ['value' => $value, 'checked' => $value == $checked] + $attributes, ['label' => $label]);
		}

		return $html;
	}
}
