<?php
/**
* The Options Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Options Class
* Renders the select options
*/
class Options extends \Mars\Html\Tag
{
	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'option';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$options = $properties['options'] ?? [];
		$selected = (array) ($properties['selected'] ?? []);

		if (!$options) {
			return '';
		}

		$html = '';
		foreach ($options as $value => $text) {
			if (is_array($text)) {
				$optgroup = new Optgroup($this->app);

				$html.= $optgroup->open(['label' => $value]);
				$html.= $this->getOptions($text, $selected);
				$html.= $optgroup->close();
			} else {
				$html.= parent::get($text, ['value' => $value, 'selected' => in_array($value, $selected)]);
			}
		}

		return $html;
	}

	/**
	* Returns the html code of the options
	* @param array $options The options
	* @param array $selected The selected options
	* @return string The html code
	*/
	protected function getOptions(array $options, array $selected) : string
	{
		$html = '';
		foreach ($options as $value => $text) {
			$html.= parent::get($text, ['value' => $value, 'selected' => in_array($value, $selected)]);
		}

		return $html;
	}
}
