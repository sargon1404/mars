<?php
/**
* The Png Image Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;
use Mars\Images\Image;

/**
* The Png Image Class
*/
class Png extends Image implements DriverInterface
{

	/**
	* @var string $mime_type The image's mime type
	*/
	protected $mime_type = 'image/png';

	/**
	* @see \Mars\Images\DriverInterface::__construct()
	* {@inheritdoc}
	*/
	public function __construct(string $filename, App $app = null)
	{
		parent::__construct($filename, $app);

		$this->quality = $this->app->config->image_png_quality;
		$this->optimize_command = $this->app->config->image_png_optimize_command;
	}

	/**
	* @see \Mars\Images\DriverInterface::open()
	* {@inheritdoc}
	*/
	public function open() : GdImage
	{
		$img = imagecreatefrompng($this->filename);
		if (!$img) {
			throw new \Exception("Unable to open image {$this->filename}");
		}

		imagealphablending($img, true);
		imagesavealpha($img, true);

		return $img;
	}

	/**
	* @see \Mars\Images\DriverInterface::create()
	* {@inheritdoc}
	*/
	public function create(int $width, int $height, GdImage $source) : GdImage
	{
		$img = parent::create($width, $height);

		imagealphablending($img, false);
		imagesavealpha($img, true);

		return $img;
	}

	/**
	* @see \Mars\Images\DriverInterface::save()
	* {@inheritdoc}
	*/
	public function save(GdImage $img)
	{
		if (!imagepng($img, $this->filename, $this->quality)) {
			throw new \Exception("Unable to save image {$this->filename}");
		}
	}
}
