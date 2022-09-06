<?php
/**
* The Cut Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
* The Cut Operation Image Class
*/
class Cut extends Base
{
	/**
	* Cuts a section from an image
	* @param int $cut_width The width of the cut section
	* @param int $cut_height The height of the cut section
	* @param int $cut_x The x point from where the cut should start
	* @param int $cut_y The y point from where the cut should start
	* @param int $width The width of the resulting image. If 0, the image will have the same width as $cut_width
	* @param int $height The height of the resulting image. If 0 the image will have the same height as $cut_height
	*/
	public function process(int $cut_width, int $cut_height, int $cut_x, int $cut_y, int $width, int $height)
	{
		[$source_width, $source_height] = $this->source->getSize();
		$source_ratio = $this->source->getRatio();

		if (!$cut_width) {
			$cut_width = $source_width - $cut_x;
		}
		if (!$cut_height) {
			$cut_height = $source_height - $cut_y;
		}

		if ($cut_width > $source_width) {
			$cut_width = $source_width;
		}
		if ($cut_height > $source_height) {
			$cut_height = $source_height;
		}

		//adjust the cut_width/cut_height if it's outside the image's boundary
		if ($cut_width + $cut_x > $source_width) {
			$cut_width = $source_width - $cut_x;
		}

		if ($cut_height + $cut_y > $source_height) {
			$cut_height = $source_height - $cut_y;
		}

		$destination_x = 0;
		$destination_y = 0;
		$ratio = $cut_width / $cut_height;

		if (!$width && !$height) {
			$width = $cut_width;
			$height = $cut_height;
		} elseif ($width) {
			$height = $width / $source_ratio;
		} elseif ($height) {
			$width = $height * $source_ratio;
		}

		//center the cut section if the destination's width/height is bigger than the section
		if ($width > $cut_width) {
			$destination_x = ($width - $cut_width) / 2;
		}
		if ($height > $cut_height) {
			$destination_y = ($height - $cut_height) / 2;
		}

		return $this->copyResampled((int) $width, (int) $height, (int) $cut_width, (int) $cut_height, $cut_x, $cut_y, (int) $cut_width, (int) $cut_height, (int)$destination_x, (int)$destination_y);
	}
}
