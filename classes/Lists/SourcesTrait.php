<?php
/**
* The Sources Trait
* @package Mars
*/

namespace Mars\Lists;

/**
* The Sources Trait
* Trait implementing the sources functionality
* Classes using this trait must set these properties:
* protected array $supported_sources = [];
*/
trait SourcesTrait
{

	/**
	* @var array $supported_sources The list of supported sources in the name => class format
	*/
	//protected array $supported_sources = [];

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
	* @param string $name The name of the source
	* @param string $class The class handling the source
	* @return static
	*/
	public function addSupportedSource(string $name, string $class) : static
	{
		$this->supported_sources[$name] = $class;

		$this->sources = [];

		return $this;
	}

	/**
	* Removes a source from the list of supported sources
	* @param string $name The name of the source
	* @return static
	*/
	public function removeSupportedSource(string $name) : static
	{
		if ($this->supported_sources[$name]) {
			unset($this->supported_sources[$name]);
		}

		$this->sources = [];

		return $this;
	}
}
