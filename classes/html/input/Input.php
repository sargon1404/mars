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
	* {@inheritDoc}
	*/
	protected string $tag = 'input';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritDocs}
	*/
	public function get() : string
	{
		if (isset($this->attributes['name'])) {
			$this->attributes['id'] = $this->attributes['id'] ?? $this->escapeId($this->attributes['name']);
		}

		$this->attributes = ['type' => $this->type] + $this->attributes;

		return parent::get();
	}
}
