<?php
/**
* The Templates Engine Class
* @package Mars
*/

namespace Mars;

use Mars\Templates\DriverInterface;

/**
* The Templates Engine Class
*/
class Templates
{
	use AppTrait;

	/**
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var DriverInterface $driver The driver object
	*/
	public readonly DriverInterface $driver;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'mars' => '\Mars\Templates\Mars'
	];

	/**
	* Constructs the templates object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'templates', $this->app);
		$this->driver = $this->drivers->get($this->app->config->templates_driver);
	}

	/**
	* Parses the template content and returns it
	* @param string $content The content to parse
	* @param array $params Params to pass to the parser
	* @return string The parsed content
	*/
	public function parse(string $content, array $params) : string
	{
		return $this->driver->parse($content, $params);
	}
}
