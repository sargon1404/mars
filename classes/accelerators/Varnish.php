<?php
/**
* The Varnish Accelerator Class
* @package Mars
*/

namespace Mars\Accelerators;

use Mars\App;
use Mars\Helpers\Curl;

/**
* The Varnish Accelerator Class
*/
class Varnish implements DriverInterface
{
	use \Mars\AppTrait;

	/**
	* @see \Mars\Accelerators\DriverInterface::delete()
	* {@inheritdoc}
	*/
	public function delete(string $url) : bool
	{
		$curl = new Curl;
		$curl->request($url, 'PURGE');

		return true;
	}

	/**
	* @see \Mars\Accelerators\DriverInterface::deleteByPattern()
	* {@inheritdoc}
	*/
	public function deleteByPattern(string $pattern) : bool
	{
		$curl = new Curl;
		$curl->addHeader('X-Ban-Pattern: ' . $pattern);
		$curl->request($this->app->site->url, 'BAN');

		return true;
	}

	/**
	* @see \Mars\Accelerators\DriverInterface::deleteAll()
	* {@inheritdoc}
	*/
	public function deleteAll() : bool
	{
		$curl = new Curl;
		$curl->request($this->app->site->url, 'FULLBAN');

		return true;
	}
}
