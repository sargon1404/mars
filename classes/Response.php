<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars;

use Mars\Response\Types\DriverInterface;
use Mars\Response\Cookies;
use Mars\Response\Headers;
use Mars\Response\Push;

/**
 * The Response Class
 * Outputs the system's html/ajax response
 */
class Response
{
	use AppTrait;

	/**
	 * @var Handlers $responses The $responses object
	 */
	public readonly Handlers $responses;

	/**
	 * @var Cookies $cookies The cookies object
	 */
	public Cookies $cookies;

	/**
	 * @var Headers $headers The headers object
	 */
	public Headers $headers;

	/**
	 * @var Push $push The server push object
	 */
	public Push $push;
	
	/**
	 * @var string $type The response type
	 */
	protected string $type = 'html';

	/**
	 * @var DriverInterface $driver The driver object
	 */
	protected DriverInterface $driver;

	/**
	 * @var array $supported_$responses The supported $responses types
	 */
	protected array $supported_responses = [
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
		$this->responses = new Handlers($this->supported_responses, $this->app);
		$this->responses->setInterface(DriverInterface::class);
		$this->headers = new Headers($this->app);
		$this->cookies = new Cookies($this->app);
		$this->push = new Push($this->app);
	}
	
	/**
	 * Returns the type of the response to send
	 * @return string
	 */
	public function getType() : string
	{
		return $this->type;
	}

	/**
	 * Sets the type of the response to send
	 * @param string $type The type
	 * @return static
	 */
	public function setType(string $type) : static
	{
		switch ($type) {
			case 'ajax':
			case 'json':
				$this->type = 'ajax';
				break;
			default:
				$this->type = 'html';
		}
		
		return $this;
	}

	/**
	 * Returns the converted content to $type
	 * @param mixed $content The content
	 * @return mixed
	 */
	public function get($content)
	{
		return $this->responses->get($this->type)->get($content);
	}

	/**
	 * Outputs the $content
	 * @param string string The content to output
	 */
	public function output(string $content)
	{
		$this->headers->output();

		$this->responses->get($this->type)->output($content);
	}
}
