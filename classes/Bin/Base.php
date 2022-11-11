<?php
/**
* The Base Bin Handler
* @package Mars
*/

namespace Mars\Bin;

/**
* The Base Bin Handler
*/
abstract class Base
{
	use \Mars\AppTrait;

	/**
	* @var int $padding_right The default right padding
	*/
	protected int $padding_right = 5;

	/**
	* Returns the max length of a column
	* @param array $data The data where to look for the max length
	* @param array $paddings_right The number of right chars to apply, if any
	* @return array The max length
	*/
	protected function getMaxLength(array $data, array $paddings_right = []) : array
	{
		$max = [];
		foreach ($data as $list) {
			foreach ($list as $i => $item) {
				if (!isset($max[$i])) {
					$max[$i] = 0;
				}

				$padding_right = $paddings_right[$i] ?? $this->padding_right;
				$length = strlen($item) + $padding_right;

				if ($length > $max[$i]) {
					$max[$i] = $length;
				}
			}
		}

		return $max;
	}
}
