<?php
/**
* The Extension Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;

/**
* The Extension Trait
* Contains the functionality of extensions
* The classes using this trait must set these properties:
* protected static string $type = '';
* protected static string $base_dir = '';
*/
trait ExtensionTrait
{
	/**
	* @var string $name The name of the extension
	*/
	public string $name = '';

	/**
	* @var string $path The path where the extension is located
	*/
	public string $path = '';

	/**
	* @var string $path_url The url pointing to the folder where the extension is located
	*/
	public string $url = '';

	/**
	* @var string $url_static The static url pointing to the folder where the extension is located
	*/
	public string $url_static = '';

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
		$this->url = $this->getUrl();
		$this->url_static = $this->getUrlStatic();
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
	* @return string The path
	*/
	public function getPath() : string
	{
		return $this->getRootPath() . static::$base_dir . '/' . $this->name . '/';
	}

	/**
	* Returns the url pointing to the folder where the extension is installed
	* @return string The url
	*/
	public function getUrl() : string
	{
		return $this->getRootUrl() . static::$base_dir . '/' . rawurldecode($this->name) . '/';
	}

	/**
	* Returns the static url pointing to the folder where the extension is installed
	* @return string The static url
	*/
	public function getUrlStatic() : string
	{
		return $this->getRootUrlStatic() . static::$base_dir . '/' . rawurldecode($this->name) . '/';
	}

	/**
	* Prepares the development property
	*/
	protected function prepareDevelopment()
	{
		$this->development = $this->app->development;
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
	public function run() : string
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
	protected function endOutput() : string
	{
		$output = ob_get_clean();

		$this->exec_time = $this->app->timer->end('extension_output');

		return $output;
	}
}
