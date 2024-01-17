<?php
/**
* The Base Image Operations Class
* @package Mars
*/

namespace Mars\Images\Operations;

use GdImage;
use Mars\App;
use Mars\Image;

/**
 * The Base Image Operations Class
 */
abstract class Base
{
	use \Mars\AppTrait;

	/**
	 * @var Image $source The source image
	 */
	protected Image $source;

	/**
	 * @var Image $destination The destination image
	 */
	protected Image $destination;

	/**
	 * Builds the Image Operations object
	 * @param Image $source The source image
	 * @param Image $destination The destination image
	 * @param App $app The app object
	 */
	public function __construct(Image $source, Image $destination, App $app)
	{
		$this->app = $app;
		$this->source = $source;
		$this->destination = $destination;
	}

	/**
	 * Converts a html color to rgb
	 * @param string $color The html color. Eg: #ff0000
	 * @return array The rgb color
	 */
	protected function htmlToRgb(string $color) : array
	{
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		$r = substr($color, 0, 2);
		$g = substr($color, 2, 2);
		$b = substr($color, 4, 2);

		return [hexdec($r), hexdec($g), hexdec($b)];
	}

	/**
	 * Copy and resize part of an image with resampling
	 * @param int $width The width of the new image
	 * @param int $height The height of the new image
	 * @param int $source_width Source width
	 * @param int $source_height Source height
	 * @param int $source_x x-coordinate of source point
	 * @param int $source_y y-coordinate of source point
	 * @param int $destination_width Destination width
	 * @param int $destination_height Destination height
	 * @param int $destination_x x-coordinate of destination point
	 * @param int $destination_y y-coordinate of destination point
	 * @param bool $fill If true, will fill the image with background
	 */
	protected function copyResampled(int $width, int $height, int $source_width, int $source_height, int $source_x, int $source_y, int $destination_width, int $destination_height, int $destination_x, int $destination_y, bool $fill = true)
	{
		$source = $this->source->open();
		$destination = $this->source->create($width, $height, $source);
		$options = $this->destination->getOptions();

		//fill the image with the chosen background
		if ($fill) {
			$bc = $this->htmlToRgb($options['background_color'] ?? $this->app->config->image_background_color);

			imagefill($destination, 0, 0, imagecolorallocate($destination, $bc[0], $bc[1], $bc[2]));
		}

		imagecopyresampled($destination, $source, $destination_x, $destination_y, $source_x, $source_y, $destination_width, $destination_height, $source_width, $source_height);

		$this->destination->save($destination);

		imagedestroy($source);
		imagedestroy($destination);
	}
}
