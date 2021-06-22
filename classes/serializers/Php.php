<?php
/**
* The PHP Serializer Class
* @package Mars
*/

namespace Mars\Serializers;

/**
* The PHP Driver Interface
*/
class Php implements DriverInterface
{
	/**
	* @see \Mars\Serializers\DriverInterface::serialize()
	* {@inheritdoc}
	*/
	public function serialize($data) : string
	{
		return \serialize($data);
	}

	/**
	* @see \Mars\Serializers\DriverInterface::unserialize()
	* {@inheritdoc}
	*/
	public function unserialize(string $data)
	{
		return \unserialize($data);
	}
}
