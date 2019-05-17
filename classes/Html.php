<?php
/**
* The HTML Class
* @package Mars
*/

namespace Mars;

/**
* The HTML Class
* Html generating methods
*/
class Html
{
	use AppTrait;

	/**
	* Html Escapes $value
	* @param string $value The value to escape
	* @param bool $escape_html If false, will return $value unchanged
	* @return string The escaped value
	*/
	public function escape(string $value, bool $escape_html = true) : string
	{
		if ($escape_html) {
			return App::e($value);
		}

		return $value;
	}

	/**
	* Returns an id name for an input field
	* @param string $name The name of the field
	*/
	protected function getIdName(string $name) : string
	{
		static $id_index = [];
		$index = 1;

		if (!isset($id_index[$name])) {
			$id_index[$name] = 1;
		} else {
			$id_index[$name]++;
		}

		return $name . '-' . $id_index[$name];
	}

	/**
	* Merges the attributes and returns the html code
	* @param array $attributes The attributes in the format name => value
	* @param bool $escape_html If true it will call escape on the values
	* @return string The attribute's html code
	*/
	public function buildAttributes(array $attributes, bool $escape_html = true) : string
	{
		$attributes_array = [];

		foreach ($attributes as $name => $value) {
			//don't escape if $value is an array
			$escape = $escape_html;
			if (is_array($value)) {
				$value = reset($value);
				$escape = false;
			}

			if ($value) {
				if (is_bool($value)) {
					$attributes_array[] = $name;
				} else {
					$attributes_array[] = $name . '="' . $this->escape($value, $escape) . '"';
				}
			}
		}

		if (!$attributes_array) {
			return '';
		}

		return ' ' . implode(' ', $attributes_array);
	}

	/**
	* Returns the attributes
	* @param string $class The class, if any
	* @param string $id The id, if any
	* @param array $attributes The attributes in the format name => value
	* @param bool $escape_html If true it will call escape on the attribute values
	* @return string The attribute's html code
	*/
	public function getAttributes(string $class = '', string $id = '', array $attributes = [], bool $escape_html = true) : string
	{
		$attributes = $attributes + ['class' => $class, 'id' => $this->app->escape->id($id)];

		return $this->buildAttributes($attributes, $escape_html);
	}

	/**
	* Returns a html tag
	* @param string $type The type of the html tag. Eg: strong,div etc..
	* @param string $text The text of the tag
	* @param string $class The class of the tag,if any
	* @param string $id The id of the tag, if any
	* @param array $attributes Extra attributes in the format name => value
	* @param bool $escape_html If true it will call escape on the text
	* @return string The html code
	*/
	public function tag(string $type, string $text, string $class = '', string $id = '', array $attributes = [], bool $escape_html = true) : string
	{
		$text = $this->escape($text, $escape_html);
		$attributes = $this->getAttributes($class, $id, $attributes);

		return "<{$type}{$attributes}>{$text}</{$type}>\n";
	}

	/**
	* Builds an unordered list
	* @param array $items The lists's items
	* @param string $class The class of the ul element, if any
	* @param string $id The id of the ul element, if any
	* @param bool $escape_html If true it will call escape on each element
	* @return string The html code
	*/
	public function ul(array $items, string $class = '', string $id = '', bool $escape_html = true) : string
	{
		return $this->list('ul', $items, $class, $id, $escape_html);
	}

	/**
	* Builds an ordered list
	* @param array $items The lists's items
	* @param string $class The class of the ul element, if any
	* @param string $id The id of the ul element, if any
	* @param bool $escape_html If true it will call escape on each element
	* @return string The html code
	*/
	public function ol(array $items, string $class = '', string $id = '', bool $escape_html = true) : string
	{
		return $this->list('ol', $items, $class, $id, $escape_html);
	}

	/**
	* Builds a list
	* @param string $type The list's type. Eg: ul or ol
	* @param array $items The list's items
	* @param string $class The class of the ul element, if any
	* @param string $id The id of the ul element, if any
	* @param bool $escape_html If true it will call escape on each element
	* @return string The html code
	*/
	public function list(string $type, array $items, string $class = '', string $id = '', bool $escape_html = true) : string
	{
		if (!$items) {
			return '';
		}

		$attributes = $this->getAttributes($class, $id);

		$html = "<{$type}{$attributes}>\n";

		foreach ($items as $item) {
			$html.= "<li>" . $this->escape($item, $escape_html) . "</li>\n";
		}

		$html.= "</{$type}>\n";

		return $html;
	}

