<?php
/**
* The Image Base Class
* @package Mars
*/

namespace Mars\Images;

use GdImage;
use Mars\App;
use Mars\Images\Operations\Resize;

/**
 * The Image Base Class
 */
abstract class Image
{
	use \Mars\AppTrait;

	/**
	 * @var string $filename The image's filename
	 */
	protected string $filename = '';

	/**
	 * @var string $mime_type The image's mime type
	 */
	protected $mime_type = '';

	/**
	 * @var int $quality The image quality of the resulting images
	 */
	protected int $quality = 80;

	/**
	 * @var bool $optimize If true, the images will be optimized when processed/uploaded
	 */
	protected bool $optimize = false;

	/**
	 * @var string $optimize_command The optimization command
	 */
	protected string $optimize_command = '';

	/**
	 * @see \Mars\Images\DriverInterface::__construct()
	 * {@inheritdoc}
	 */
	public function __construct(string $filename, App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->filename = $filename;
		$this->optimize = $this->app->config->image_optimize;
	}

	/**
	 * @see \Mars\Images\DriverInterface::isValid()
	 * {@inheritdoc}
	 */
	public function isValid() : bool
	{
		$finfo = \finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = \finfo_file($finfo, $this->filename);
		finfo_close($finfo);

		return $mime_type == $this->mime_type;
	}

	/**
	 * @see \Mars\Images\DriverInterface::getSize()
	 * {@inheritdoc}
	 */
	public function getSize() : array
	{
		$info = getimagesize($this->filename);
		if (!$info) {
			throw new \Exception("Unable to get the size of image: {$this->filename}");
		}

		return [$info[0], $info[1]];
	}

	/**
	 * @see \Mars\Images\DriverInterface::getWidth()
	 * {@inheritdoc}
	 */
	public function getWidth(): int
	{
		$info = getimagesize($this->filename);
		if (!$info) {
			throw new \Exception("Unable to get the size of image: {$this->filename}");
		}

		return $info[0];
	}

	/**
	 * @see \Mars\Images\DriverInterface::getHeight()
	 * {@inheritdoc}
	 */
	public function getHeight(): int
	{
		$info = getimagesize($this->filename);
		if (!$info) {
			throw new \Exception("Unable to get the size of image: {$this->filename}");
		}

		return $info[1];
	}

	/**
	 * @see \Mars\Images\DriverInterface::getRatio()
	 * {@inheritdoc}
	 */
	public function getRatio() : float
	{
		[$width, $height] = $this->getSize();

		return $width / $height;
	}

	/**
	 * @see \Mars\Images\DriverInterface::create()
	 * {@inheritdoc}
	 */
	public function create(int $width, int $height, GdImage $source) : GdImage
	{
		return imagecreatetruecolor($width, $height);
	}

	/**
	 * @see \Mars\Images\DriverInterface::optimize()
	 * {@inheritdoc}
	 */
	public function optimize() : bool
	{
		if (!$this->optimize || !$this->optimize_command) {
			return false;
		}

		$filename = escapeshellarg($this->filename);
		$command = str_replace('{FILENAME}', $filename, $this->optimize_command);

		exec($command, $output, $result_code);

		if ($result_code == 0) {
			return true;
		}

		return false;
	}
}
