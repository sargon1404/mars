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
	* @var array $options The options
	*/
	public array $options = [];

	/**
	* @var string|array $selected The selected option(s)
	*/
	public string|array $selected = '';

	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'option';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		if (!$this->options) {
			return '';
		}

		$html = '';

		foreach ($this->options as $text => $value) {
			$text = $this->app->escape->html($text);

			$attributes = $value;
			if (!is_array($value)) {
				$attributes = ['value' => $value];
			} else {
				$value = $value['value'];
			}

			$selected = '';
			if ($value == $this->selected) {
				$attributes['selected'] = true;
			}

			$attributes = $this->getAttributes($attributes);

			$html.= "<option{$attributes}>{$text}</option>\n";
		}

		return $html;
	}
}
