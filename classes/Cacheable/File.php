<?php
/**
* The Cachable File Driver
* @package Mars
*/

namespace Mars\Cacheable;

/**
 * The Cachable File Driver
 * Driver which stores on disk the cached resources
 */
class File implements \Mars\Cacheable\DriverInterface
{
	use \Mars\AppTrait;

	/**
	 * @see \Mars\Cachable\DriverInterface::get()
	 * {@inheritdoc}
	 */
	public function get(string $filename) : string
	{
		return file_get_contents($filename);
	}

	/**
	 * @see \Mars\Cachable\DriverInterface::store()
	 * {@inheritdoc}
	 */
	public function store(string $filename, string $content) : bool
	{
		return file_put_contents($filename, $content);
	}

	/**
	 * @see \Mars\Cachable\DriverInterface::getLastModified()
	 * {@inheritdoc}
	 */
	public function getLastModified(string $filename) : int
	{
		if (!is_file($filename)) {
			return 0;
		}

		return filemtime($filename);
	}

	/**
	 * @see \Mars\Cachable\DriverInterface::delete()
	 * {@inheritdoc}
	 */
	public function delete(string $filename) : bool
	{
		if (is_file($filename)) {
			return unlink($filename);
		}

		return true;
	}
}
