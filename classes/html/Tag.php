<?php
/**
* The Tag Class
* @package Mars
*/

namespace Mars\Html;

use Mars\App;

/**
* The Tag Class
* Renders a generic tag
*/
abstract class Tag implements TagInterface
{
	/**
	* @var string $type The tag's attributes, if any
	*/
	public array $attributes = [];

	/**
	* @var string $text The tag's text
	*/
	public string $text = '';

	/**
	* @var bool $escape If true, will escape the content
	*/
	public bool $escape = true;

	/**
	* @var string $tag The tag's tag
	*/
	protected string $tag = '';

	/**
	* @var App $app The app object
	*/
	protected App $app;

	/**
	* Builds the tag
	* @param string $type The tag's type
	* @param array $attributes The attributes of the tag, if any
	* @param string $text The tag's text, if any
	* @param bool $escape  If true, will escape the content
	* @param App $app The app object
	*/
	public function __construct(array $attributes = [], array $properties = [], bool $escape = true, App $app = null)
	{
		if (!$app) {
			$app = App::get();
		}

		$this->attributes = $attributes;
		$this->escape = $escape;
		$this->app = $app;

		foreach ($properties as $name => $val) {
			$this->$name = $val;
		}
	}

	/**
	* Creates an object using a static call
	* @see Tag::__construct()
	* @return Tag
	*/
	public static function create(array $attributes = [], array $properties =[], bool $escape = true) : Tag
	{
		return new static($attributes, $properties, $escape);
	}

	/**
	* Opens the tag
	*/
	public function open() : string
	{
		$attributes = $this->getAttributes($this->attributes);

		return "<{$this->tag}{$attributes}>\n";
	}

	/**
	* Closes the tag
	*/
	public function close() : string
	{
		return "</{$this->tag}>\n";
	}

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritDocs}
	*/
	public function get() : string
	{
		$attributes = $this->getAttributes($this->attributes);

		if ($this->text) {
			return "<{$this->tag}{$attributes}>" . $this->escape($this->text) . "</{$this->tag}>\n";
		} else {
			return "<{$this->tag}{$attributes}>\n";
		}
	}

	/**
	* Html Escapes $value
	* @param string $value The value to escape
	* @return string The escaped value
	*/
	protected function escape(string $value) : string
	{
		if ($this->escape) {
			return App::e($value);
		}

		return $value;
	}

	/**
	* Merges the attributes and returns the html code
	* @param array $attributes The attributes in the format name => value
	* @return string The attribute's html code
	*/
	public function getAttributes(array $attributes) : string
	{
		$attributes_array = [];

		foreach ($attributes as $name => $value) {
			if (is_array($value)) {
				//don't escape if $value is an array
				$value = reset($value);
			} else {
				if (!is_bool($value)) {
					$value = App::e($value);
				}
			}

			if ($value) {
				if (is_bool($value)) {
					$attributes_array[] = $name;
				} else {
					$attributes_array[] = $name . '="' . $value . '"';
				}
			}
		}

		if (!$attributes_array) {
			return '';
		}

		return ' ' . implode(' ', $attributes_array);
	}

	/**
	* Returns an id name for an input field
	* @param string $name The name of the field
	*/
	public function getIdName(string $name) : string
	{
		static $id_index = [];
		$index = 1;

		$name = $this->escapeId($name);

		if (!isset($id_index[$name])) {
			$id_index[$name] = 1;
		} else {
			$id_index[$name]++;
		}

		return $name . '-' . $id_index[$name];
	}

	/**
	* Escapes an ID
	* @param string $id The id to escape
	* @return string The escaped id
	*/
	protected function escapeId(string $id) : string
	{
		$id = str_replace(['[', ']', ')', '(', '.', '#'], '', $id);
		$id = str_replace(' ', '-', $id);

		return $id;
	}
}
