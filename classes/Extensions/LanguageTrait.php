<?php
/**
* The Language Trait
* @package Mars
*/

namespace Mars\Extensions;

/**
 * The Language Trait
 * Trait implementing the Language functionality
 */
trait LanguageTrait
{
	/**
	 * @var string $encoding The encoding of the language
	 */
	public string $encoding = 'UTF-8';

	/**
	 * @var string $code The language's code
	 */
	public string $code = 'en';

	/**
	 * @var string $timestamp_format The format in which a timestamp will be displayed
	 */
	public string $timestamp_format = 'D M d, Y g:i a';

	/**
	 * @var string $date_format The format in which a date will be displayed
	 */
	public string $date_format = 'D M d, Y';

	/**
	 * @var string $time_format The format in which the time of the day will be displayed
	 */
	public string $time_format = 'g:i a';

	/**
	 * @var string datetime_picker_format The format of the datetime picker
	 */
	public string $datetime_picker_format = 'Y-m-d H:i';

	/**
	 * @var string date_picker_format The format of the date picker
	 */
	public string $date_picker_format = 'Y-m-d';

	/**
	 * @var string time_picker_format The format of the time picker
	 */
	public string $time_picker_format = 'H:i';

	/**
	 * @var string $decimal_separator The language's decimal_separator
	 */
	public string $decimal_separator = '.';

	/**
	 * @var string $thousands_separator The language's thousands_separator
	 */
	public string $thousands_separator = ',';

	/**
	 * @var array $strings The language's strings
	 */
	public array $strings = [];

	/**
	 * @var array $loaded_files The list of loaded files
	 */
	protected array $loaded_files = [];

	/**
	 * @internal
	 */
	protected static string $type = 'language';

	/**
	 * @internal
	 */
	protected static string $base_dir = 'languages';

	/**
	 * Loads the specified $file from the languages folder
	 * @param string $file The name of the file to load
	 * @param string $prefix Prefix to apply to the strings, if any
	 * @return static
	 */
	public function loadFile(string $file, string $prefix = '') : static
	{
		if (isset($this->loaded_files[$file])) {
			return $this;
		}

		$this->loaded_file[$file] = true;

		$this->loadFilename($this->path . '/' . $file . '.php', $prefix);

		return $this;
	}

	/**
	 * Loads the specified filename from anywhere on the disk as a language file
	 * @param string $filename The filename to load
	 * @param string $prefix Prefix to apply to the strings, if any
	 * @return static
	 */
	public function loadFilename(string $filename, string $prefix = '') : static
	{
		$strings = include($filename);

		if ($prefix) {
			$strings_with_prefix = [];
			foreach ($strings as $key => $string) {
				$key = $prefix . '.' . $key;
				$strings_with_prefix[$key] = $string;
			}

			$strings = $strings_with_prefix;
		}

		$this->strings = array_merge($this->strings, $strings);

		return $this;
	}
}
