<?php
/**
* The Http Response Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;

/**
* The Http Response Class
* Encapsulates a http response
*/
class Response
{
	/**
	* @var string $body The body
	*/
	public string $body = '';

	/**
	* @var array $headers Array listing the response headers
	*/
	public array $headers = [];

	/**
	* @var int $code The http code of the response
	*/
	public int $code = 0;

	/**
	* @var string $error The generated error if any
	*/
	public string $error = '';

	/**
	* @var array $info The request info
	*/
	protected array $info = [];

	/**
	* Builds the curl result object
	* @param resource $ch The curl chandle
	* @param string|bool $result  The curl result
	* @param App $app The app object
	*/
	public function __construct($ch, string|bool $result, App $app = null)
	{
		if ($result === false) {
			$this->error = curl_error($ch);
		}

		$this->app = $app ?? $this->getApp();
		$this->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->info = curl_getinfo($ch);
		$this->header = $this->getHeaders();
		$this->body = is_bool($result) ? '' : $result;
	}

	/**
	* Returns true if the curl request was successful
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->error || $this->code != 200) {
			return false;
		}

		return true;
	}

	/**
	* Returns the generated response
	* @return string
	*/
	public function getBody() : string
	{
		return $this->body;
	}

	/**
	* Returns the generated response as json code
	* @return mixed
	*/
	public function getJson() : mixed
	{
		return $this->app->json->decode($this->body);
	}

	/**
	* Returns the request headers
	* @return array
	*/
	protected function getHeaders() : array
	{
		if (!isset($this->info['request_header'])) {
			return [];
		}

		if (!is_array($this->info['request_header'])) {
			return explode("\n", $this->info['request_header']);
		}

		return $this->info['request_header'];
	}

}
