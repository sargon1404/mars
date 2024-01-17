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
	 * @see \Mars\Format::jsObject()
	 * {@inheritdoc}
	 */
	public function format(array $data, bool $quote = true, array $dont_quote_array = []) : string
	{
		$data = App::array($data);

		if (!$data) {
			return '{}';
		}

		$list = [];
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$value = parent::format($value, $quote, $dont_quote_array);
			} else {
				$value = $this->getValue($key, $value, $quote, $dont_quote_array);
			}

			$list[] = $key . ': ' . $value;
		}

		return '{' . implode(', ', $list) . '}';
	}
}