	/**
	* Creates an img tag
	* @param string $src The image's src attribute
	* @param int $width The image's width
	* @param int $height The image's height
	* @param alt $alt The alt attribute.If empty it will be determined from the basename of the source
	* @param string $title The title attribute of the image
	* @param string $class The class of the image,if any
	* @param string $id The id of the image element,if any
	* @param bool $escape_html If true it will call escape on the src attribute
	* @return string The html code
	*/
	public function img(string $src, int $width = 0, int $height = 0, string $alt = '', string $title = '', string $class = '', string $id = '', bool $escape_html = true) : string
	{
		if (!$src) {
			return '';
		}
		if (!$alt) {
			$alt = basename($src);
		}

		$src = $this->escape($src, $escape_html);
		$attributes = $this->getAttributes($class, $id, ['width' => $width, 'height' => $height, 'alt' => $alt, 'title' => $title]);

		return "<img src=\"{$src}\"{$attributes}>";
	}

	/**
	* Returns the width and height attributes of an image
	* @param int $width The image's width
	* @param int $height The image's height
	* @return string The html code
	*/
	public function imgWh(int $width = 0, int $height = 0) : string
	{
		return $this->buildAttributes(['width' => $width, 'height' => $height]);
	}

	/**
	* Creates a link
	* @param string $url The link's url
	* @param string $text The link text.If empty $url will be displayed insteed
	* @param string $class The class of the image,if any
	* @param string $id The id of the image element,if any
	* @param string $title Link's title.
	* @param string $target Link's target.
	* @param string $rel Link's rel attribute.
	* @param bool $escape_html If true it will call escape on url&text
	* @return string The html code
	*/
	public function a(string $url, string $text = '', string $class = '', string $id = '', string $title = '', string $target = '', string $rel = '', bool $escape_html = true) : string
	{
		if (!$url && !$text) {
			return '';
		}

		if (!$url) {
			$url = 'javascript:void(0)';
		}
		if (!$text) {
			$text = $url;
		}

		$url = $this->escape($url, $escape_html);
		$text = $this->escape($text, $escape_html);
		$attributes = $this->getAttributes($class, $id, ['title' => $title, 'target' => $target, 'rel' => $rel]);

		return "<a href=\"{$url}\"{$attributes}>{$text}</a>";
	}

	/**
	* Alias for a()
	* @see \Mars\Html::a()
	*/
	public function link(string $url, string $text = '', string $class = '', string $id = '', string $title = '', string $target = '', string $rel = '', bool $escape_html = true) : string
	{
		return $this->a($url, $text, $class, $id, $title, $target, $rel, $escape_html);
	}

	/**
	* Builds the title/target/rel properties of a link
	* @param string $title The value of the title property
	* @param string $target The value of the target property
	* @param string $rel The value of the rel property
	* @return string The html code
	*/
	public function aAttributes(string $title = '', string $target = '', string $rel = '') : string
	{
		return $this->buildAttributes(['title' => $title, 'target' => $target, 'rel' => $rel]);
	}

	/**
	* Alias for aAttributes
	* @see \Mars\Html::aAttributes()
	*/
	public function linkAttributes(string $title = '', string $target = '', string $rel = '') : string
	{
		return $this->aAttributes($title, $target, $rel);
	}

	/**
	* Builds a <form> tag
	* @param string $url The url used as the form's action
	* @param string $id The form's id, if any
	* @param array $attributes Extra attributes in the format name => value
	* @param string $method The form's method
	* @return string The html code
	*/
	public function formStart(string $url, string $id = '', array $attributes = [], string $method = 'post') : string
	{
		$url = App::e($url);
		$attributes = $this->getAttributes('', $id, $attributes + ['method' => $method]);

		return "<form action=\"{$url}\"{$attributes}>\n";
	}

	/**
	* Builds a </form> tag
	* @return string The html code
	*/
	public function formEnd() : string
	{
		return '</form>' . "\n";
	}

