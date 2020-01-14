<?php
/**
* The Image Class
* @package Mars
*/

namespace Mars\Helpers;

use Mars\App;

/**
* The Image Class
* Used to convert images, process images [resize, cut..]
*/
class Image
{
	use \Mars\AppTrait;

	/**
	* @var int $jpeg_quality The quality of the jpeg images
	*/
	public int $jpeg_quality = 80;

	/**
	* @var int $png_quality The quality of the png images
	*/
	public int $png_quality	 = 6;

	/**
	* @var string background_color The background color of resized/cut images
	*/
	public string $background_color = 'ffffff';

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
	* @var array $supported_source_images The formats this class supports for source/input images
	*/
	protected array $supported_source_images = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

	/**
	* @var array $supported_destination_images The formats this class supports for destination/output images
	*/
	protected array $supported_destination_images = ['jpg', 'jpeg', 'png', 'gif'];

	/**
	* Processes an image
	* @param string $source_image The filename of the image to resize
	* @param string $destination_image The filename of the resized image
	* @param string $process_type The process type. resize|cut|cut_resize
	* @param int $image_width The width of the destination image. If 0, it will be computed based on the ratio
	* @param int $image_height The height of the destination image. If 0, it will be computed based on the ratio
	* @return bool
	*/
	public function processByType(string $source_image, string $destination_image, string $process_type, int $image_width, int $image_height) : bool
	{
		if ($process_type == 'cut_resize') {
			return $this->cutAndResize($source_image, $destination_image, $image_width, $image_height);
		} elseif ($process_type == 'cut') {
			return $this->cutCenter($source_image, $destination_image, $image_width, $image_height);
		} else {
			return $this->resize($source_image, $destination_image, $image_width, $image_height);
		}
	}

	/**
	* Resizes an image
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $destination_width The width of the destination image. If 0, it will be computed based on the ratio
	* @param int $destination_height The height of the destination image. If 0, it will be computed based on the ratio
	* @return bool
	*/
	public function resize(string $source_image, string $destination_image, int $destination_width, int $destination_height = 0) : bool
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		if (!$destination_width && !$destination_height) {
			return copy($source_image, $destination_image);
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		$width = 0;
		$height = 0;
		[$source_width, $source_height] = $size;

		if ($destination_width && $destination_height) {
			$section_width = 0;
			$section_height = 0;
			$offset_x = 0;
			$offset_y = 0;

			$ratio = $source_width / $source_height;
			$section_width = $destination_width;
			$section_height = $section_width / $ratio;

			if ($section_height > $destination_height || $section_width > $destination_width) {
				$section_height = $destination_height;
				$section_width = $section_height * $ratio;
			}

			$offset_x = ($destination_width - $section_width) / 2;
			$offset_y = ($destination_height - $section_height) / 2;

			return $this->processResize($source_image, $destination_image, $destination_width, $destination_height, $source_width, $source_height, $offset_x, $offset_y, $section_width, $section_height);
		} elseif ($destination_width) {
			$ratio = $source_width/$source_height;
			$width = $destination_width;
			$height = $destination_width / $ratio;
		} elseif ($destination_height) {
			$ratio = $source_width/$source_height;
			$width = $destination_height * $ratio;
			$height = $destination_height;
		}

		return $this->process($source_image, $destination_image, 0, 0, $source_width, $source_height, $width, $height, false);
	}

	/**
	* Crops and resizes an image
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $destination_width The width of the destination image. If 0, it will be computed based on the ratio
	* @param int $destination_height The height of the destination image. If 0, it will be computed based on the ratio
	* @return bool
	*/
	public function cutAndResize(string $source_image, string $destination_image, int $destination_width, int $destination_height = 0) : bool
	{
		return $this->cutAndResizeFrom($source_image, $destination_image, $destination_width, $destination_height);
	}

