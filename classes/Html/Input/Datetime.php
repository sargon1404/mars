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
	 * @see \Mars\Html\TagInterface::html()
	 * {@inheritdoc}
	 */
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$name = $attributes['name'];
		$value = $attributes['value'];

		$parts = explode(' ', $value);

		$date = new Date($this->app);
		$time = new Time($this->app);

		$html = $date->html('', ['name' => $name . '-date', 'value' => $parts[0]]);
		$html.= '&nbsp;';
		$html.= $time->html('', ['name' => $name . '-time', 'value' => $parts[1]]);

		return $html;
	}
}
