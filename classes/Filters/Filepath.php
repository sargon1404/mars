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
	 * @see \Mars\Filter::filepath()
	 */
	public function filter(string $filepath) : string
	{
		$path = $this->app->file->getPath($filepath);
		$filename = basename($filepath);

		if ($path) {
			$filepath = $path . '/' . parent::filter($filename);
		} else {
			$filepath = parent::filter($filename);
		}

		return $this->app->plugins->filter('filters_filepath_filter', $filepath);
	}
}
