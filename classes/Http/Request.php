<?php
/**
* The Http Request Class
* @package Mars
*/

namespace Mars\Http;

use Mars\App;

/**
 * The Http Request Class
 * Wrapper around the Curl library for http requests
 */
class Request
{
	use \Mars\AppTrait;

	/**
	 * @var string $url The url
	 */
	public string $url = '';

	/**
	 * @var int $timeout The timeout, in seconds
	 */
	public int $timeout = 30;

	/**
	 * @var string $useragent The useragent used when making requests
	 */
	public string $useragent = '';

	/**
	 * @var bool $follow_location Determines the value of CURLOPT_FOLLOWLOCATION
	 */
	public bool $follow_location = true;

	/**
	 * @var bool $show_headers If true,the headers will be also returned
	 */
	public bool $show_headers = false;

	/**
	 * @var array $headers Array listing the custom headers to be sent
	 */
	public array $headers = [];

	/**
	 * @var array $options Array listing CURL options, if any
	 */
	public array $options = [];

	/**
	 * Builds the Http Request object
	 * @param string $url The url of the request
	 * @param App $app The app object
	 */
	public function __construct(string $url, App $app = null)
	{
		if (!extension_loaded('curl')) {
			throw new \Exception('The curl extension must be enabled on this server to be able to use the Curl class!');
		}

		$this->app = $app ?? $this->getApp();
		$this->url = $url;
		$this->useragent = $this->app->getUseragent();
	}

	/**
	 * Adds a request header
	 * @param string $header The header to add
	 * @return static
	 */
	public function addHeader(string $header) : static
	{
		$this->headers[] = $header;

		return $this;
	}

	/**
	 * Sets the basic curl options [header/useragent/followlocation]
	 * @param array $options Curl options, if any
	 * @return resource The curl handle
	 */
	protected function init(array $options = [])
	{
		$options = $options + $this->options;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->follow_location);
		curl_setopt($ch, CURLOPT_HEADER, $this->show_headers);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt_array($ch, $options);

		return $ch;
	}

	/**
	 * Executes the curl session and returns the result
	 * @param resource $ch The curl handler
	 * @return Response The response
	 */
	protected function exec($ch) : Response
	{
		$result = curl_exec($ch);

		$response = new Response($ch, $result, $this->app);

		curl_close($ch);

		return $response;
	}

	/**
	 * Sets the request referer
	 * @param string $referer The referer
	 * @return static
	 */
	public function setReferer(string $referer) : static
	{
		$this->options[CURLOPT_REFERER] = $referer;

		return $this;
	}

	/**
	 * Sets the cookies jar filename
	 * @param string $filename
	 */
	public function setCookiesJar(string $filename) : static
	{
		$this->options[CURLOPT_COOKIEJAR] = $filename;
		$this->options[CURLOPT_COOKIEFILE] = $filename;

		return $this;
	}

	/**
	 * Fetches an url with a custom request
	 * @param string $request The custom request
	 * @return Response The response
	 */
	public function custom(string $request) : Response
	{
		$ch = $this->init([CURLOPT_CUSTOMREQUEST => $request]);

		return $this->exec($ch);
	}

	/**
	 * Fetches an url with a GET request
	 * @return Response The response
	 */
	public function get() : Response
	{
		$ch = $this->init();

		return $this->exec($ch);
	}

	/**
	 * Fetches an url with a POST request
	 * @param array $data Array with the data to post
	 * @param array $files Files to send in the name=>filename format
	 * @return Response The response
	 */
	public function post(array $data, array $files = []) : Response
	{
		if ($files) {
			foreach ($files as $name => $filename) {
				$file = new \CURLFile($filename, null, basename($filename));
				$data[$name] = $file;
			}
		}

		$options = [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data
		];

		$ch = $this->init($options);

		return $this->exec($ch);
	}

	/**
	 * Downloads a file with a get request
	 * @param string $filename The local filename under which the file will be stored
	 * @param bool $download_if_exists If false, the file won't be downloaded, if it already exists
	 * @return Response The response. If the file exists and $download_if_exists = false, it will return true
	 * @throws Exception if the file can't be written
	 */
	public function getFile(string $filename, bool $download_if_exists = true) : bool|Response
	{
		if (!$download_if_exists) {
			if (is_file($filename)) {
				return true;
			}
		}

		$f = fopen($filename, 'wb');
		if (!$f) {
			throw new \Exception(App::__('file_error_write', ['{FILE}' => $filename]));
		}

		$ch = $this->init([CURLOPT_FILE => $f]);

		$response = $this->exec($ch);

		fclose($f);

		return $response;
	}

	/**
	 * Returns the contents of the file. If the file exists, if returns it's content. If the file doesn't exist, it will download it with a get request
	 * @param string $filename The local filename under which the file will be stored
	 * @return string The file's content
	 * @throws Exception if the file can't be written
	 */
	public function getFileContent(string $filename) : string
	{
		if (!is_file($filename)) {
			$this->getFile($filename);
		}

		return file_get_contents($filename);
	}
}
