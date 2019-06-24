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
	public $name = '';

	/**
	* @var string $dir The path of the folder where the extension is installed
	*/
	public $dir = '';

	/**
	* @var string $dir_url The url pointing to the folder where the extension is installed
	*/
	public $dir_url = '';

	/**
	* @var string $dir_url_static The static url pointing to the folder where the extension is installed
	*/
	public $dir_url_static = '';

	/**
	* @var bool $development If true, the extension is run in development mode
	*/
	public $development = false;

	/**
	* @var int $exec_time The time needed to run this extension
	*/
	public $exec_time = 0;

	/**
	* @var string $type The type of the extension
	*/
	//protected static $type = '';
	/**
	* @var string $base_dir The dir where these type of extensions are located
	*/
	//protected static $base_dir = '';

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
		$this->dir = $this->getDir();
		$this->dir_url = $this->getDirUrl();
		$this->dir_url_static = $this->getDirUrlStatic();
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
	* Returns the root dir where extensions of this type are located
	*/
	public function getRootDir() : string
	{
		return $this->app->extensions_dir;
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
	* @return string The base dir
	*/
	public function getDir(string $name = '') : string
	{
		if (!$name) {
			if ($this->dir) {
				return $this->dir;
			}

			$name = $this->name;
		}

		return $this->getRootDir() . static::$base_dir . '/' . App::sl($name);
	}

	/**
	* Returns the url pointing to the folder where the extension is installed
	* @param string $name The name of the extension. If empty, the name of the current extension is used
	* @return string The base url
	*/
	public function getDirUrl(string $name = '') : string
	{
		if (!$name) {
			if ($this->dir_url) {
				return $this->dir_url;
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
	public function getDirUrlStatic(string $name = '') : string
	{
		if (!$name) {
			if ($this->dir_url_static) {
				return $this->dir_url_static;
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

		include($this->dir . 'index.php');

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
