<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars;

use Mars\Response\DriverInterface;

/**
* The Response Class
* Outputs the system's html/ajax response
*/
class Response
{
	use AppTrait;
	use DriverTrait;

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'response';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Mars\Response\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'ajax' => '\Mars\Response\Ajax',
		'html' => '\Mars\Response\Html'
	];

	/**
	* Builds the Response object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->driver = $this->getDriver();

		$this->handle = $this->getHandle();
	}

	/**
	* Returns the name of the driver to use
	* @return string
	*/
	protected function getDriver() : string
	{
		$driver = $this->app->request->getResponse();

		switch ($driver) {
			case 'ajax':
			case 'json':
				return 'ajax';
			default:
				return 'html';
		}
	}

	/**
	* Returns true if the request should be processed with ajax/json
	*/
	public function isAjax() : bool
	{
		if ($this->driver == 'ajax') {
			return true;
		}

		return false;
	}

	/**
	* Returns true if the request should be processed with ajax
	*/
	public function isHtml() : bool
	{
		if ($this->driver == 'html') {
			return true;
		}

		return false;
	}

	/**
	* Outputs the $content
	* @param string $content The content to output
	* @param array $data Data to output, if any
	*/
	public function output(string $content = '', array $data = [])
	{
		$this->handle->output($content, $data);
	}
}
