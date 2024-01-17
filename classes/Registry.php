<?php
/**
* The Registry Class
* @package Mars
*/

namespace Mars;

/**
 * The Registry Class
 * Stores/Retrives values
 */
class Registry
{
	use AppTrait;

	/**
	 * @var array $data Array storing the data
	 */
	protected array $data = [];

	/**
	 * Sets a registry value
	 * @param string $key The registry key
	 * @param mixed $data The data
	 * @return $this
	 */
	public function set($key, $data)
	{
		$this->data[$key] = $data;

		return $this;
	}

	/**
	 * Returns a registry value
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->data[$key] ?? null;
	}
}
