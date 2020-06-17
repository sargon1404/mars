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
	* {@inheritDoc}
	*/
	protected string $tag = 'textarea';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritDocs}
	*/
	public function get() : string
	{
		$this->attributes['id'] = $this->attributes['id'] ?? $this->escapeId($this->attributes['name']);

		return parent::get();
	}
}
