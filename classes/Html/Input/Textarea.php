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
	 * @see \Mars\Html\TagInterface::html()
	 * {@inheritdoc}
	 */
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$attributes = $this->generateIdAttribute($attributes);

		return parent::html($text, $attributes, $properties);
	}
}