	/**
	* Builds an input field
	* @param string $name The name of the field
	* @param string $value The value of the field
	* @param bool $required If true, this is a required field
	* @param string $placeholder Placeholder text
	* @param string $class The class of the input control.
	* @param string $id The id of the input control
	* @param array $attributes Extra attributes in the format name => value
	* @param string $type The type of the field. Default: text
	* @return string The html code
	*/
	public function input(string $name, string $value = '', bool $required = false, string $placeholder = '', string $class = '', string $id = '', array $attributes = [], string $type = 'text') : string
	{
		if (!$id) {
			$id = $name;
		}

		$name = App::e($name);
		$value = App::e($value);
		$attributes = $this->getAttributes($class, $id, $attributes + ['placeholder' => $placeholder, 'required' => $required]);

		return "<input type=\"{$type}\" name=\"{$name}\" value=\"{$value}\"{$attributes}>\n";
	}

	/**
	* Builds a text input field
	* @param string $name The name of the field
	* @param string $value The value of the field
	* @param bool $required If true, this is a required field
	* @param string $placeholder Placeholder text
	* @param string $class The class of the input control.
	* @param string $id The id of the input control
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputText(string $name, string $value = '', bool $required = false, string $placeholder = '', string $class = '', string $id = '', array $attributes = []) : string
	{
		return $this->request($name, $value, $required, $placeholder, $class, $id, $attributes, 'text');
	}

	/**
	* Builds an button input field
	* @param string $name The name of the field
	* @param string $value The value of the field
	* @param string $class The class of the input control.
	* @param string $id The id of the input control
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputButton(string $name, string $value = '', string $class = '', string $id = '', array $attributes = []) : string
	{
		return $this->request($name, $value, false, '', $class, $id, $attributes, 'button');
	}

	/**
	* Builds an button input field
	* @param string $value The value of the field
	* @param string $class The class of the input control.
	* @param string $id The id of the input control
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function inputSubmit(string $value = '', string $class = '', string $id = '', array $attributes = []) : string
	{
		$value = App::e($value);
		$attributes = $this->getAttributes($class, $id, $attributes);

		return "<input type=\"submit\" value=\"{$value}\"{$attributes}>\n";
	}

	/**
	* Builds a form input submit
	* @param string $name The name of the hidden field
	* @param string $value The value of the hidden field
	* @return string The html code
	*/
	public function inputHidden(string $name, string $value, string $id = '') : string
	{
		$name = App::e($name);
		$value = App::e($value);
		$attributes = $this->getAttributes('', $id);

		return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\"{$attributes}>\n";
	}

	/**
	* Returns checked="checked" if $checked is true, empty if false
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
	* Returns checked="checked" if $value is found in $array
	* @param string $value The value to look for
	* @param array $array The arrach to search for the value
	* @return string
	*/
	public function checkedInArray($value, array $array) : string
	{
		return $this->checked(in_array($value, $array));
	}

	/**
	* Returns disabled="disabled" if $disabled is true, empty if false
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
	* Returns required="required" if $required is true
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
	* Returns a form checkbox field
	* @param string $name The name of the field
	* @param string $label The label of the checkbox
	* @param string $value The value of the checkbox
	* @param bool $checked If true the checkbox will be checked
	* @param string $class The class of the checkbox, if any
	* @param string $id The id of the checkbox, if any
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function checkbox(string $name, string $label = '', string $value = '1', bool $checked = true, string $class = '', string $id = '', array $attributes = []) : string
	{
		$name = App::e($name);
		$value = App::e($value);

		if (!$id) {
			$id = $this->getIdName($name);
		}

		$attributes = $this->getAttributes($class, $id, $attributes + ['checked' => $checked]);

		return $this->getCheckbox($name, $value, $label, $id, $attributes);
	}

	/**
	* Builds the html code of a checkbox
	* @internal
	*/
	protected function getCheckbox(string $name, string $value, string $label, string $id, string $attributes) : string
	{
		$html = "<input type=\"checkbox\" name=\"{$name}\" value=\"{$value}\"{$attributes}>";
		$html.= '<label for="' . $this->app->escape->id($id) . '">' . App::e($label) . '</label>';

		return $html;
	}

	/**
	* Returns a form radio field
	* @param string $name The name of the field
	* @param string $label The label of the radio
	* @param string $value The value of the radio button
	* @param bool $checked If true the radio button will be checked
	* @param string $class The class of the radio, if any
	* @param string $id The id of the radio, if any
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function radio(string $name, string $label = '', string $value = '1', bool $checked = true, string $class = '', string $id = '', array $attributes = []) : string
	{
		$name = App::e($name);
		$value = App::e($value);

		if (!$id) {
			$id = $this->getIdName($name);
		}

		$attributes = $this->getAttributes($class, $id, $attributes + ['checked' => $checked]);

		return $this->getRadio($name, $value, $label, $id, $attributes);
	}

	/**
	* Builds the html code of a radio
	* @internal
	*/
	protected function getRadio(string $name, string $value, string  $label, string $id, string $attributes) : string
	{
		$html = "<input type=\"radio\" name=\"{$name}\" value=\"{$value}\"{$attributes}>";
		$html.= '<label for="' . $this->app->escape->id($id) . '">' . App::e($label) . '</label>';

		return $html;
	}

