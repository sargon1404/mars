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
	* @var bool $generate_id If true, will generate ids for this type of inputs
	*/
	public bool $generate_id = true;

	/**
	* @var string $type The input's type
	*/
	protected string $type = 'text';

	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'input';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		if ($this->generate_id && isset($this->attributes['name'])) {
			$this->attributes['id'] = $this->attributes['id'] ?? $this->escapeId($this->attributes['name']);
		}

		$this->attributes = ['type' => $this->type] + $this->attributes;

		return parent::get();
	}
}
