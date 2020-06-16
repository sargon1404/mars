<?php
/**
* The HTML Class
* @package Mars
*/

namespace Mars;

use Mars\Html\TagInterface;
use Mars\Html\Tag;

/**
* The HTML Class
* Html generating methods
*/
class Html
{
	use AppTrait;

	protected array $supported_tags = [
		'ul' => '\Mars\Html\Lists\UL',
		'ol' => '\Mars\Html\Lists\OL',
		'checkbox' => '\Mars\Html\Input\Checkbox',
		'radio' => '\Mars\Html\Input\Radio',
		'select_options' => '\Mars\Html\Input\SelectOptions',
		'select' => '\Mars\Html\Input\Select',
	];

	/**
	* Adds a supported tag to the list
	* @param string $name The name of the tag
	* @param string $class The name of the class handling the tag
	* @return $this
	*/
	public function addSupportedTag(string $name, string $class)
	{
		$this->supported_tags[$name] = $class;

		return $this;
	}

	/**
	* Removes a supported tag
	* @param string $name The name of the tag
	* @return $this
	*/
	public function removeSupportedTag(string $name)
	{
		unset($this->supported_tag[$name]);

		return $this;
	}

	/**
	* Returns an id name for an input field
	* @param string $name The name of the field
	*/
	protected function getIdName(string $name) : string
	{
		static $id_index = [];
		$index = 1;

		$name = $this->app->escape->id($name);

		if (!isset($id_index[$name])) {
			$id_index[$name] = 1;
		} else {
			$id_index[$name]++;
		}

		return $name . '-' . $id_index[$name];
	}

	/**
	* Returns a tag
	* @param string $type The tag's type
	* @param array $attributes
	* @param string|array $text The tag's text, if any
	* @param array $properties Extra properties to pass to the tag object
	* @param string $escape If true, will escape text
	* @return Tag The tag
	*/
	public function getTag(string $type, array $attributes = [], $text = '', array $properties = [], bool $escape = true) : TagInterface
	{
		$tag = null;

		if (isset($this->supported_tags[$type])) {
			$class = $this->supported_tags[$type];
			$tag = new $class($attributes, $properties, $escape);
		} else {
			$tag = new Tag($type, $attributes, $text, $escape);
		}

		return $tag;
	}

	/**
	* Builds an unordered list
	* @param array $items The lists's items
	* @param array $attributes The list's attributes
	* @param bool $escape If true it will call escape on each item
	* @return string The html code
	*/
	public function ul(array $items, array $attributes = [], bool $escape = true) : string
	{
		return $this->getTag('ul', $attributes, '', ['items' => $items], $escape)->get();
	}

	/**
	* Builds an ordered list
	* @param array $items The lists's items
	* @param array $attributes The list's attributes
	* @param bool $escape If true it will call escape on each item
	* @return string The html code
	*/
	public function ol(array $items, array $attributes = [], bool $escape = true) : string
	{
		return $this->getTag('ol', $attributes, '', ['items' => $items], $escape)->get();
	}

	/**
	* Creates an img tag
	* @param string $url The image's url
	* @param int $width The image's width
	* @param int $height The image's height
	* @param alt $alt The alt attribute.If empty it will be determined from the basename of the source
	* @param array $attributes The image's attributes
	* @return string The html code
	*/
	public function img(string $url, int $width = 0, int $height = 0, string $alt = '', array $attributes = []) : string
	{
		if (!$alt) {
			$alt = basename($url);
		}

		$attributes = ['src' => $url, 'alt' => $alt, 'width' => $width, 'height' => $height] + $attributes;

		return $this->getTag('img', $attributes)->get();
	}

	/**
	* Returns the width and height attributes of an image
	* @param int $width The image's width
	* @param int $height The image's height
	* @return string The html code
	*/
	public function imgWh(int $width = 0, int $height = 0) : string
	{
		return $this->getTag('img')->getAttributes(['width' => $width, 'height' => $height]);
	}

