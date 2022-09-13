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
class Select extends \Mars\Html\Tag
{
	/**
	* @var string $type The tag's type
	*/
	public string $tag = 'select';

	/**
	* {@inheritdoc}
	*/
	public function open(array $attributes = []) : string
	{
		$attributes['size'] = $attributes['size'] ?? 1;
		$attributes = $this->generateIdAttribute($attributes);

		return parent::open($attributes);
	}

	/**
	* @see \Mars\Html\TagInterface::html()
	* {@inheritdoc}
	*/
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$html = $this->open($attributes);
		$html.= (new Options($this->app))->html('', [], $properties);
		$html.= $this->close();

		return $html;
	}
}
