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
	protected $urls = ['first' => [], 'head' => [], 'footer' => []];

	/**
	* Outputs an url
	* @param string $url The url to output
	* @param bool $async If true, will apply the async attr
	* @param bool $defer If true, will apply the defer attr
	*/
	abstract public function outputUrl(string $url, bool $async = false, bool $defer = false);

	/**
	* Returns the list of urls
	* @return array
	*/
	public function get() : array
	{
		return array_map([$this, 'sort'], $this->urls);
	}

	/**
	* Returns the list of urls
	* @param string $location The location of the urls [head|footer]
	* @return array
	*/
	public function getUrls(string $location = 'head') : array
	{
		if (!isset($this->urls[$location])) {
			return [];
		}

		return $this->sort($this->urls[$location]);
	}

	/**
	* Sorts the urls
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
	* Returns the list of local urls
	* @param string $location The location of the urls [head|footer]
	* @return array
	*/
	public function getLocalUrls(string $location = '') : array
	{
		$urls = $this->getSplitUrls($location);

		return $urls['local'];
	}

	/**
	* Returns the list of external urls
	* @param string $location The location of the urls [head|footer]
	* @return array
	*/
	public function getExternalUrls(string $location = '') : array
	{
		$urls = $this->getSplitUrls($location);

		return $urls['external'];
	}

	/**
	* Splits the urls into local and external
	* @param string $location The location of the urls [head|footer]. If empty, urls from all locations are returned
	* @param bool $group If true, the urls will be grouped. If false, it will be returned based on location
	* @return array
	*/
	public function getSplitUrls(string $location = '', bool $group = false) : array
	{
		$split_urls = ['local' => [], 'external' => []];

		if ($location) {
			$urls = [];
			if (isset($this->urls[$location])) {
				$urls = $this->getUrls($location);
			}

			foreach ($urls as $url => $data) {
				$key = 'external';
				if ($this->app->uri->isLocal($url)) {
					$key = 'local';
				}

				$split_urls[$key][$url] = $data;
			}
		} else {
			foreach ($this->urls as $pos => $urls) {
				$urls = $this->sort($urls);

				foreach ($urls as $url => $data) {
					$key = 'external';
					if ($this->app->uri->isLocal($url)) {
						$key = 'local';
					}

					if ($group) {
						$split_urls[$key][$url] = $data;
					} else {
						$split_urls[$key][$pos][$url] = $data;
					}
				}
			}
		}

		return $split_urls;
	}

	/**
	* Loads an url
	* @param string $url The url to load. Will only load it once, no matter how many times the function is called with the same url
	* @param string $location The location of the url [head|footer]
	* @param int $priority The url's output priority. The higher, the better
	* @param bool $async If true, will apply the async attr
	* @param bool $defer If true, will apply the defer attr
	* @return $this
	*/
	public function load(string $url, string $location = 'head', int $priority = 100, bool $async = false, bool $defer = false)
	{
		if ($this->isLoaded($url)) {
			return $this;
		}

		$this->urls[$location][$url] = ['priority' => $priority, 'async' => $async, 'defer' => $defer];

		return $this;
	}

	/**
	* Checks if the url is loaded
	* @param string $url The url to check
	* @return bool
	*/
	protected function isLoaded(string $url) : bool
	{
		$is_loaded = false;
		foreach ($this->urls as $location => $urls) {
			if (isset($urls[$url])) {
				$is_loaded = true;
				break;
			}
		}

		return $is_loaded;
	}

	/**
	* Changes the properties of an url
	* @param string $url The url to change the properties for. If it's not found, it will be added
	* @param string $location The location of the url [head|footer]. If null, the value isn't changed
	* @param int $priority The url's output priority. The higher, the better. If null, the value isn't changed
	* @param bool $async If true, will apply the async attr. If null, the value isn't changed
	* @param bool Returns true if the url was found, false otherwise. If null, the value isn't changed
	* @return $this
	*/
	public function change(string $url, string $location = null, int $priority = null, bool $async = null, bool $defer = null) : bool
	{
		$url_location = $this->getLocation($url);

		if (!$url_location) {
			$this->load($url, $location ?? 'head', $priority ?? 100, $async ?? false, $defer ?? false);

			return false;
		} else {
			$data = $this->urls[$url_location][$url];

			if ($priority !== null) {
				$data['priority'] = $priority;
			}
			if ($async !== null) {
				$data['async'] = $async;
			}
			if ($defer !== null) {
				$data['defer'] = $defer;
			}

			if ($location && $location != $url_location) {
				$this->unload($url);
				$this->load($url, $location, $data['priority'], $data['async'], $data['defer']);
			} else {
				$this->urls[$url_location][$url] = $data;
			}

			return true;
		}
	}

	/**
	* Unloads an url
	* @param string $url The url to unload
	* @return $this
	*/
	public function unload(string $url)
	{
		foreach ($this->urls as $location => $urls) {
			if (isset($urls[$url])) {
				unset($this->urls[$location][$url]);
				break;
			}
		}

		return $this;
	}

	/**
	* Returns the location where an url is loaded
	* @param string The url
	* @return string The location. Returns an empty string if the url is not found
	*/
	public function getLocation(string $url) : string
	{
		$url_location = '';
		foreach ($this->urls as $location => $urls) {
			if (isset($urls[$url])) {
				$url_location = $location;
				break;
			}
		}

		return $url_location;
	}

	/**
	* Outputs the urls
	* @param string $location The location of the url [head|footer]
	* @return $this
	*/
	public function output(string $location = 'head')
	{
		if (!isset($this->urls[$location])) {
			return $this;
		}

		$this->outputUrls($this->getUrls($location));

		return $this;
	}

	/**
	* Outputs the urls
	* @param array $urls The urls to output
	* @return $this
	*/
	public function outputUrls(array $urls)
	{
		foreach ($urls as $url => $data) {
			$this->outputUrl($url, $data['async'], $data['defer']);
		}
	}
}
