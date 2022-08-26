<?php
/**
* The Watermark Operation Image Class
* @package Mars
*/

namespace Mars\Images\Operations;

/**
* The Watermark Operation Image Class
*/
class Watermark extends Base
{
		/**
	* @var string $watermark_font The font used to draw the watermark text
	*/
	public string $watermark_font = '5';

	/**
	* @var string $watermark_font_ttf If true, will use ttf as a font
	*/
	public bool $watermark_font_ttf = false;

	/**
	* @var string $watermark_text_color The color of the watermark text
	*/
	public string $watermark_text_color = 'ff0000';

	/**
	* @var int $watermark_text_size The size of the watermark text
	*/
	public string $watermark_text_size = '20';

	/**
	* @var string $watermark_text_angle The angle of the watermark text
	*/
	public string $watermark_text_angle = '0';

	/**
	* @var string $watermark_background The color of the watermark's background
	*/
	public string $watermark_background = '000000';

	/**
	* @var string $watermark_opacity The opacity of the watermark
	*/
	public string $watermark_opacity = '40';

	/**
	* @var string $watermark_padding_top The top/bottom padding of the watermark text
	*/
	public string $watermark_padding_top = '10';

	/**
	* @var string $watermark_padding_left The left/right padding of the watermark text
	*/
	public string $watermark_padding_left = '15';

	/**
	* @var string $watermark_margin_top The top/bottom margin of the watermark text
	*/
	public string $watermark_margin_top = '20';

	/**
	* @var string $watermark_margin_left The left/right margin of the watermark text
	*/
	public string $watermark_margin_left = '30';

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

	}

	/**
	* Applies a watermark image over $source_image and saves it as $destination_image
	* @param string $source_image The filename of the source image
	* @param string $destination_image The filename of the destination_image
	* @param string $watermark_image The filename of the image which will be used as a watermark
	* @param int $position The position of the watermark. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	* @return bool
	*/
	public function watermarkImage(string $source_image, string $destination_image, string $watermark_image, int $position = 3)
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		$source = $this->openImage($source_image, $type, false);
		if (!$source) {
			return false;
		}

		if ($type == 'png') {
			imagealphablending($source, true);
		}

		$w_size = $this->getSize($watermark_image);
		if (!$w_size) {
			imagedestroy($source);
			return false;
		}

		$watermark = $this->openImage($watermark_image, $type2, false);
		if (!$watermark) {
			imagedestroy($source);
			return false;
		}

		$pos = $this->getWatermarkPosition($w_size[0], $w_size[1], $size[0], $size[1], $position);

		imagecopymerge($source, $watermark, $pos[0], $pos[1], 0, 0, $w_size[0], $w_size[1], $this->watermark_opacity);

		$ret = $this->saveImage($source, $destination_image);

		imagedestroy($source);
		imagedestroy($watermark);

		return $ret;
	}

	/**
	* Applies watermark text over $source_image and saves it as $destination_image
	* @param string $source_image The filename of the source image
	* @param string $destination_image The filename of the destination_image
	* @param string $text The watermark text
	* @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	* @return bool
	*/
	public function watermarkText(string $source_image, string $destination_image, string $text, int $position = 3)
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		[$width, $height] = $size;

		$source = $this->openImage($source_image, $type);
		if (!$source) {
			return false;
		}

		$dest = $this->createImage($type, $width, $height, $source);

		imagecopy($dest, $source, 0, 0, 0, 0, $width, $height);

		if ($this->watermark_font_ttf) {
			$font_size = imagettfbbox($this->watermark_text_size, $this->watermark_text_angle, $this->watermark_font, $text);
			$text_width = $font_size[2] - $font_size[0];
			$text_height = $font_size[3] - $font_size[5];
		} else {
			$chars = strlen($text);
			$text_width = imagefontwidth($this->watermark_font) * $chars;
			$text_height = imagefontheight($this->watermark_font);
		}

		if ($this->watermark_background) {
			$pos = $this->getWatermarkPosition($text_width + 2 * $this->watermark_padding_left, $text_height + 2 * $this->watermark_padding_top, $width, $height, $position);
			$bc = $this->htmlToRgb($this->watermark_background);
			$tc = $this->htmlToRgb($this->watermark_text_color);

			imagefilledrectangle($dest, $pos[0], $pos[1], $pos[0] + $text_width + 2 * $this->watermark_padding_left, $pos[1] + $text_height + 2 * $this->watermark_padding_top, imagecolorallocate($dest, $bc[0], $bc[1], $bc[2]));

			if ($this->watermark_font_ttf) {
				imagettftext($dest, $this->watermark_text_size, $this->watermark_text_angle, $pos[0] + $this->watermark_padding_left, $pos[1] + $text_height + $this->watermark_padding_top, imagecolorallocate($dest, $tc[0], $tc[1], $tc[2]), $this->watermark_font, $text);
			} else {
				imagestring($dest, $this->watermark_font, $pos[0] + $this->watermark_padding_left, $pos[1] + $this->watermark_padding_top, $text, imagecolorallocate($dest, $tc[0], $tc[1], $tc[2]));
			}
		} else {
			$pos = $this->getWatermarkPosition($text_width, $text_height, $width, $height, $position);
			$tc = $this->htmlToRgb($this->watermark_text_color);

			if ($this->watermark_font_ttf) {
				imagettftext($dest, $this->watermark_text_size, $this->watermark_text_angle, $pos[0], $pos[1] + $text_height, imagecolorallocate($dest, $tc[0], $tc[1], $tc[2]), $this->watermark_font, $text);
			} else {
				imagestring($dest, $this->watermark_font, $pos[0], $pos[1] + $this->watermark_padding_top, $text, imagecolorallocate($dest, $tc[0], $tc[1], $tc[2]));
			}
		}

		$ret = $this->saveImage($dest, $destination_image);

		imagedestroy($source);
		imagedestroy($dest);

		return $ret;
	}

	/**
	* Computes the coordinates where the watermark should be placed
	* @param int $width The watermark's width
	* @param int $height The watermark's height
	* @param int $image_width The image's width
	* @param int $image_height The image's height
	* @param int $position The watermark's position
	* @return array The x,y position
	*/
	protected function getWatermarkPosition(int $width, int $height, int $image_width, int $image_height, int $position) : array
	{
		$pos = [];

		switch ($position) {
			case 1:
				$pos = [$this->watermark_margin_left, $image_height - $this->watermark_margin_top - $height];
			break;
			case 2:
				$pos = [($image_width - 2 * $this->watermark_margin_left - $height) / 2, $image_height - $this->watermark_margin_top - $height];
			break;
			case 3:
				$pos = [$image_width - $this->watermark_margin_left - $width, $image_height - $this->watermark_margin_top - $height];
			break;
			case 4:
				$pos = [$this->watermark_margin_left, ($image_height - 2 * $this->watermark_margin_top - $height) / 2];
			break;
			case 5:
				$pos = [($image_width - 2 * $this->watermark_margin_left - $height) / 2, ($image_height - 2 * $this->watermark_margin_top - $height) / 2];
			break;
			case 6:
				$pos = [$image_width - $this->watermark_margin_left - $width, ($image_height - 2 * $this->watermark_margin_top - $height) / 2];
			break;
			case 7:
				$pos = [$this->watermark_margin_left, $this->watermark_margin_top];
			break;
			case 8:
				$pos = [($image_width - 2 * $this->watermark_margin_left - $height) / 2, $this->watermark_margin_top];
			break;
			case 9:
				$pos = [$image_width - $this->watermark_margin_left - $width, $this->watermark_margin_top];
			break;
		}

		return $pos;
	}
}