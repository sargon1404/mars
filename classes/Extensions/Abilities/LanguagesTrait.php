<?php
/**
* The Extension's Languages Trait
* @package Venus
*/

namespace Mars\Extensions\Abilities;

use Mars\App;

/**
 * The Extension's Languages Trait
 * Trait which allows extensions to load language files
 */
trait LanguagesTrait
{
	/**
	 * Loads a file from the extension's languages dir
	 * @param string $file The name of the file to load (must not include the .php extension)
	 * @param string $prefix The string's prefix, if any
	 * @return static
	 */
	public function loadLanguage(string $file = '', ?string $prefix = null) : static
	{
		if (!$file) {
			$file = $this->name;
		}
		if ($prefix === null) {
			$prefix = $this->name;
		}

		$filename = $this->path . '/' . App::EXTENSIONS_DIRS['languages'] . '/' . $this->app->lang->name . '/' . $file . '.php';

		$this->app->lang->loadFilename($filename, $prefix);

		return $this;
	}
}
