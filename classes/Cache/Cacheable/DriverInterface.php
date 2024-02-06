<?php
/**
* The Cacheable Driver Interface
* @package Mars
*/

namespace Mars\Cache\Cacheable;

/**
 * The Cacheable Driver Interface
 */
interface DriverInterface
{
	/**
	 * Returns the cached content of an asset
	 * @param string $filename The filename
	 * @return string The content
	 */
	public function get(string $filename) : string;

	/**
	 * Stores the content of an asset
	 * @param string $filename The filename
	 * @param string $content The content to store
	 * @return bool True on success, false on failure
	 */
	public function store(string $filename, string $content) : bool;

	/**
	 * Returns the timestamp when the asset was last modified
	 * @param string $filename The filename
	 * @return int The timestamp
	 */
	public function getLastModified(string $filename) : int;

	/**
	 * Deletes an asset
	 * @param string $filename The filename
	 * @return bool True on success, false on failure
	 */
	public function delete(string $filename) : bool;
}