	/**
	* Crops and resizes an image
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $destination_width The width of the destination image. If 0, it will be computed based on the ratio
	* @param int $destination_height The height of the destination image. If 0, it will be computed based on the ratio
	* @param string $from The area from where to cut the image, if necesarilly: top|center|bottom
	* @return bool
	*/
	public function cutAndResizeFrom(string $source_image, string $destination_image, int $destination_width, int $destination_height = 0, string $from = 'center') : bool
	{
		if (!$destination_width || !$destination_height) {
			return $this->resize($source_image, $destination_image, $destination_width, $destination_height);
		}

		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		if (!$destination_width && !$destination_height) {
			return copy($image, $thumb);
		}

		$width = 0;
		$height = 0;
		$source_width = 0;
		$source_height = 0;

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		[$source_width, $source_height] = $size;

		$offset_x = 0;
		$offset_y = 0;
		$ratio = $destination_width / $destination_height;

		if ($source_width >= $source_height) {
			$width = $source_height * $ratio;
			$height = $source_height;
			$offset_x = ($source_width - $width) / 2;

			if ($offset_x < 0) {
				$width = $source_width;
				$height = $source_width / $ratio;

				$offset_x = 0;
				$offset_y = ($source_height - $height) / 2;
			}
		} else {
			$width = $source_width;
			$height = $source_width / $ratio;
			$offset_y = ($source_height - $height) / 2;

			if ($offset_y < 0) {
				$width = $source_height * $ratio;
				$height = $source_height;

				$offset_x = ($source_width - $width) / 2;
				$offset_y = 0;
			}
		}

		if ($from == 'top') {
			$offset_y = 0;
		}

		return $this->process($source_image, $destination_image, $offset_x, $offset_y, $width, $height, $destination_width, $destination_height);
	}

	/**
	* Crops and resizes an image starting from center
	* @see Image::cutAndResize()
	*/
	public function cutAndResizeFromCenter(string $source_image, string $destination_image, int $destination_width, int $destination_height = 0) : bool
	{
		return $this->cutAndResizeFrom($source_image, $destination_image, $destination_width, $destination_height, 'center');
	}

	/**
	* Crops and resizes an image starting from top
	* @see Image::cutAndResize()
	*/
	public function cutAndResizeFromTop(string $source_image, string $destination_image, int $destination_width, int $destination_height = 0) : bool
	{
		return $this->cutAndResizeFrom($source_image, $destination_image, $destination_width, $destination_height, 'top');
	}

	/**
	* Cuts a section from an image and, optionally, resize it.
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $cut_x The x point from where the cut should start
	* @param int $cut_y The y point from where the cut should start
	* @param int $cut_width The width of the cut section
	* @param int $cut_height The height of the cut section
	* @param int $destination_width The width of the destination image. If 0, it will be computed based on the ratio
	* @param int $destination_height The height of the destination image. If 0, it will be computed based on the ratio
	* @return bool
	*/
	public function cut(string $source_image, string $destination_image, int $cut_x, int $cut_y, int $cut_width = 0, int $cut_height = 0, int $destination_width = 0, int $destination_height = 0) : bool
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		$cw = 0;
		$ch = 0;
		$width = 0;
		$height = 0;
		[$source_width, $source_height] = $size;

		if ($cut_width) {
			$cw = $cut_width;
		} else {
			$cw = $source_width - $cut_x;
		}

		if ($cut_height) {
			$ch = $cut_height;
		} else {
			$ch = $source_height - $cut_y;
		}

		if ($cw > $source_width) {
			$cw = $source_width;
		}
		if ($ch > $source_height) {
			$ch = $source_height;
		}

		if (!$destination_width && !$destination_height) {
			$width = $cw;
			$height = $ch;
		} else {
			if ($destination_width && $destination_height) {
				$offset_x = 0;
				$offset_y = 0;
				$section_width = 0;
				$section_height = 0;

				$ratio = $cw / $ch;
				$section_width = $destination_width;
				$section_height = $section_width / $ratio;

				if ($section_height > $destination_height || $section_width > $destination_width) {
					$section_height = $destination_height;
					$section_width = $section_height * $ratio;
				}

				$offset_x = ($destination_width - $section_width) / 2;
				$offset_y = ($destination_height - $section_height) / 2;

				return $this->processResize($source_image, $destination_image, $destination_width, $destination_height, $cw, $ch, $offset_x, $offset_y, $section_width, $section_height, $cut_x, $cut_y);
			} elseif ($destination_width) {
				$ratio = $cw / $ch;
				$width = $destination_width;
				$height = $destination_width / $ratio;
			} elseif ($destination_height) {
				$ratio = $cw / $ch;
				$width = $destination_height * $ratio;
				$height = $destination_height;
			}
		}

