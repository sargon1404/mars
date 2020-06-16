<?php
/**
* The Curl Class
* @package Mars
*/

namespace Mars\Helpers;

/**
* The Curl Class
* Wrapper around the Curl library
*/
class Curl
{
	/**
	* @var string $useragent The useragent used when making requests
	*/
	public string $useragent = '';

	/**
	* @var bool $followlocation Determines the value of CURLOPT_FOLLOWLOCATION
	*/
	public bool $followlocation = true;

	/**
	* @var bool $header If true,the headers will be also returned
	*/
	public bool $header = false;

	/**
	* @var int $code The http code of the last request
	*/
	public string $code = '';

	/**
	* @var array $request_header The request headers
	*/
	public array $request_header = [];

	/**
	* @var string $error The generated error if any
	*/
	public string $error = '';

	/**
	* @var array $info The request info
	*/
	public array $info = [];

	/**
	* @var array $options Curl options for curl_setopt
	*/
	protected array $options = [];

	/**
	* Builds the curl object
	* @param array $options Curl options specified as key=>value
	*/
	public function __construct(array $options = [])
	{
		if (!extension_loaded('curl')) {
			throw new \Exception('The curl extension must be enabled on this server!');
		}

		if (!$options) {
			$this->setOptions($options);
		}

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->useragent = $_SERVER['HTTP_USER_AGENT'];
		}

		$this->app->plugins->run('helpers_curl_construct', $this);
	}

	/**
	* Curl Options can be set as key=>value before the request is made
	* @param array $options The options
	* @return $this
	*/
	public function setOptions(array $options = [])
	{
		$this->options = $this->options + $options;

		return $this;
	}

	/**
	* Sets the basic curl options [header/useragent/followlocation]
	* @param string $url The url
	* @return resource The curl handle
	*/
	protected function init(string $url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
		curl_setopt($ch, CURLOPT_HEADER, $this->header);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		foreach ($this->options as $name => $val) {
			curl_setopt($ch, $name, $val);
		}

		return $ch;
	}

	/**
	* Executes the curl session and returns the result
	* @param resource $ch The curl handler
	* @return string The result
	*/
	protected function exec($ch) : ?string
	{
		$result = curl_exec($ch);
		$this->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->info = curl_getinfo($ch);

		if ($this->code) {
			$this->request_header = $this->info['request_header'];
		} else {
			$this->error = curl_error($ch);
		}

		curl_close($ch);

		if (!$result) {
			$result = null;
		}

		return $result;
	}

	/**
	* Fetches an url with a GET request
	* @param string $url The url to retrieve
	* @return string The contents of the url; null on failure
	*/
	public function get(string $url) : ?string
	{
		$ch = $this->init($url);

		return $this->exec($ch);
	}

	/**
	* Uses Curl to download a file with a get request
	* @param string $url The url to retrieve
	* @param string $filename The local filename under which the file will be stored
	* @return string The contents of the url, null on failure
	*/
	public function getFile(string $url, string $filename) : ?string
	{
		$f = fopen($filename, 'wb');
		if (!$f) {
			return null;
		}

		$ch = $this->init($url);

		curl_setopt($ch, CURLOPT_FILE, $f);

		$result = $this->exec($ch);

		fclose($f);

		return $result;
	}

	/**
	* Fetches an url with a POST request
	* @param string $url The url to retrieve
	* @param array $data Array with the data to post
	* @param array $files Files to send in the name=>filename format
	* @return string The contents of the url; null on failure
	*/
	public function post(string $url, array $data, array $files = []) : ?string
	{
		if ($files) {
			foreach ($files as $name => $filename) {
				$file = new \CURLFile($filename, null, basename($filename));
				$data[$name] = $file;
			}
		}

		$ch = $this->init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		return $this->exec($ch);
	}
}
