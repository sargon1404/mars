<?php
/**
* The Filepath Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Filepath Filter Class
*/
class Filepath extends Filename
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $filepath, ...$params) : string
	{
		$path = $this->app->file->getDirname($filepath);
		$filename = basename($filepath);

		$filepath = $path . parent::get($filename);

		return $filepath;
		//return $this->app->plugins->filter('filters_filepath_get', $filepath, $this);
	}
}
