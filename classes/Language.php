<?php
/**
* The Language "Class"
* @package Mars
*/

namespace Mars;

/**
* The Language "Class"
* Trait implementing the Language functionality
*/
trait Language
{
	/**
	* @var string $encoding The encoding of the language
	*/
	public $encoding = 'UTF-8';

	/**
	* @var string $code The language's code
	*/
	public $code = 'en';

	/**
	* @var string $timestamp_format The format in which a timestamp will be displayed
	*/
	public $timestamp_format = 'D M d, Y g:i a';

	/**
	* @var string $date_format The format in which a date will be displayed
	*/
	public $date_format = 'D M d, Y';

	/**
	* @var string $time_format The format in which the time of the day will be displayed
	*/
	public $time_format = 'g:i a';

	/**
	* @var string $decimal_separator The language's decimal_separator
	*/
	public $decimal_separator = '.';

	/**
	* @var string $thousands_separator The language's thousands_separator
	*/
	public $thousands_separator = ',';

	/**
	* @var array $strings The language's strings
	*/
	public $strings = [];

	/**
	* @internal
	*/
	protected $loaded_files = [];

	/**
	* @internal
	*/
	protected static $base_dir = 'languages';

	/**
	* Loads the specified $file from the languages folder
	* @param string $file The name of the file to load
	* @return $this
	*/
	public function loadFile(string $file)
	{
		if (!$file) {
			return $this;
		}

		if (in_array($file, $this->loaded_files)) {
			return $this;
		}

		$this->loaded_file[] = $file;

		$this->loadFilename($this->dir . $file . '.php');

		return $this;
	}

	/**
	* Loads the specified filename from anywhere on the disk as a language file
	* @param string $filename The filename to load
	* @return $this
	*/
	public function loadFilename(string $filename)
	{
		$strings = include($filename);

		$this->strings = array_merge($this->strings, $strings);

		return $this;
	}
}
