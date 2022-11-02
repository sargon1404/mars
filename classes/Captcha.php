<?php
/**
* The Captcha Class
* @package Mars
*/

namespace Mars;

use Mars\Captcha\DriverInterface;

/**
* The Captcha Class
* Class which provides captcha functionality
*/
class Captcha
{
	use AppTrait;

	/**
	* @var bool $enabled Will be set to true, if captcha is enabled
	*/
	protected bool $enabled = false;

	/**
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var DriverInterface $driver The driver object
	*/
	protected DriverInterface $driver;

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'recaptcha2' => '\Mars\Captcha\Recaptcha2'
	];

	/**
	* Builds the captcha object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app ?? $this->getApp();

		if (!$this->app->config->captcha_enable) {
			return;
		}

		$this->enabled = true;
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'captchae', $this->app);
		$this->driver = $this->drivers->get($this->app->config->captcha_driver);
	}

	/**
	* Checks the captcha is correct
	* @return bool Returns bool if the captcha is correct
	*/
	public function check() : bool
	{
		if (!$this->enabled) {
			return true;
		}

		return $this->driver->check();
	}

	/**
	* Outputs the captcha
	*/
	public function output()
	{
		if (!$this->enabled) {
			return;
		}

		echo '<div class="captcha">';
		$this->driver->output();
		echo '</div>';
	}
}
