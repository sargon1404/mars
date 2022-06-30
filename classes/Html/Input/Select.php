<?php
/**
* The Select Class
* @package Mars
*/

namespace Mars\Html\Input;

/**
* The Select Class
* Renders a select field
*/
class Select extends SelectOptions
{
	/**
	* @var string $type The tag's type
	*/
	public string $tag = 'select';

	/**
	* Opens the tag
	*/
	public function open() : string
	{
		$this->attributes['size'] = $this->attributes['size'] ?? 1;
		$this->attributes['id'] = $this->attributes['id'] ?? $this->escapeId($this->attributes['name']);

		return parent::open();
	}

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		$html = $this->open();
		$html.= parent::get();
		$html.= $this->close();

		return $html;
	}
}
