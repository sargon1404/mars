<?php
/**
* The Datetime Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Datetime Class
* Renders a field from where a date & time can be picked
*/
class Datetime extends \Mars\Html\Tag
{
	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$name = $attributes['name'];
		$value = $attributes['value'];

		$parts = explode(' ', $value);

		$date = new Date($this->app);
		$time = new Time($this->app);

		$html = $date->get('', ['name' => $name . '-date', 'value' => $parts[0]]);
		$html.= '&nbsp;';
		$html.= $time->get('', ['name' => $name . '-time', 'value' => $parts[1]]);

		return $html;
	}
}
