<?php
/**
* The Textarea Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Textarea Class
* Renders a textarea field
*/
class Textarea extends \Mars\Html\Tag
{
	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'textarea';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$attributes = $this->generateIdAttribute($attributes);

		return parent::get($text, $attributes, $properties);
	}
}
