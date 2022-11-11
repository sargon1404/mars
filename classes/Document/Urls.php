<?php
/**
* The Urls Class
* @package Mars
*/

namespace Mars\Document;

/**
* The Document Urls Class
* Abstract class containing the urls & their corresponding locations used by a document
*/
abstract class Urls
{
	use \Mars\AppTrait;

	/**
	* @var array $urls Array with all the urls to be outputed
	*/
	protected array $urls = [];

	/**
	* @var string $version The version to be applied to the urls
	*/
	protected string $version = '';

	/**
	* @var string $push_type The http2 push type
	*/
	protected string $push_type = '';

	/**
	* Outputs an url
	* @param string $url The url to output
	* @param bool $async If true, will apply the async attr
	* @param bool $defer If true, will apply the defer attr
	* @param bool $is_local True, if the url is a local url
	*/
	abstract public function outputUrl(string $url, bool $async = false, bool $defer = false);

	/**
	* Loads an url
	* @param string $url The url to load. Will only load it once, no matter how many times the function is called with the same url
	* @param string $location The location of the url [first|head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
	* @param bool $async If true, will apply the async attr
	* @param bool $defer If true, will apply the defer attr
	* @return static
	*/
	public function add(string $url, string $location = 'head', int $priority = 100, $version = true, bool $async = false, bool $defer = false) : static
	{
		$this->urls[$url] = [
			'location' => $location, 'priority' => $priority, 'version' => $version,
			'async' => $async, 'defer' => $defer, 'is_local' => $this->app->uri->isLocal($url)
		];

		return $this;
	}

	/**
	* Unloads an url
	* @param string $url The url to unload
	* @return static
	*/
	public function remove(string $url) : static
	{
		if (!isset($this->urls[$url])) {
			return $this;
		}

		unset($this->urls[$url]);

		return $this;
	}

	/**
	* Returns the list of urls
	* @return array
	*/
	public function get(string $location = '') : array
	{
		if (!$location) {
			return $this->sort($this->urls);
		}

		$urls = array_filter($this->urls, function ($url) use ($location) {
			if ($url['location'] == $location) {
				return true;
			}

			return false;
		});

		return $this->sort($urls);
	}

	/**
	* Sorts the urls by priority
	* @param array $urls
	* @return array The sorted urls
	*/
	protected function sort(array $urls) : array
	{
		//sort the urls by priority
		uasort($urls, function ($url1, $url2) {
			return $url2['priority'] <=> $url1['priority'];
		});

		return $urls;
	}

	/**
	* Outputs the urls
	* @param string $location The location of the url [first_head|footer]
	* @return static
	*/
	public function output(string $location = 'head') : static
	{
		$urls = $this->get($location);

		foreach ($urls as $url => $data) {
			$url = $this->getUrl($url, $data['version']);

			if ($data['is_local']) {
				$this->pushUrl($url);
			}

			$this->outputUrl($url, $data['async'], $data['defer']);
		}

		return $this;
	}

	/**
	* Returns an url with the version appended, if required
	* @param string $url The url to append the version to
	* @param bool|string $version If string, will add the specified version. If true, will add the configured version param to the url
	* @return string The url
	*/
	public function getUrl(string $url, bool|string $version = true) : string
	{
		if (!$version) {
			return $url;
		}

		if (is_bool($version)) {
			$version = $this->version;
		}

		return $this->app->uri->build($url, ['ver' => $version]);
	}

	/**
	* http2 pushes the url, if enabled
	* @param string $url The url to push
	*/
	protected function pushUrl(string $url)
	{
		$this->app->response->push->add($url, $this->push_type);
	}
}
