<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars;

use Mars\Response\Types\DriverInterface;

/**
* The Response Class
* Outputs the system's html/ajax response
*/
class Response
{
	use AppTrait;

	/**
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var string $type The response's type
	*/
	public readonly string $type;

	/**
	* @var DriverInterface $driver The driver object
	*/
	protected DriverInterface $driver;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'ajax' => '\Mars\Response\Types\Ajax',
		'html' => '\Mars\Response\Types\Html'
	];

	/**
	* Builds the Response object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->type = $this->getType();
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'response', $this->app);
		$this->driver = $this->drivers->get($this->type);
		$this->headers = new \Mars\Response\Headers($this->app);
		$this->cookies = new \Mars\Response\Cookies($this->app);
	}

	/**
	* Returns the type of the respponse to send
	* @return string
	*/
	protected function getType() : string
	{
		$driver = $this->app->request->getType();

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
		if ($this->type == 'ajax') {
			return true;
		}

		return false;
	}

	/**
	* Returns true if the request should be processed with ajax
	*/
	public function isHtml() : bool
	{
		if ($this->type == 'html') {
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
		$this->driver->output($content, $data);
	}
}