		return $this->process($source_image, $destination_image, $cut_x, $cut_y, $cw, $ch, $width, $height);
	}

	/**
	* Cuts a section from an the source_image's center
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $cut_width The width of the area being cut
	* @param int $cut_height The height of the area being cut
	* @return bool
	*/
	public function cutCenter(string $source_image, string $destination_image, int $cut_width, int $cut_height = 0)
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		[$source_width, $source_height] = $size;

		$width = 0;
		$height = 0;
		$cut_x = 0;
		$cut_y = 0;

		if ($cut_width && $cut_height) {
			$width = $cut_width;
			$height = $cut_height;
		} elseif ($cut_width) {
			$ratio = $source_width / $source_height;
			$width = $cut_width;
			$height = $cut_width / $ratio;
			$cut_height = $height;
		} elseif ($cut_height) {
			$ratio = $source_width / $source_height;
			$width = $cut_height * $ratio;
			$height = $cut_height;
			$cut_width = $width;
		}

		$cut_x = ($source_width - $width) / 2;
		$cut_y = ($source_height - $height) / 2;

		if ($cut_x < 0 && $cut_y < 0) {
			$width = $source_width;
			$height = $source_height;

			return $this->processResize($source_image, $destination_image, $cut_width, $cut_height, $width, $height, abs($cut_x), abs($cut_y), $width, $height, 0, 0);
		}
		if ($cut_x < 0) {
			$width = $source_width;

			return $this->processResize($source_image, $destination_image, $cut_width, $cut_height, $width, $height, abs($cut_x), 0, $width, $height, 0, $cut_y);
		} elseif ($cut_y < 0) {
			$height = $source_height;

			return $this->processResize($source_image, $destination_image, $cut_width, $cut_height, $width, $height, 0, abs($cut_y), $width, $height, $cut_x, 0);
		}

		return $this->process($source_image, $destination_image, $cut_x, $cut_y, $width, $height, $width, $height);
	}

	/**
	* Converts an image from $source_image to $destination_image
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @return bool True on success, false on failure
	*/
	public function convert($source_image, $destination_image) : bool
	{
		if (!$this->areSupported($source_image, $destination_image)) {
			return false;
		}

		$size = $this->getSize($source_image);
		if (!$size) {
			return false;
		}

		[$source_width, $source_height] = $size;

		return $this->process($source_image, $destination_image, 0, 0, $source_width, $source_height, $source_width, $source_height, false);
	}

	/**
	* Returns the size of an image
	* @param string $filename The filename of the image
	* @return array The size of the image
	*/
	public function getSize(string $filename) : array
	{
		return getimagesize($filename);
	}

	/**
	* Returns the mime type of an image
	* @param string $filename The filename of the image
	* @return string The mime type
	*/
	public function getMimeType(string $filename) : string
	{
		$info = getimagesize($filename);
		if (!$info) {
			return '';
		}

		return $info['mime'];
	}

	/**
	* Checks if the source/destination images are supported, based on extension
	* @param string $source_image The filename of the source image
	* @param string $destination_image The filename of the destination_image
	* @return bool Returns true if both images are supported
	*/
	protected function areSupported($source_image, $destination_image) : bool
	{
		$s_ext = $this->app->file->getExtension($source_image);
		$d_ext = $this->app->file->getExtension($destination_image);

		if (!in_array($s_ext, $this->supported_source_images)) {
			return false;
		}
		if (!in_array($d_ext, $this->supported_destination_images)) {
			return false;
		}

		return true;
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
	* Opens an image and returns the resource
	* @param string $filename The filename to open
	* @param string $type The value will be written with the image's type
	* @param bool $alpha If true, will preserve the alpha for png images
	* @return resource
	*/
	protected function openImage(string $filename, ?string &$type, bool $alpha = true)
	{
		$type = '';
		$source = null;
		$ext = $this->app->file->getExtension($filename);

		switch ($ext) {
			case 'jpg':
			case 'jpeg':
				$source = imagecreatefromjpeg($filename);
				$type = 'jpg';
				break;
			case 'gif':
				$source = imagecreatefromgif($filename);
				$type = 'gif';
				break;
			case 'png':
				$source = imagecreatefrompng($filename);
				if ($source && $alpha) {
					imagealphablending($source, true);
					imagesavealpha($source, true);
				}
				$type = 'png';
				break;
			case 'bmp':
				$source = $this->imagecreatefrombmp($filename);
				$type = 'bmp';
				break;
		}

		if (!$source) {
			return false;
		}

		return $source;
	}

	/**
	* Creates an image of $width width and $height height based on $type
	* @param string $type The image's type
	* @param int $width The image's width
	* @param int $height The image's height
	* @param resource $source The image's source
	* @return resource
	*/
	protected function createImage(string $type, int $width, int $height, &$source)
	{
		$img = imagecreatetruecolor($width, $height);

		if ($type == 'gif') {
			$originaltransparentcolor = imagecolortransparent($source);

			if ($originaltransparentcolor >= 0 && $originaltransparentcolor < imagecolorstotal($source)) {
				$transparentcolor = imagecolorsforindex($source, $originaltransparentcolor);
				$newtransparentcolor = imagecolorallocate($img, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);

				imagefill($img, 0, 0, $newtransparentcolor);
				imagecolortransparent($img, $newtransparentcolor);
			}
		} elseif ($type == 'png') {
			imagealphablending($img, false);
			imagesavealpha($img, true);
		}

		return $img;
	}

	/**
	* Writes an image to disk
	* @param resource $img The image resource to write
	* @param string $filename The filename under which the image will be saved
	* @return bool
	*/
	protected function saveImage(&$img, string $filename) : bool
	{
		$ext = $this->app->file->getExtension($filename);

		switch ($ext) {
			case 'jpg':
			case 'jpeg':
				return imagejpeg($img, $filename, $this->jpeg_quality);
			case 'gif':
				return imagegif($img, $filename);
			case 'png':
				return imagepng($img, $filename, $this->png_quality);
			default:
				return false;
		}
	}

	/**
	* Performs a resize operation
	* @param string $source_image The source image
	* @param string $destination_image The destination image
	* @param int $destination_width The width of the destination image
	* @param int $destination_height The height of the destination image
	* @param int $source_width The width of the source image
	* @param int $source_height The height of the source image
	* @param int $offset_x The x point from where to start processing
	* @param int $offset_y The y point from where to start processing
	* @param int $section_width The width of section
	* @param int $section_height The height of section
	* @param int $offset_source_x The source's x point
	* @param int $offset_source_y The source's y point
	* @param bool $fill If true, will fill the image with the $this->background_color
	* @return bool
	*/
	protected function processResize(string $source_image, string $destination_image, int $destination_width, int $destination_height, int $source_width, int $source_height, int $offset_x, int $offset_y, int $section_width, int $section_height, int $offset_source_x = 0, int $offset_source_y = 0, bool $fill = true) : bool
	{
		$source = $this->openImage($source_image, $type);
		if (!$source) {
			return false;
		}

		$dest = $this->createImage($type, $destination_width, $destination_height, $source);

		//fill the image with the chosen background
		if ($fill) {
			$bc = $this->htmlToRgb($this->background_color);

			imagefill($dest, 0, 0, imagecolorallocate($dest, $bc[0], $bc[1], $bc[2]));
		}

		imagecopyresampled($dest, $source, $offset_x, $offset_y, $offset_source_x, $offset_source_y, $section_width, $section_height, $source_width, $source_height);

		$ret = $this->saveImage($dest, $destination_image);

		imagedestroy($source);
		imagedestroy($dest);

		return $ret;
	}

	/**
	* @see Image::processResize()
	*/
	protected function process(string $source_image, string $destination_image, int $cut_x, int $cut_y, int $cut_width, int $cut_height, int $destination_width, int $destination_height, bool $fill = true) :bool
	{
		return $this->processResize($source_image, $destination_image, $destination_width, $destination_height, $cut_width, $cut_height, 0, 0, $destination_width, $destination_height, $cut_x, $cut_y, $fill);
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

	/**
	* Creates an image from a bmp file
	* Code from http://php.net/manual/en/function.imagecreate.php posted by a user named DHKold - admin@dhkold.com
	* @param string $filename The filename
	* @return resource
	*/
	protected function imagecreatefrombmp(string $filename)
	{
		if (!$f1 = fopen($filename, 'rb')) {
			return false;
		}

		$file = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
		if ($file['file_type'] != 19778) {
			return false;
		}

		$bmp = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
							'/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
							'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));

		$bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
		if ($bmp['size_bitmap'] == 0) {
			$bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
		}

		$bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
		$bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
		$bmp['decal'] = ($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
		$bmp['decal']-= floor($bmp['width']*$bmp['bytes_per_pixel'] / 4);
		$bmp['decal'] = 4 - (4 * $bmp['decal']);
		if ($bmp['decal'] == 4) {
			$bmp['decal'] = 0;
		}

		$palette = [];
		if ($bmp['colors'] < 16777216) {
			$palette = unpack('V' . $bmp['colors'], fread($f1, $bmp['colors'] * 4));
		}


		$img = fread($f1, $bmp['size_bitmap']);
		$vide = chr(0);

		$res = imagecreatetruecolor($bmp['width'], $bmp['height']);
		$p = 0;
		$y = $bmp['height'] -1;

		while ($y >= 0) {
			$x = 0;
			while ($x < $bmp['width']) {
				if ($bmp['bits_per_pixel'] == 24) {
					$color = unpack("V", substr($img, $p, 3) . $vide);
				} elseif ($bmp['bits_per_pixel'] == 16) {
					$color = unpack("n", substr($img, $p, 2));
					$color[1] = $palette[$color[1] + 1];
				} elseif ($bmp['bits_per_pixel'] == 8) {
					$color = unpack("n", $vide . substr($img, $p, 1));
					$color[1] = $palette[$color[1] + 1];
				} elseif ($bmp['bits_per_pixel'] == 4) {
					$color = unpack("n", $vide . substr($img, floor($p), 1));
					if (($p * 2) %2  == 0) {
						$color[1] = ($color[1] >> 4);
					} else {
						$color[1] = ($color[1] & 0x0F);
					}

					$color[1] = $palette[$color[1] + 1];
				} elseif ($bmp['bits_per_pixel'] == 1) {
					$color = unpack("n", $vide . substr($img, floor($p), 1));
					if (($p * 8) % 8  == 0) {
						$color[1] = $color[1] >> 7;
					} elseif (($p * 8) % 8 == 1) {
						$color[1] = ($color[1] & 0x40) >> 6;
					} elseif (($p * 8) % 8 == 2) {
						$color[1] = ($color[1] & 0x20) >> 5;
					} elseif (($p * 8) % 8 == 3) {
						$color[1] = ($color[1] & 0x10) >> 4;
					} elseif (($p * 8) % 8 == 4) {
						$color[1] = ($color[1] & 0x8) >> 3;
					} elseif (($p * 8) % 8 == 5) {
						$color[1] = ($color[1] & 0x4) >> 2;
					} elseif (($p * 8) % 8 == 6) {
						$color[1] = ($color[1] & 0x2) >> 1;
					} elseif (($p * 8) % 8 == 7) {
						$color[1] = ($color[1] & 0x1);
					}

					$color[1] = $palette[$color[1] + 1];
				} else {
					return false;
				}

				imagesetpixel($res, $x, $y, $color[1]);
				$x++;
				$p+= $bmp['bytes_per_pixel'];
			}

			$y--;
			$p+= $bmp['decal'];
		}

		fclose($f1);

		return $res;
	}
}
