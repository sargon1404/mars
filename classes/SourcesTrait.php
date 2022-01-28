<?php
/**
* The Sources Trait
* @package Mars
*/

namespace Mars;

/**
* The Sources Trait
* Trait implementing the sources functionality
*/
trait SourcesTrait
{

	/**
	* @var array $sources Array listing the source objects
	*/
	protected array $sources = [];

	/**
	* Returns the source objects
	*/
	public function getSources() : array
	{
		if (!$this->sources) {
			foreach ($this->supported_sources as $class_name) {
				$this->sources[] = new $class_name($this->app);
			}
		}

		return $this->sources;
	}

	/**
	* Adds a source to the list of supported sources
	* @param string $name The name of the driver
	* @param string $class The class handling the driver
	* @return static
	*/
	public function addSource(string $name, string $class) : static
	{
		$this->supported_sources[$name] = $class;

		return $this;
	}

	/**
	* Adds multiple sources to the list of supported sources
	* @param array $sources The sources to add
	* @return static
	*/
	public function addSupportedSources(array $sources) : static
	{
		$this->supported_sources = array_merge($this->supported_sources, $sources);

		return $this;
	}
}
