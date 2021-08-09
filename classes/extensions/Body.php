<?php
/**
* The Extension Body "Class"
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
* The Extension Body "Class"
* Contains the functionality for classes Extension/Basic
*/
trait Body
{
	/**
	* @var string $name The name of the extension
	*/
	public string $name = '';

	/**
	* @var string $path The path of the folder where the extension is installed
	*/
	public string $path = '';

	/**
	* @var string $url Alias for path_url
	*/
	public string $url = '';

	/**
	* @var string $path_url The url pointing to the folder where the extension is installed
	*/
	public string $path_url = '';

	/**
	* @var string $base_url The url pointing to the folder where the extension is installed. It uses the static base url
	*/
	public string $base_url = '';

	/**
	* @var bool $development If true, the extension is run in development mode
	*/
	public bool $development = false;

	/**
	* @var float $exec_time The time needed to run this extension
	*/
	public float $exec_time = 0;

	/**
	* @var string $type The type of the extension
	*/
	//protected static string $type = '';

	/**
	* @var string $base_dir The dir where these type of extensions are located
	*/
	//protected static string $base_dir = '';

	/**
	* Returns the extension's type
	* @return string
	*/
	public static function getType() : string
	{
		return static::$type;
	}

	/**
	* Returns the extension's base dir
	* @return string
	*/
	public static function getBaseDir() : string
	{
		return static::$base_dir;
	}

	/**
	* Prepares the extension
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
	}

	/**
	* Prepares the base paths
	*/
	protected function preparePaths()
	{
		$this->path = $this->getPath();
		$this->path_url = $this->getPathUrl();
		$this->base_url = $this->getBaseUrl();

		$this->url =  $this->path_url;
	}

	/**
	* Prepares the development property
	*/
	protected function prepareDevelopment()
	{
		if ($this->app->development) {
			$this->development = true;
		}
	}

	/**
	* Returns the root path where extensions of this type are located
	*/
	public function getRootPath() : string
	{
		return $this->app->extensions_path;
	}

	/**
	* Returns the root url where extensions of this type are located
	*/
	public function getRootUrl() : string
	{
		return $this->app->extensions_url;
	}

	/**
	* Returns the static root url where extensions of this type are located
	*/
	public function getRootUrlStatic() : string
	{
		return $this->app->getStaticUrl('extensions');
	}

	/**
	* Returns the path of the folder where the extension is installed
	* @param string $name The name of the extension. If empty, the name of the current extension is used
	* @return string The path
	*/
	public function getPath(string $name = '') : string
	{
		if (!$name) {
			if ($this->path) {
				return $this->path;
			}

			$name = $this->name;
		}

		return $this->getRootPath() . static::$base_dir . '/' . App::sl($name);
	}

	/**
	* Returns the url pointing to the folder where the extension is installed
	* @param string $name The name of the extension. If empty, the name of the current extension is used
	* @return string The base url
	*/
	public function getPathUrl(string $name = '') : string
	{
		if (!$name) {
			if ($this->url) {
				return $this->url;
			}

			$name = $this->name;
		}

		return $this->getRootUrl() . static::$base_dir . '/' . App::sl(rawurldecode($name));
	}

	/**
	* Returns the static url pointing to the folder where the extension is installed
	* @param string $name The name of the extension. If empty, the name of the current extension is used
	* @return string The base url
	*/
	public function getBaseUrl(string $name = '') : string
	{
		if (!$name) {
			if ($this->base_url) {
				return $this->base_url;
			}

			$name = $this->name;
		}

		return $this->getRootUrlStatic() . static::$base_dir . '/' . App::sl(rawurldecode($name));
	}

	/**
	* Runs the extension and outputs the generated content
	*/
	public function output()
	{
		echo $this->run();
	}

	/**
	* Executes the extension's code and returns the generated content
	* @return string The generated content
	*/
	public function run()
	{
		$this->startOutput();

		include($this->path . 'index.php');

		return $this->endOutput();
	}

	/**
	* Starts the output buffering
	*/
	protected function startOutput()
	{
		$this->app->timer->start('extension_output');

		ob_start();
	}

	/**
	* Ends the output buffering
	* @return string The output
	*/
	protected function endOutput()
	{
		$output = ob_get_clean();

		$this->exec_time = $this->app->timer->end('extension_output');

		return $output;
	}
}
