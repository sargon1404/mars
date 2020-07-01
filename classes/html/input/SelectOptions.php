<?php
/**
* The Select Options Class
* @package Mars
*/

namespace Mars\Html\Input;

use Mars\App;

/**
* The Select Options Class
* Renders the select options
*/
class SelectOptions extends Options
{
	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritDocs}
	*/
	public function get() : string
	{
		if (!$this->options) {
			return '';
		}

		$html = '';
		$use_optgroup = false;
		$first_optgroup = true;
		$used_optgroup = false;

		if (is_array(current($this->options))) {
			$use_optgroup = true;
		}

		foreach ($this->options as $name => $value) {
			$name = App::e($name);

			$is_optgroup = false;
			if ($use_optgroup) {
				if ($value[1]) {
					$is_optgroup = true;
				}

				$value = App::e($value[0]);
			} else {
				$value = App::e($value);
			}

			$selected = '';
			if (is_array($this->selected)) {
				if (in_array($name, $this->selected)) {
					$selected = ' selected';
				}
			} else {
				if ($name == $this->selected) {
					$selected = ' selected';
				}
			}

			if ($is_optgroup) {
				if (!$first_optgroup) {
					$html.= "</optgroup>\n";
				} else {
					$first_optgroup = false;
				}

				$html.= "<optgroup label=\"{$value}\"{$selected}>\n";
				$used_optgroup = true;
			} else {
				if (!$value) {
					$value = '&nbsp;';
				}

				$html.= "<option value=\"{$name}\"{$selected}>{$value}</option>\n";
			}
		}
		if ($use_optgroup && $used_optgroup) {
			$html.= "</optgroup>\n";
		}

		return $html;
	}
}