	/**
	* Creates a link
	* @param string $url The link's url
	* @param string $text The link text.If empty $url will be displayed insteed
	* @param array $attributes The link's attributes
	* @return string The html code
	*/
	public function a(string $url, string $text = '', array $attributes = []) : string
	{
		if (!$url) {
			$url = 'javascript:void(0)';
		}
		if (!$text) {
			$text = $url;
		}

		$attributes = ['href' => $url] + $attributes;

		return $this->getTag('a', $attributes, $text)->get();
	}

	/**
	* Alias for a()
	* @see \Mars\Html::a()
	*/
	public function link(string $url, string $text = '', array $attributes = []) : string
	{
		return $this->a($url, $text, $attributes);
	}

	/**
	* Returns checked if $checked is true, empty if false
	* @param bool $checked The checked flag
	* @return string
	*/
	public function checked(bool $checked = true) : string
	{
		if ($checked) {
			return ' checked';
		}

		return '';
	}

	/**
	* Returns checked if $value is found in $array
	* @param string $value The value to look for
	* @param array $array The arrach to search for the value
	* @return string
	*/
	public function checkedInArray($value, array $array) : string
	{
		return $this->checked(in_array($value, $array));
	}

	/**
	* Returns disabled if $disabled is true, empty if false
	* @param bool $disabled The disabled flag
	* @return string
	*/
	public function disabled(bool $disabled = true) : string
	{
		if ($disabled) {
			return ' disabled';
		}

		return '';
	}

	/**
	* Returns style="display:none" if $hidden is true, empty if false
	* @param bool $hidden The hidden flag
	* @return string
	*/
	public function hidden(bool $hidden = true) : string
	{
		if ($hidden) {
			return ' style="display:none"';
		}

		return '';
	}

	/**
	* Returns required if $required is true
	* @param bool $required The required flag
	* @return string
	*/
	public function required(bool $required = true) : string
	{
		if ($required) {
			return ' required';
		}

		return '';
	}

	/**
	* Returns the opening tag of a form
	* @param string $url The url used as the form's action
	* @param string $id The form's id, if any
	* @param array $attributes Extra attributes in the format name => value
	* @param string $method The form's method
	* @return string The html code
	*/
	public function formOpen(string $url, string $id = '', array $attributes = [], string $method = 'post') : string
	{
		$attributes = $attributes + ['action' => $url, 'id' => $id, 'method' => $method];

		return $this->getTag('form', $attributes)->get();
	}

	/**
	* Returns the closing tag of a form
	* @return string The html code
	*/
	public function formClose() : string
	{
		return $this->getTag('form')->close();
	}

