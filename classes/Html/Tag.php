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
class Tag implements TagInterface
{
	use \Mars\AppTrait;

	/**
	 * @var string $type The tag's attributes, if any
	 */
	public array $attributes = [];

	/**
	 * @var bool $escape If true, will escape the content
	 */
	public bool $escape = true;

	/**
	 * @var string $tag The tag's tag
	 */
	protected string $tag = '';

	/**
	 * @var string $newline Newline to add after the tag, if any
	 */
	protected string $newline = "\n";

	/**
	 * Opens the tag
	 * @param array $attributes The tag's attributes
	 * @return string The html code
	 */
	public function open(array $attributes = []) : string
	{
		$attributes = $this->getAttributes($attributes);

		return "<{$this->tag}{$attributes}>" . $this->newline;
	}

	/**
	 * Closes the tag
	 * @return string The html code
	 */
	public function close() : string
	{
		return "</{$this->tag}>" . $this->newline;
	}

	/**
	 * @see \Mars\Html\TagInterface::get()
	 * {@inheritdoc}
	 */
	public function html(string $text = '', array $attributes = [], array $properties = []) : string
	{
		$attributes = $this->getAttributes($attributes);

		if ($text) {
			return "<{$this->tag}{$attributes}>" . $this->escape($text) . "</{$this->tag}>" . $this->newline;
		} else {
			return "<{$this->tag}{$attributes}>" . $this->newline;
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
			return $this->app->escape->html($value);
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
					$value = $this->app->escape->html($value);
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
	 * Generates an id attribute, if one doesn't already exists
	 * @param array $attributes The attributes in the format name => value
	 * @return array The attributes, including the id field
	 */
	public function generateIdAttribute(array $attributes) : array
	{
		if (!isset($attributes['no-id']) && !isset($attributes['id']) && isset($attributes['name'])) {
			$attributes['id'] ??= $this->getIdName($attributes['name']);
		}

		return $attributes;
	}

	/**
	 * Returns an id name for an input field
	 * @param string $name The name of the field
	 */
	public function getIdName(string $name) : string
	{
		static $id_index = [];

		$name = $this->escapeId($name);

		if (!isset($id_index[$name])) {
			$id_index[$name] = 0;

			return $name;
		} else {
			$id_index[$name]++;

			return $name . '-' . $id_index[$name];
		}
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
