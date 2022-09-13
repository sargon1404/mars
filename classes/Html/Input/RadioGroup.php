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
	* @see \Mars\Html\TagInterface::html()
	* {@inheritdoc}
	*/
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$values = $properties['values'] ?? [];
		$checked = $properties['checked'] ?? '';

		if (!$values) {
			return '';
		}

		$html = '';
		$radio = new Radio($this->app);

		foreach ($values as $value => $label) {
			$html.= $radio->html('', ['value' => $value, 'checked' => $value == $checked] + $attributes, ['label' => $label]);
		}

		return $html;
	}
}
