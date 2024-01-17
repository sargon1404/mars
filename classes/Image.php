<?php
/**
* The Image Class
* @package Mars
*/

namespace Mars;

use GdImage;
use Mars\Images\DriverInterface;

/**
 * The Image Class
 */
class Image
{
	use AppTrait;

	/**
	 * @var Drivers $drivers The drivers object
	 */
	public readonly Drivers $drivers;

	/**
	 * @var Handlers $operations The operations handlers
	 */
	public readonly Handlers $operations;

	/**
	 * @var DriverInterface $driver The driver object
	 */
	protected DriverInterface $driver;

	/**
	 * @var string $filename The image's filename
	 */
	protected string $filename = '';

	/**
	 * @var array $options The image's options
	 */
	protected array $options = [];

	/**
	 * @var array $supported_drivers The supported drivers
	 */
	protected array $supported_drivers = [
		'jpg' => '\Mars\Images\Jpg',
		'jpeg' => '\Mars\Images\Jpg',
		'png' => '\Mars\Images\Png',
		'gif' => '\Mars\Images\Gif',
		'webp' => '\Mars\Images\Webp',
	];

	/**
	 * @var array $supported_operations The list of supported operations
	 */
	protected array $supported_operations = [
		'resize' => '\Mars\Images\Operations\Resize',
		'crop' => '\Mars\Images\Operations\Crop',
		'cut' => '\Mars\Images\Operations\Cut',
		'convert' => '\Mars\Images\Operations\Convert',
		'watermark' => '\Mars\Images\Operations\Watermark'
	];

