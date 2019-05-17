<?php
/**
* The Image Optimizer Class
* @package Mars
*/

namespace Mars\Helpers;

use Mars\App;

/**
* The Image Optimizer Class
* Optimizes images using: png: imagemagick(convert); jpg: jpegoptim; gif: gifsicle
*/
class ImageOptimizer
{
	use \Mars\AppTrait;

	/**
	* @var int $jpeg_quality The quality of the jpeg images
	*/
	public $jpeg_quality = 80;

	/**
	* @var array $supported_source_images The formats this class supports
	*/
	protected $supported_source_images = ['jpg', 'jpeg', 'png', 'gif'];

	/**
	* Optimizes an image
	* @param string $filename The filename of the image to optimize
	* @return bool False on error
	*/
	public function optimize(string $filename) : bool
	{
		$ext = $this->app->file->getExtension($filename);
		if (!in_array($ext, $this->supported_source_images)) {
			return false;
		}

		switch ($ext) {
			case 'jpg':
			case 'jpeg':
				return $this->optimizeJpeg($filename);
				break;
			case 'gif':
				return $this->optimizeGif($filename);
				break;
			case 'png':
				return $this->optimizePng($filename);
				break;
			default:
				return false;
		}
	}

	/**
	* Optimizes a jpeg image using jpegoptim
	* @param string $filename The filename
	* @return bool
	*/
	protected function optimizeJpeg(string $filename) : bool
	{
		$quality = (int)$this->jpeg_quality;
		$safe_filename = escapeshellarg($filename);

		$command = "jpegoptim {$safe_filename} --strip-all -m {$quality}";

		exec($command);

		//jpegoptim will reset the file's permissions. Set it to 777
		chmod($filename, 0777);

		return true;
	}

	/**
	* Optimizes a png image using convert
	* @param string $filename The filename
	* @return bool
	*/
	protected function optimizePng(string $filename) : bool
	{
		$safe_filename = escapeshellarg($filename);

		$command = "convert {$safe_filename} -strip {$safe_filename}";

		exec($command, $o);

		return true;
	}

	/**
	* Optimizes a gif image using gifsicle
	* @param string $filename The filename
	* @return bool
	*/
	protected function optimizeGif(string $filename) : bool
	{
		$new_filename = $this->app->file->appendToFilename($filename, 'new');

		$command = 'gifsicle ' . escapeshellarg($filename) . ' > ' . escapeshellarg($new_filename);

		exec($command);

		$this->switchFiles($filename, $new_filename);

		return true;
	}

	/**
	* Switches between the original file and the new one
	* @param string $filename The filename
	* @param string $new_filename The new filename
	*/
	protected function switchFiles(string $filename, string $new_filename)
	{
		if (is_file($new_filename)) {
			unlink($filename);
			rename($new_filename, $filename);
		}
	}
}
