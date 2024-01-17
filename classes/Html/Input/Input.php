<?php
/**
* The Input Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
 * The Input Class
 * Renders an input field
 */
class Input extends \Mars\Html\Tag
{
	/**
	 * @var string $type The input's type
	 */
	protected string $type = 'text';

	/**
	 * {@inheritdoc}
	 */
	protected string $tag = 'input';

	/**
	 * @see \Mars\Html\TagInterface::html()
	 * {@inheritdoc}
	 */
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$attributes = $this->generateIdAttribute($attributes);

		return parent::html($text, ['type' => $this->type] + $attributes, $properties);
	}
}
