<?php
/**
* The Serializers Driver Interface
* @package Mars
*/

namespace Mars\Serializers;

/**
 * The Database Driver Interface
 */
interface DriverInterface
{
	/**
	 * Serializes data
	 * @param mixed $data The data to serialize
	 * @return string The serialized data
	 */
	public function serialize($data) : string;

	/**
	 * Unserializes data
	 * @param string $data The data to unserialize
	 * @return mixed The unserialized data
	 */
	public function unserialize(string $data);
}
