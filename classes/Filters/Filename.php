<?php
/**
* The Filename Filter Class
* @package Mars
*/

namespace Mars\Filters;

use Mars\App;

/**
* The Filename Filter Class
*/
class Filename extends Filter
{
	/**
	* @see \Mars\Filters\Filter::get()
	* {@inheritdoc}
	*/
	public function get(string $filename, ...$params) : string
	{
		$is_path = $params[0] ?? false;

		$max_chars = 300;
		$filename = trim($filename);
		$search = [
			'../', './', '/..', '/.', '..\\', '.\\', '\\..', '\\.' ,'php:',
			'<', '>', '[', ']', '(', ')', '{', '}', '\\', '*', '?', ':', ';',
			'$', '%', '*', '+', '#', '~', '&', '\'' ,'`', '=', '|', '!', chr(0),
		];

		if ($is_path) {
			//replace multiple slashes with just one
			$filename = preg_replace('/\/+/', '/', $filename);
		} else {
			$search[] = '/';

			$filename = basename($filename);
		}

		//filter the non-allowed chars
		$filename = str_replace($search, '', $filename);

		//filter non-ascii chars
		$reg = '/[\x00-\x1F\x80-\xFF]/';
		$filename = preg_replace($reg, '', $filename);

		//replace spaces with dashes
		$filename = str_replace(' ', '-', $filename);

		return $filename;
		//return $this->app->plugins->filter('filters_filename_get', $filename, $is_path, $this);
	}
}
