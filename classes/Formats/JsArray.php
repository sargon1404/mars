<?php
/**
* The JsArray Format Class
* @package Mars
*/

namespace Mars\Formats;

/**
* The JsArray Format Class
*/
class JsArray extends Format
{
	/**
	* @see \Mars\Formats\Format::jsArray()
	* {@inheritdoc}
	*/
	public function get($data, ...$params) : string
	{
		$quote = $params[0] ?? true;
		$dont_quote_array = $params[1] ?? [];

		if (!$data) {
			return '[]';
		}

		$list = [];
		foreach ($data as $key => $value) {
			$list[] = $this->getValue($key, $value, $quote, $dont_quote_array);
		}

		return '[' . implode(',', $list) . ']';
	}

	/**
	* Quotes a value, if necesarilly
	* @param string $key The value's key
	* @param string $value The value
	* @param bool $quote If true will put quotes around the array's elements
	* @param array $dont_quote_array If $quote is true, will NOT quote the elements with the keys found in this array
	*/
	protected function getValue(string $key, string $value, bool $quote = true, array $dont_quote_array = []) : string
	{
		$value = $this->app->escape->jsString($value);

		if ($quote) {
			if (!in_array($key, $dont_quote_array)) {
				$value = "'{$value}'";
			}
		}

		return $value;
	}
}
