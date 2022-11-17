<?php
/**
* The Extension's Templates Trait
* @package Venus
*/

namespace Mars\Extensions\Abilities;

use Mars\App;

/**
* The Extension's Templates Trait
* Trait which allows extensions to load templates
*/
trait TemplatesTrait
{
	/**
	* Loads the template and outputs it.
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	*/
	public function render(string $template, array $vars = [])
	{
		echo $this->getTemplate($template, $vars);
	}

	/**
	* Loads a template from the extension's templates dir
	* @param string $template The name of the template to load
	* @param array $vars Vars to pass to the template, if any
	* @return string The contents of the template
	*/
	public function getTemplate(string $template, array $vars = []) : string
	{
		$filename = $this->path . App::EXTENSIONS_DIRS['templates'] . $template . '.' . App::FILE_EXTENSIONS['templates'];

		return $this->app->theme->getTemplateFromFilename($filename, $vars, static::$type, $this->development);
	}
}
