<?php
/**
* The Cookie Response Class
* @package Mars
*/

namespace Mars\Response;

use Mars\App;

/**
* The Cookie Response Class
* Handles the setting/removing cookies
*/
class Cookies
{
	use \Mars\AppTrait;

	/**
	* @var int $expires The cookie's expires timestamp
	*/
	protected string $expires = '';

	/**
	* @var string $path The cookie's path
	*/
	protected string $path = '';

	/**
	* @var string $domain The cookie's domain
	*/
	protected string $domain = '';

	/**
	* @var string $secure If true the cookies will only be sent over secure connections.
	*/
	protected bool $secure = false;

	/**
	* @var bool $httponly If true then httponly flag will be set for the cookies
	*/
	public bool $httponly = true;

	/**
	* Builds the Cookie object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->expires = time() + 3600 * 24 * $this->app->config->cookie_expire_days;
		$this->path = $this->app->config->cookie_path;
		$this->domain = $this->app->config->cookie_domain;
		$this->secure = $this->app->config->cookie_secure;
		$this->httponly = $this->app->config->cookie_httponly;
	}

	/**
	* Sets a cookie. The data is first encoded using json encode
	* @param string $name The name of the cookie
	* @param mixed $data The data to be written
	* @param int $expires The expiration date. If null, $this->cookie_expires is used
	* @param string $path The path in which the cookie is valid. If null, $this->cookie_path is used
	* @param string $domain The domain in which the cookie is valid. If null, $this->cookie_domain is used
	* @param bool $secure If true, the cookie will only be set over a https connection
	* @param bool $httponly If true the cookie is accesible only over http and not with javascript
	* @param bool $encode If true will encode the data
	* @return static
	*/
	public function set(string $name, $data, ?int $expires = null, ?string $path = null, ?string $domain = null, bool $secure = null, bool $httponly = null, bool $encode = true) : static
	{
		if ($encode) {
			$data = $this->app->json->encode($data);
		}

		$expires = $expires ?? $this->expires;
		$path = $path ?? $this->path;
		$domain = $domain ?? $this->domain;
		$secure = $secure ?? $this->secure;
		$httponly = $httponly ?? $this->httponly;

		setcookie($name, $data, $expires, $path, $domain, $secure, $httponly);

		return $this;
	}

	/**
	* Deletes a cookie
	* @param string $name The name of the cookie
	* @param string $path The path in which the cookie is valid. If null, $this->cookie_path is used
	* @param string $domain The domain in which the cookie is valid. If null, $this->cookie_domain is used
	* @return static
	*/
	public function unset(string $name, ?string $path = null, ?string $domain = null) : static
	{
		$path = $path ?? $this->path;
		$domain = $domain ?? $this->domain;

		setcookie($name, '', 0, $path, $domain);

		return $this;
	}
}
