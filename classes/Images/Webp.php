<?php
/**
* The Webp Image Class
* @package Mars
*/

namespace Mars\Images;

use Mars\App;
use Mars\Images\Image;
use GdImage;

/**
* The Webp Image Class
*/
class Webp extends Image implements DriverInterface
{

	/**
	* @var string $mime_type The image's mime type
	*/
	protected $mime_type = 'image/webp';

	/**
	* @see \Mars\Images\DriverInterface::__construct()
	* {@inheritdoc}
	*/
	public function __construct(string $filename, App $app = null)
	{
		parent::__construct($filename, $app);

		$this->quality = $this->app->config->image_webp_quality;
	}

	/**
	* @see \Mars\Images\DriverInterface::open()
	* {@inheritdoc}
	*/
	public function open() : GdImage
	{
		$img = imagecreatefromwebp($this->filename);
		if (!$img) {
			throw new \Exception("Unable to open image {$this->filename}");
		}

		return $img;
	}

	/**
	* @see \Mars\Images\DriverInterface::save()
	* {@inheritdoc}
	*/
	public function save(GdImage $img)
	{
		imagewebp($img, $this->filename, $this->quality);
	}
}
