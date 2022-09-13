<?php
/**
* The Filename Filter Class
* @package Mars
*/

namespace Mars\Filters;

/**
* The Filename Filter Class
*/
class Filename
{
	use \Mars\AppTrait;

	/**
	* @var int $max_chars The maximum number of chars allowed in $filename
	*/
	protected int $max_chars = 300;

	/**
	* @see \Mars\Filter::filename()
	*/
	public function filter(string $filename) : string
	{
		$filename = basename(trim($filename));
		if (strlen($filename > $this->max_chars)) {
			$filename = $this->cutFilename($filename);
		}

		$search = [
			'../', './', '/..', '/.', '..\\', '.\\', '\\..', '\\.' ,'php:',
			'<', '>', '[', ']', '(', ')', '{', '}', '\\', '*', '?', ':', ';', '/',
			'$', '%', '*', '+', '#', '~', '&', '\'' ,'`', '=', '|', '!', chr(0),
		];

		//filter the non-allowed chars
		$filename = str_replace($search, '', $filename);

		//filter non-ascii chars
		$reg = '/[\x00-\x1F\x80-\xFF]/';
		$filename = preg_replace($reg, '', $filename);

		//replace spaces with dashes
		$filename = str_replace(' ', '-', $filename);

		return $this->app->plugins->filter('filters_filename_filter', $filename);
	}

	/*
	* Will cut filename to $max_chars
	* @param string $filename The filename
	* @return string
	*/
	protected function cutFilename(string $filename) : string
	{
		$name = substr($this->app->file->getFile($filename), 0, $this->max_chars);
		$ext = $this->app->file->getExtension($filename);

		return $this->app->file->addExtension($name, $ext);
	}
}
