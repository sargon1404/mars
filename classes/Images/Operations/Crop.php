<?php
/**
* The Crop Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
* The Crop Operation Image Class
*/
class Crop extends Base
{
	/**
	* Crops the image
	* @param int $width The width of the cropped image
	* @param int $height The height of the cropped image
	*/
	public function process(int $width, int $height)
	{
		[$source_width, $source_height] = $this->source->getSize();
		$ratio = $width / $height;

		$source_x = 0;
		$source_y = 0;
		$crop_width = 0;
		$crop_height = 0;

		if ($source_width >= $source_height) {
			$crop_width = $source_height * $ratio;
			$crop_height = $source_height;
			$source_x = ($source_width - $crop_width) / 2;

			if ($source_x < 0) {
				$crop_width = $source_width;
				$crop_height = $source_width / $ratio;

				$source_x = 0;
				$source_y = ($source_height - $crop_height) / 2;
			}
		} else {
			$crop_width = $source_width;
			$crop_height = $source_width / $ratio;
			$source_y = ($source_height - $crop_height) / 2;

			if ($source_y < 0) {
				$crop_width = $source_height * $ratio;
				$crop_height = $source_height;

				$source_x = ($source_width - $crop_width) / 2;
				$source_y = 0;
			}
		}

		return $this->copyResampled((int) $width, (int) $height, (int) $crop_width, (int) $crop_height, (int)$source_x, (int)$source_y, (int) $width, (int) $height, 0, 0, false);
	}
}