	/**
	 * Constructs the image object
	 * @param string $filename The image's filename
	 * @param array $options The image's options
	 * @param App $app The app object
	 */
	public function __construct(string $filename, array $options = [], App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->filename = $filename;
		$this->options = $options;
		$ext = $this->app->file->getExtension($this->filename);

		$this->app->plugins->run('image_construct', $filename, $this);

		if (!$ext) {
			throw new \Exception("Invalid image {$filename}");
		}

		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, '', $this->app);
		$this->driver = $this->drivers->get($ext, $this->filename);
		$this->operations = new Handlers($this->supported_operations, $this->app);
		$this->operations->setStore(false);
	}

	/**
	 * Returns the filename
	 * @return string
	 */
	public function getFilename() : string
	{
		return $this->filename;
	}

	/**
	 * Returns the options
	 * @return array
	 */
	public function getOptions() : array
	{
		return $this->options;
	}

	/**
	 * Determines if the image is valid
	 * @return bool
	 */
	public function isValid() : bool
	{
		return $this->driver->isValid();
	}

	/**
	 * Returns the size (width/height) of the image
	 * @return array
	 */
	public function getSize() : array
	{
		return $this->driver->getSize();
	}

	/**
	 * Returns the width of the image
	 * @return int
	 */
	public function getWidth(): int
	{
		return $this->driver->getWidth();
	}

	/**
	 * Returns the height of the image
	 * @return int
	 */
	public function getHeight(): int
	{
		return $this->driver->getHeight();
	}

	/**
	 * Returns the radio between width and height
	 * @return float
	 */
	public function getRatio() : float
	{
		return $this->driver->getRatio();
	}

	/**
	 * Opens the file as a GdImage
	 * @return GdImage
	 */
	public function open() : GdImage
	{
		return $this->driver->open();
	}

	/**
	 * Creates a GdImage object
	 * @param int $width The image's width
	 * @param int $height The image's height
	 * @return GdImage
	 */
	public function create(int $width, int $height, GdImage $source) : GdImage
	{
		return $this->driver->create($width, $height, $source);
	}

	/**
	 * Saves a GdImage object
	 * @return GdImage
	 */
	public function save(GdImage $img)
	{
		return $this->driver->save($img);
	}

	/**
	 * Optimizes the image
	 * @return bool Returns true, if the image was optimized
	 */
	public function optimize() : bool
	{
		return $this->driver->optimize();
	}

	/**
	 * Copies the current image and returns an Image object from the copied filename
	 * @param string $filename The destination filename
	 * @throws Exception If the copy operation fails
	 */
	public function copy(string $filename) : Image
	{
		$image = new static($filename, $this->app);

		$this->app->file->copy($this->filename, $filename);

		return $image;
	}

	/**
	 * Resizes the image
	 * @param string $destination The destination's filename
	 * @param int $width The width of the resized image. If 0, it will be computed based on the ratio
	 * @param int $height The height of the resized image. If 0, it will be computed based on the ratio
	 * @param array $options The destination's options
	 * @return Image The resized image
	 */
	public function resize(string $destination, int $width, int $height = 0, array $options = []) : Image
	{
		$destination = new static($destination, $options);

		$operation = $this->operations->get('resize', $this, $destination, $this->app);
		$operation->process($width, $height);

		return $destination;
	}

	/**
	 * Crops the image
	 * @param string $destination The destination's filename
	 * @param int $width The width of the cropped image
	 * @param int $height The height of the cropped image
	 * @param array $options The crop's options
	 * @return Image The cropped image
	 */
	public function crop(string $destination, int $width, int $height, array $options = []) : Image
	{
		$destination = new static($destination, $options);

		$operation = $this->operations->get('crop', $this, $destination, $this->app);
		$operation->process($width, $height);

		return $destination;
	}

	/**
	 * Cuts a section of the image and saves it to destination
	 * @param string $destination The destination's filename
	 * @param int $cut_width The width of the cut section
	 * @param int $cut_height The height of the cut section
	 * @param int $cut_x The x point from where the cut should start
	 * @param int $cut_y The y point from where the cut should start
	 * @param int $width The width of the resulting image. If 0, the image will have the same width as $cut_width
	 * @param int $height The height of the resulting image. If 0 the image will have the same height as $cut_height
	 * @param array $options The destination's options
	 * @return Image The cut image
	 */
	public function cut(string $destination, int $cut_width, int $cut_height, int $cut_x = 0, int $cut_y = 0, int $width = 0, int $height = 0, array $options = []) : Image
	{
		$destination = new static($destination, $options);

		$operation = $this->operations->get('cut', $this, $destination, $this->app);
		$operation->process($cut_width, $cut_height, $cut_x, $cut_y, $width, $height);

		return $destination;
	}

	/**
	 * Converts an image to another format
	 * @param string $destination The destination's filename
	 * @return Image The converted image
	 */
	public function convert(string $destination) : Image
	{
		$destination = new static($destination);

		$operation = $this->operations->get('convert', $this, $destination, $this->app);
		$operation->process();

		return $destination;
	}

	/**
	 * Places a watermark text over an image
	 * @param string $destination The destination's filename
	 * @param string $text The text to place as watermark
	 * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	 * @return Image The converted image
	 */
	public function watermarkText(string $destination, string $text, int $position = 3, array $options = []) : Image
	{
		$destination = new static($destination, $options);

		$operation = $this->operations->get('watermark', $this, $destination, $this->app);
		$operation->applyText($text, $position);

		return $destination;
	}

	/**
	 * Places a watermark text over an image
	 * @param string $destination The destination's filename
	 * @param string $watermark_image The path of the image which will be used as a watermark
	 * @param int $position The position of the watermark text. Matches the 1-9 keys of the numpad. 1:bottom-left; 5:middle center; 9:top-right
	 * @return Image The converted image
	 */
	public function watermarkImage(string $destination, string $watermark_image, int $position = 3, array $options = []) : Image
	{
		$destination = new static($destination, $options);
		$watermark = new static($watermark_image);

		$operation = $this->operations->get('watermark', $this, $destination, $this->app);
		$operation->applyImage($watermark, $position);

		return $destination;
	}
}
