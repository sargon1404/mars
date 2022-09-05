<?php
/**
* The Jpeg Image Class
* @package Mars
*/

namespace Mars\Images;

use Mars\App;
use Mars\Images\Image;
use GdImage;

/**
* The Jpeg Image Class
*/
class Jpg extends Image implements DriverInterface
{

	/**
	* @var string $mime_type The image's mime type
	*/
	protected $mime_type = 'image/jpeg';

	/**
	* @see \Mars\Images\DriverInterface::__construct()
	* {@inheritdoc}
	*/
	public function __construct(string $filename, App $app = null)
	{
		parent::__construct($filename, $app);

		$this->quality = $this->app->config->image_jpg_quality;
		$this->optimize_command = $this->app->config->image_jpg_optimize_command;
	}

	/**
	* @see \Mars\Images\DriverInterface::optimize()
	* {@inheritdoc}
	*/
	public function optimize() : bool
	{
		$ret = parent::optimize();

		//jpegoptim will reset the file's permissions. Set it to 744
		chmod($this->filename, 0744);

		return $ret;
	}

	/**
	* @see \Mars\Images\DriverInterface::open()
	* {@inheritdoc}
	*/
	public function open() : GdImage
	{
		$img = imagecreatefromjpeg($this->filename);
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
		if (!imagejpeg($img, $this->filename, $this->quality)) {
			throw new \Exception("Unable to save image {$this->filename}");
		}
	}
}
