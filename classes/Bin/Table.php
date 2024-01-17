<?php
/**
* The Table Bin Handler
* @package Mars
*/

namespace Mars\Bin;

/**
 * The Table Bin Handler
 * Displays a table
 */
class Table extends Base
{
	/**
	 * @var string $header_color The default header color
	 */
	protected string $header_color = 'header';

	/**
	 * @var string $header_align The default header align
	 */
	protected string $header_align = 'left';

	/**
	 * @var string $data_color The default data color
	 */
	protected string $data_color = 'default';

	/**
	 * @var string $data_align The default data align
	 */
	protected string $data_align = 'left';

	/**
	 * @var int $padding_left The default left padding
	 */
	protected int $padding_left = 2;

	/**
	 * @var int $padding_right The default right padding
	 */
	protected int $padding_right = 2;

	/**
	 * Prints a table
	 * @param array $headers The header data
	 * @param array $data The data to print
	 * @param array $colors The colors to use. $colors[0] is the header's color
	 * @param array $align Determines how the headers/cells are align. $align[0] is the header's alignment
	 * @param array $paddings_left The number of left chars to apply, if any
	 * @param array $paddings_right The number of left chars to apply, if any
	 */
	public function print(array $headers, array $data, array $colors = [], array $align = [], array $paddings_left = [], array $paddings_right = [])
	{
		$all_data = array_merge([$headers], $data);
		$max = $this->getMaxLength($all_data, $paddings_right, $paddings_left);

		$chars = array_sum($max) + 2 + count($headers) - 1;
		$this->app->bin->printRepeat('-', $chars);
		$this->printHeader($headers, $colors, $align, $paddings_left, $paddings_right, $max);
		$this->app->bin->printRepeat('-', $chars);

		$this->printData($data, $colors, $align, $paddings_left, $paddings_right, $max);

		$this->app->bin->printRepeat('-', $chars);
	}

	/**
	 * Prints a row
	 */
	protected function printRow(array $row, string $color, string $alignment, array $paddings_left, array $paddings_right, array $max)
	{
		echo "|";
		foreach ($row as $i => $text) {
			$max_value = $max[$i];
			$padding_left = $paddings_left[$i] ?? $this->padding_left;
			$padding_right = $paddings_right[$i] ?? $this->padding_right;

			$this->app->bin->print($this->getText($text, $alignment, $padding_left, $padding_right, $max_value), $color, false);
			echo "|";
		}

		$this->app->bin->printNewline();
	}

	/**
	 * Prints the header
	 */
	protected function printHeader(array $headers, array $colors, array $align, array $paddings_left, array $paddings_right, array $max)
	{
		$color = $colors[0] ?? $this->header_color;
		$alignment = $align[0] ?? $this->header_align;

		$this->printRow($headers, $color, $alignment, $paddings_left, $paddings_right, $max);
	}

	/**
	 * Prints the data
	 */
	protected function printData(array $data, array $colors, array $align, array $paddings_left, array $paddings_right, array $max)
	{
		$color = $colors[1] ?? $this->data_color;
		$alignment = $align[1] ?? $this->data_align;

		foreach ($data as $data_array) {
			$this->printRow($data_array, $color, $alignment, $paddings_left, $paddings_right, $max);
		}
	}

	/**
	 * Returns the text, aligned and with padding
	 * @param string $text The text
	 * @param string $alignment The alignment [left,center,right]
	 * @param int $padding_left The left padding
	 * @param int $padding_right The right padding
	 * @return stirng The text
	 */
	protected function getText(string $text, string $alignment, int $padding_left, int $padding_right, int $max_value) : string
	{
		$length = strlen($text);

		if ($alignment == 'left') {
			$padding_right = $max_value - $length - $padding_left;
		} elseif ($alignment == 'right') {
			$padding_left = $max_value - $length - $padding_right;
		} else {
			$padding_left = ceil(($max_value - $length) / 2);
			$padding_right = $padding_left;
		}

		$total = $padding_left + $length;
		$text = sprintf("%{$total}s", $text);

		$total = $padding_right + $padding_left + $length;
		if ($total > $max_value) {
			$total = $max_value;
		}

		$text = sprintf("%-{$max_value}s", $text);

		return $text;
	}

	/**
	 * @see \Mars\Bin\Base::getMaxLength()
	 * {@inheritdoc}
	 */
	protected function getMaxLength(array $data, array $paddings_right = [], array $paddings_left = []) : array
	{
		$max = [];
		foreach ($data as $list) {
			foreach ($list as $i => $item) {
				if (!isset($max[$i])) {
					$max[$i] = 0;
				}

				$padding_left = $paddings_left[$i] ?? $this->padding_left;
				$padding_right = $paddings_right[$i] ?? $this->padding_right;
				$length = strlen($item) + $padding_right + $padding_left;

				if ($length > $max[$i]) {
					$max[$i] = $length;
				}
			}
		}

		return $max;
	}
}
