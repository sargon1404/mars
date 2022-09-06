<?php
/**
* The Resize Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
* The Resize Image Class
*/
class Resize extends Base
{
	/**
	* Resizes the image
	* @param int $width The width of the resized image
	* @param int $height The height of the resized image
	*/
	public function process(int $width, int $height)
	{
		[$source_width, $source_height] = $this->source->getSize();
		$ratio = $this->source->getRatio();

		$destination_x = 0;
		$destination_y = 0;
		$destination_width = $width;
		$destination_height = $height;

		if ($width && $height) {
			$destination_width = $width;
			$destination_height = $destination_width / $ratio;

			if ($destination_height > $height || $destination_width > $width) {
				$destination_width = $height * $ratio;
			} else {
				$destination_height = $width / $ratio;
			}

			$destination_x = ($width - $destination_width) / 2;
			$destination_y = ($height - $destination_height) / 2;
		} elseif ($destination_width) {
			$height = $width / $ratio;
			$destination_height = $height;
		} elseif ($destination_height) {
			$width = $height * $ratio;
			$destination_width = $width;
		}

		return $this->copyResampled((int) $width, (int) $height, (int) $source_width, (int) $source_height, 0, 0, (int) $destination_width, (int) $destination_height, (int) $destination_x, (int) $destination_y);
	}
}
