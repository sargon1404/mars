<?php
/**
* The List Bin Handler
* @package Mars
*/

namespace Mars\Bin;

/**
 * The List Bin Handler
 * Displays a list
 */
class Listing extends Base
{
	/**
	 * Prints a list
	 * @param array $data The data to print
	 * @param array $colors The colors to use
	 * @param array $paddings_right The number of right chars to apply, if any
	 * @param array $paddings_left The number of left chars to apply, if any
	 */
	public function print(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = [])
	{
		$max = $this->getMaxLength($data, $paddings_right);

		foreach ($data as $text) {
			$this->printMulti($text, $colors, $paddings_left, $max);
		}
	}

	/**
	 * Prints a line of text from multiple pieces
	 * @param array $text_array The text to print
	 * @param array $colors The colors to use
	 * @param array $paddings_left The number of left chars to apply, if any
	 * @param array $paddings_right The number of left chars to apply, if any
	 * @return static
	 */
	public function printMulti(array $text_array, array $colors = [], array $paddings_left = [], array $paddings_right = []) : static
	{
		foreach ($text_array as $i => $text) {
			$color = $colors[$i] ?? '';
			$padding_left = $paddings_left[$i] ?? 0;
			$padding_right = $paddings_right[$i] ?? 0;

			if ($padding_left) {
				$text = sprintf("%{$padding_left}s", $text);
			}
			if ($padding_right) {
				$text = sprintf("%-{$padding_right}s", $text);
			}

			$this->app->bin->print($text, $color, false);
		}

		$this->app->bin->printNewline();

		return $this;
	}
}
