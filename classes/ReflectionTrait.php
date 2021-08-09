<?php
/**
* The Reflection Trait
* @package Mars
*/

namespace Mars;

/**
* The Reflection Trait
* Trait implementing reflection methods
*/
trait ReflectionTrait
{
	/**
	* Copies the properties from $properties
	* @param array $properties The list of properties to copy
	*/
	protected function copyProperties(array $properties)
	{
		foreach ($properties as $name => $prop) {
			$this->$name = $prop;
		}
	}

	/**
	* Copies the properties from $properties
	* @param array $properties The list of properties to copy
	*/
	protected function copyStaticProperties(array $properties)
	{
		foreach ($properties as $name => $prop) {
			static::$$name = $prop;
		}
	}

	/**
	* Checks if the properties exist and are not empty
	* @param array $properties The list of properties to check
	*/
	protected function propertiesExist(array $properties)
	{
		foreach ($properties as $prop) {
			if (empty($this->$prop)) {
				throw new \Exception("Property {$prop} must be set in class: " . static::class);
			}
		}
	}

	/**
	* Checks if the static properties exist and are not empty
	* @param array $properties The list of properties to check
	*/
	protected function staticPropertiesExist(array $properties)
	{
		foreach ($properties as $prop) {
			if (empty(static::$$prop)) {
				throw new \Exception("Property {$prop} must be set in class: " . static::class);
			}
		}
	}

	/**
	* Returns the name of the class
	* @param bool @with_namespace If true, will return the namespace
	* @return string The class name
	*/
	protected function getClassName(bool $with_namespace = true) : string
	{
		$rc = new \ReflectionClass(static::class);

		if ($with_namespace) {
			return $rc->name;
		} else {
			return $rc->getShortName();
		}
	}

	/**
	* Returns the name of the filename where the class is defined
	* @return string The filename
	*/
	protected function getClassFilename() : string
	{
		$rc = new \ReflectionClass(static::class);

		return $rc->getFileName();
	}

	/**
	* Returns the name of the namespace where the class is defined
	* @return string The namespace
	*/
	protected function getClassNamespace() : string
	{
		$rc = new \ReflectionClass(static::class);

		return $rc->getNamespaceName();
	}

	/**
	* Returns the path of the folder where the class is defined
	* @return string The path
	*/
	protected function getClassPath() : string
	{
		return App::sl(dirname($this->getClassFilename()));
	}
}