	/**
	* Builds an input field
	* @param string $name The name of the field
	* @param string $value The value of the field
	* @param string $placeholder Placeholder text
	* @param bool $required If true, this is a required field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function input(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
	{
		$type = $attributes['type'] ?? 'text';
		$id = $attributes['id'] ?? $this->app->escape->id($name);

		$attributes = ['type' => $type, 'name' => $name, 'id' => $id, 'value'=> $value, 'placeholder' => $placeholder, 'required' => $required] + $attributes;

		return $this->getTag('input', $attributes)->get();
	}

	/**
	* Alias for input()
	* @see \Mars\Html::input()
	*/
	public function inputText(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
	{
		return $this->input($name, $value, $placeholder, $required, $attributes);
	}

	/**
	* Builds a hidden input field
	* @param string $name The name of the hidden field
	* @param string $value The value of the hidden field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputHidden(string $name, string $value, array $attributes = []) : string
	{
		return $this->input($name, $value, '', false, ['type' => 'hidden'] + $attributes);
	}

	/**
	* Builds an email input field
	* @param string $name The name of the hidden field
	* @param string $value The value of the hidden field
	* @param string $placeholder Placeholder text
	* @param bool $required If true, this is a required field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputEmail(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
	{
		return $this->input($name, $value, $placeholder, $required, ['type' => 'email'] + $attributes);
	}

	/**
	* Builds a password input field
	* @param string $name The name of the hidden field
	* @param string $value The value of the hidden field
	* @param bool $required If true, this is a required field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputPassword(string $name, string $value = '', bool $required = false, array $attributes = []) : string
	{
		return $this->input($name, $value, '', $required, ['type' => 'password'] + $attributes);
	}

	/**
	* Builds a phone input field
	* @param string $name The name of the hidden field
	* @param string $value The value of the hidden field
	* @param string $placeholder Placeholder text
	* @param bool $required If true, this is a required field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputPhone(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
	{
		return $this->input($name, $value, $placeholder, $required, ['type' => 'tel'] + $attributes);
	}

	/**
	* Builds an submit button field
	* @param string $value The value of the field
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function submit(string $value = '', array $attributes = []) : string
	{
		$attributes = ['type' => 'submit', 'value'=> $value] + $attributes;

		return $this->getTag('input', $attributes)->get();
	}

	/**
	* Returns a form checkbox field
	* @param string $name The name of the field
	* @param string $label The label of the checkbox
	* @param string $value The value of the checkbox
	* @param bool $checked If true the checkbox will be checked
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function checkbox(string $name, string $label = '', string $value = '1', bool $checked = true, array $attributes = []) : string
	{
		$id = $attributes['id'] ?? $this->getIdName($name);

		$attributes = ['id' => $id, 'value' => $value, 'checked' => $checked];

		return $this->getTag('checkbox', $attributes, '', ['label' => $label])->get();
	}

	/**
	* Returns a form radio field
	* @param string $name The name of the field
	* @param string $label The label of the radio
	* @param string $value The value of the radio button
	* @param bool $checked If true the radio button will be checked
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function radio(string $name, string $label = '', string $value = '1', bool $checked = true, array $attributes = []) : string
	{
		$id = $attributes['id'] ?? $this->getIdName($name);

		$attributes = ['id' => $id, 'value' => $value, 'checked' => $checked];

		return $this->getTag('checkbox', $attributes, '', ['label' => $label])->get();
	}

	/**
	* Builds a <select> tag
	* @param string $name The name of the select control
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function selectOpen(string $name, array $attributes = []) : string
	{
		$attributes['size'] = $attributes['size'] ?? 1;
		$id = $attributes['id'] ?? $this->app->escape->id($name);

		$attributes = ['name' => $name, 'id' => $id] + $attributes;

		return $this->getTag('select', $attributes)->open();
	}

	/**
	* Builds a </select> tag
	* @return string The html code
	*/
	public function selectClose() : string
	{
		return $this->getTag('select')->close();
	}

	/**
	* Builds a select control
	* @param string $name The name of the select control
	* @param array $options Array containing the options [$name=>$value]. If $value is an array the first element will be the actual value. The second is a bool value determining if the field is an optgroup rather than a option
	* @param string|array $selected The name of the option that should be selected [string or array if $multiple =  true]
	* @param bool $required If true,it will be a required control
	* @param array $attributes Extra attributes in the format name => value
	* @param bool $multiple If true multiple options can be selected
	* @return string The html code
	*/
	public function select(string $name, array $options, $selected = '', bool $required = false, array $attributes = [], bool $multiple = false) : string
	{
		$attributes['size'] = $attributes['size'] ?? 1;
		$id = $attributes['id'] ?? $this->app->escape->id($name);

		$attributes = ['name' => $name, 'id' => $id, 'required' => $required, 'multiple' => $multiple] + $attributes;

		return $this->getTag('select', $attributes, '', ['options' => $options, 'selected' => $selected])->get();
	}

	/**
	* Builds multiple options tags-used in drop-down boxes.
	* @param array $options Array containing the options [$name=>$value]. If $value is an array the first element will be the actual value. The second is a bool value determining if the field is an optgroup rather than a option
	* @param string|array $selected The name of the option that should be selected [string or array if $multiple =  true]
	* @return string The html code
	*/
	public function selectOptions(array $options, $selected = '') : string
	{
		return $this->getTag('select_options', [], '', ['options' => $options, 'selected' => $selected])->get();
	}
}