	/**
	* Builds a <select> tag
	* @param string $name The name of the select control
	* @param string $id The select's id, if any
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function selectStart(string $name, string $id = '', array $attributes = []) : string
	{
		$attributes['size'] = $attributes['size'] ?? 1;

		$attributes = $this->getAttributes('', $id, $attributes);

		return "<select{$attributes}>\n";
	}

	/**
	* Builds a </select> tag
	* @return string The html code
	*/
	public function selectEnd() : string
	{
		return '</select>' . "\n";
	}

	/**
	* Builds a select control
	* @param string $name The name of the select control
	* @param array $options Array containing the options [$name=>$value]. If $value is an array the first element will be the actual value. The second is a bool value determining if the field is an optgroup rather than a option
	* @param mixed $selected The name of the option that should be selected [string or array if $multiple =  true]
	* @param bool $required If true,it will be a required control
	* @param string $class The class of the select,if any
	* @param string $id The id of the select,if any
	* @param int $size The size of the select control
	* @param bool $multiple If true multiple options can be selected
	* @param bool $use_only_value If true the field_name will be the same as field_value
	* @param array $attributes Extra attributes in the format name => value
	* @return string The html code
	*/
	public function select(string $name, array $options, $selected = '', bool $required = false, string $class = '', string $id = '', int $size = 1, bool $multiple = false, bool $use_only_value = false, array $attributes = []) : string
	{
		if (!$options) {
			return '';
		}

		if (!$id) {
			$id = $name;
		}

		$html = $this->selectStart($name, $id, $attributes + ['class' => $class, 'size' => $size, 'required' => $required, 'multiple' => $multiple]);
		$html.= $this->selectOptions($options, $selected, $use_only_value);
		$html.= $this->selectEnd();

		return $html;
	}

	/**
	* Builds multiple options tags-used in drop-down boxes.
	* @param array $options Array containing the options [$name=>$value]. If $value is an array the first element will be the actual value. The second is a bool value determining if the field is an optgroup rather than a option
	* @param mixed $selected The name of the option that should be selected [string or array if $multiple =  true]
	* @param bool $use_only_value If true the field_name will be the same as field_value
	* @return string The html code
	*/
	public function selectOptions(array $options, $selected = '', bool $use_only_value = false) : string
	{
		if (!$options) {
			return '';
		}

		$html = '';
		$use_optgroup = false;
		$first_optgroup = true;
		$used_optgroup = false;

		if (is_array(current($options))) {
			$use_optgroup = true;
		}

		foreach ($options as $field_name => $field_value) {
			$is_optgroup = false;
			$field_name = App::e($field_name);
			if ($use_optgroup) {
				if ($field_value[1]) {
					$is_optgroup = true;
				}

				$field_value = App::e($field_value[0]);
			} else {
				$field_value = App::e($field_value);
			}

			if ($use_only_value) {
				$field_name = $field_value;
			}

			$sel = '';
			if (is_array($selected)) {
				if (in_array($field_name, $selected)) {
					$sel = ' selected="selected"';
				}
			} else {
				if ($use_only_value) {
					if ($field_value == $selected) {
						$sel = ' selected="selected"';
					}
				} else {
					if ($field_name == $selected) {
						$sel = ' selected="selected"';
					}
				}
			}
			if ($is_optgroup) {
				if (!$first_optgroup) {
					$html.= "</optgroup>\n";
				} else {
					$first_optgroup = false;
				}

				$html.= "<optgroup label=\"{$field_value}\"{$sel}>\n";
				$used_optgroup = true;
			} else {
				if (!$field_value) {
					$field_value = '&nbsp;';
				}

				$html.= "<option value=\"{$field_name}\"{$sel}>{$field_value}</option>\n";
			}
		}
		if ($use_optgroup && $used_optgroup) {
			$html.= "</optgroup>\n";
		}

		return $html;
	}
}
