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
	* Returns the name of the folder where the class is defined
	* @return string The dir
	*/
	protected function getClassDir() : string
	{
		return App::sl(dirname($this->getClassFilename()));
	}

	/**
	* Returns a prefix out of the class's name
	* @param string $suffix The suffix to append, if any
	* @return string The prefix
	*/
	protected function getPrefix(string $suffix = '') : string
	{
		$prefix = $this->prefix;

		if (!$prefix) {
			$prefix = str_replace("\\", '_', strtolower($this->getClassName()));
		}

		if ($suffix) {
			$prefix.= '_' . $suffix;
		}

		return $prefix;
	}
}
