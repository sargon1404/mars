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
	* @var bool $initialized Set to true, if the driver & handle have been set
	*/
	protected bool $initialized = false;

	/**
	* @var string $driver_namespace The driver's namespace
	*/
	protected string $driver_namespace = '\\Mars\\Response';

	/**
	* Builds the Response object
	* @param App $app The app object
	* @param string $driver The driver used to output the content
	*/
	public function __construct(App $app, string $driver = '')
	{
		$this->app = $app;

		if (!$driver) {
			$driver = $this->app->request->getResponse();
		}

		$this->driver = $driver;

		$this->init();
	}

	/**
	* Initializes the driver & handle
	*/
	protected function init()
	{
		if ($this->initialized) {
			return;
		}

		$this->driver = $this->getDriver();
		$this->handle = $this->getHandle();

		$this->initialized = true;
	}

	/**
	* Returns the name of the driver to use
	* @return string
	*/
	protected function getDriver() : string
	{
		switch ($this->driver) {
			case 'ajax':
			case 'json':
				return 'ajax';
			default:
				return 'html';
		}

		return $this->driver;
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
