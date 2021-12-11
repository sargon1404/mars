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
	public function get() : string
	{
		$name = $this->attributes['name'];
		$value = $this->attributes['value'];

		$parts = explode(' ', $value);

		$date = new Date(['name' => $name . '-date', 'value' => $parts[0]] + $this->attributes);
		$time = new Time(['name' => $name . '-time', 'value' => $parts[1]] + $this->attributes);

		$html = $date->get();
		$html.= '&nbsp;';
		$html.= $time->get();

		return $html;
	}
}
