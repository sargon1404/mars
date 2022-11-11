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
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return static
	*/
	public function loadLanguage(string $file = '', string $name = '') : static
	{
		if (!$name) {
			$name = $this->name;
		}
		if (!$file) {
			$file = 'index';
		}

		$filename = $this->getPath($name) . App::EXTENSIONS_DIRS['languages'] . $this->app->lang->name . '/' . $file . '.php';

		$this->app->lang->loadFilename($filename);

		return $this;
	}
}
