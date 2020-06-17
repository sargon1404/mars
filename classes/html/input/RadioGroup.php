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
	* @var string $values The values, in the format $value => $label
	*/
	public array $values = [];

	/**
	* @var string $checked The checked radio
	*/
	public string $checked = '';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritDocs}
	*/
	public function get() : string
	{
		if (!$this->values) {
			return '';
		}

		$html = '';
		$radio = new Radio;

		foreach ($this->values as $value => $label) {
			$id = $this->getIdName($this->attributes['name']);
			$checked = false;
			if ($value == $this->checked) {
				$checked = true;
			}

			$radio->label = $label;
			$radio->attributes = ['id' => $id, 'value' => $value, 'checked' => $checked] + $this->attributes;
			$html.= $radio->get();
		}

		return $html;
	}
}
