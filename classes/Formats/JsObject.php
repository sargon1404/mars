<?php
/**
* The JsObject Format Class
* @package Mars
*/

namespace Mars\Formats;

use Mars\App;

/**
* The JsObject Format Class
*/
class JsObject extends JsArray
{
	/**
	* @see \Mars\Formats\Format::jsArray()
	* {@inheritdoc}
	*/
	public function get($data, ...$params) : string
	{
		$quote = $params[0] ?? true;
		$dont_quote_array = $params[1] ?? [];

		$data = App::array($data);

		if (!$data) {
			return '{}';
		}

		$list = [];
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$value = parent::get($value, $quote, $dont_quote_array);
			} else {
				$value = $this->getValue($key, $value, $quote, $dont_quote_array);
			}

			$list[] = $key . ': ' . $value;
		}

		return '{' . implode(', ', $list) . '}';
	}
}
