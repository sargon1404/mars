<?php
/**
* The Watermark Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

use Mars\Image;

/**
* The Watermark Operation Image Class
*/
class Watermark extends Base
{

   /**
	* Cuts a section from an image
	* @param int $cut_width The width of the cut section
	* @param int $cut_height The height of the cut section
	* @param int $cut_x The x point from where the cut should start
	* @param int $cut_y The y point from where the cut should start
	* @param int $width The width of the resulting image. If 0, the image will have the same width as $cut_width
	* @param int $height The height of the resulting image. If 0 the image will have the same height as $cut_height
	* @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	*/
	public function applyText(string $text, int $position)
	{
		[$source_width, $source_height] = $this->source->getSize();

		$source = $this->source->open();
		$destination = $this->destination->create($source_width, $source_height, $source);
		$options = $this->getOptions($this->destination->getOptions());

		imagecopy($destination, $source, 0, 0, 0, 0, $source_width, $source_height);

		if ($options['text_ttf']) {
			$font_size = imagettfbbox($options['text_size'], $options['text_angle'], $options['text_font'], $text);
			$text_width = $font_size[2] - $font_size[0];
			$text_height = $font_size[3] - $font_size[5];
		} else {
			$text_width = imagefontwidth($options['text_font']) * mb_strlen($text);
			$text_height = imagefontheight($options['text_font']);
		}

		$pos = $this->getPosition($text_width + 2 * $options['padding_left'], $text_height + 2 * $options['padding_top'], $source_width, $source_height, $options['margin_left'], $options['margin_top'], $position);

		//draw the background
		if ($options['background']) {
			$pos = $this->getPosition($text_width + 2 * $options['padding_left'], $text_height + 2 * $options['padding_top'], $source_width, $source_height, $options['margin_left'], $options['margin_top'], $position);
			$bc = $this->htmlToRgb($options['background']);

			imagefilledrectangle($destination, $pos[0], $pos[1], $pos[0] + $text_width + 2 * $options['padding_left'], $pos[1] + $text_height + 2 * $options['padding_top'], imagecolorallocatealpha($destination, $bc[0], $bc[1], $bc[2], $options['opacity']));
		}

		//draw the text
		$tc = $this->htmlToRgb($options['text_color']);
		if ($options['text_ttf']) {
			imagettftext($destination, $options['text_size'], $options['text_angle'], $pos[0] + $options['padding_left'], $pos[1] + $text_height + $options['padding_top'], imagecolorallocate($destination, $tc[0], $tc[1], $tc[2]), $options['text_font'], $text);
		} else {
			imagestring($destination, $options['text_font'], $pos[0] + $options['padding_left'], $pos[1] + $options['padding_top'], $text, imagecolorallocate($destination, $tc[0], $tc[1], $tc[2]));
		}

		$this->destination->save($destination);

		imagedestroy($source);
		imagedestroy($destination);
	}

	/**
	* Applies a watermark image
	* @param Image $watermark_image The watermark image
	* @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	*/
	public function applyImage(Image $watermark_image, int $position = 3)
	{
		[$source_width, $source_height] = $this->source->getSize();

		$size = $this->source->getSize();
		$watermark_size = $watermark_image->getSize();

		$source = $this->source->open();
		$destination = $this->destination->create($source_width, $source_height, $source);
		$watermark = $watermark_image->open();
		$options = $this->getOptions($this->destination->getOptions());

		$pos = $this->getPosition($watermark_size[0], $watermark_size[1], $size[0], $size[1], $options['margin_left'], $options['margin_top'], $position);

		imagecopy($destination, $source, 0, 0, 0, 0, $source_width, $source_height);
		imagecopymerge($destination, $watermark, $pos[0], $pos[1], 0, 0, $watermark_size[0], $watermark_size[1], $options['opacity']);

		$this->destination->save($destination);

		imagedestroy($source);
		imagedestroy($destination);
		imagedestroy($watermark);
	}

	/**
	* Sets the watermark options
	* @param array $options The options array
	* @return array The options
	*/
	protected function getOptions(array $options) : array
	{
		$options['background'] ??= $this->app->config->image_watermark_background;
		$options['opacity'] ??= $this->app->config->image_watermark_opacity;
		$options['text_ttf'] ??= $this->app->config->image_watermark_text_ttf;
		$options['text_color'] ??= $this->app->config->image_watermark_text_color;
		$options['text_font'] ??= $this->app->config->image_watermark_text_font;
		$options['text_size'] ??= $this->app->config->image_watermark_text_size;
		$options['text_angle'] ??= $this->app->config->image_watermark_text_angle;
		$options['padding_left'] ??= $this->app->config->image_watermark_padding_left;
		$options['padding_top'] ??= $this->app->config->image_watermark_padding_top;
		$options['margin_left'] ??= $this->app->config->image_watermark_margin_left;
		$options['margin_top'] ??= $this->app->config->image_watermark_margin_top;

		return $options;
	}

	/**
	* Computes the coordinates where the watermark should be placed
	* @param int $watermark_width The watermark's width
	* @param int $watermark_height The watermark's height
	* @param int $image_width The image's width
	* @param int $image_height The image's height
	* @param int $margin_left The left margin
	* @param int $margin_top The top margin
	* @param int $position The watermark's position
	* @return array The x,y position
	*/
	protected function getPosition(int $watermark_width, int $watermark_height, int $image_width, int $image_height, int $margin_left, int $margin_top, int $position) : array
	{
		$pos = [];

		switch ($position) {
			case 1:
				$pos = [$margin_left, $image_height - $margin_top - $watermark_height];
			break;
			case 2:
				$pos = [($image_width - 2 * $margin_left - $watermark_height) / 2, $image_height - $margin_top - $watermark_height];
			break;
			case 3:
				$pos = [$image_width - $margin_left - $watermark_width, $image_height - $margin_top - $watermark_height];
			break;
			case 4:
				$pos = [$margin_left, ($image_height - 2 * $margin_top - $watermark_height) / 2];
			break;
			case 5:
				$pos = [($image_width - 2 * $margin_left - $watermark_height) / 2, ($image_height - 2 * $margin_top - $watermark_height) / 2];
			break;
			case 6:
				$pos = [$image_width - $margin_left - $watermark_width, ($image_height - 2 * $margin_top - $watermark_height) / 2];
			break;
			case 7:
				$pos = [$margin_left, $margin_top];
			break;
			case 8:
				$pos = [($image_width - 2 * $margin_left - $watermark_height) / 2, $margin_top];
			break;
			case 9:
				$pos = [$image_width - $margin_left - $watermark_width, $margin_top];
			break;
			default:
				$pos = [$image_width - $margin_left - $watermark_width, $image_height - $margin_top - $watermark_height];
		}

		return $pos;
	}
}