<?php
/**
* The Varnish Accelerator Class
* @package Mars
*/

namespace Mars\Accelerators;

use Mars\App;
use Mars\Http\Request;

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
		$req = new Request($url);
		$response = $req->custom('PURGE');

		return $response->ok();
	}

	/**
	* @see \Mars\Accelerators\DriverInterface::deleteByPattern()
	* {@inheritdoc}
	*/
	public function deleteByPattern(string $pattern) : bool
	{
		$req = new Request($this->app->url);
		$req->addHeader('X-Ban-Pattern: ' . $pattern);
		$response = $req->custom('BAN');

		return $response->ok();
	}

	/**
	* @see \Mars\Accelerators\DriverInterface::deleteAll()
	* {@inheritdoc}
	*/
	public function deleteAll() : bool
	{
		$req = new Request($this->app->url);
		$response = $req->custom('FULLBAN');

		return $response->ok();
	}
}
