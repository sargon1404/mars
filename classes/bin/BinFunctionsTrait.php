<?php
/**
* The Bin Functions Trait
* @package Mars
*/

namespace Mars\Bin;

/**
* The Bin Functions Trait
*/
trait BinFunctionsTrait
{

	/**
	* @see \Mars\Bin::getOptions()
	*/
	public function getOptions(array $list = []) : array
	{
		return $this->app->bin->getOptions($list);
	}

	/**
	* @see \Mars\Bin::checkArguments()
	*/
	public function checkArguments(int $size) : bool
	{
		return $this->app->bin->checkArguments($size);
	}

	/**
	* @see \Mars\Bin::checkOptions()
	*/
	public function checkOptions(array $options) : bool
	{
		return $this->app->bin->checkOptions($options);
	}

	/**
	* @see \Mars\Bin::getOptionsMissing()
	*/
	public function getOptionsMissing() : array
	{
		return $this->app->bin->getOptionsMissing();
	}

	/**
	* @see \Mars\Bin::getArguments()
	*/
	public function getArguments(int $size) : array
	{
		return $this->app->bin->getArguments($size);
	}

	/**
	* @see \Mars\Bin::getArgument()
	*/
	public function getArgument() : string
	{
		return $this->app->bin->getArgument();
	}

	/**
	* @see \Mars\Bin::getArguments()
	*/
	public function getArgs(int $size) : array
	{
		return $this->app->bin->getArguments($size);
	}

	/**
	* @see \Mars\Bin::getOption()
	*/
	public function getOption($name) : ?string
	{
		return $this->app->bin->getOption($name);
	}

	/**
	* @see \Mars\Bin::isOption()
	*/
	public function isOption($name) : bool
	{
		return $this->app->bin->isOption($name);
	}

	/**
	* @see \Mars\Bin::getColor()
	*/
	public function getColor(string $type) : string
	{
		return $this->app->bin->getColor($type);
	}

	/**
	* @see \Mars\Bin::print()
	*/
	public function print($text, string $color = '', int $pad_left = 0, string $prefix = '', string $suffix = '', bool $newline = true, bool $die = false, int $empty_right = 5)
	{
		return $this->app->bin->print($text, $color, $pad_left, $prefix, $suffix, $newline, $die, $empty_right);
	}

	/**
	* @see \Mars\Bin::header()
	*/
	public function header(string $text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->bin->header($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Bin::message()
	*/
	public function message($text, string $title = '', bool $escape_html = true)
	{
		return $this->app->bin->message($text, (int)$title);
	}

	/**
	* @see \Mars\Bin::error()
	*/
	public function error($text, string $title = '', bool $escape_html = true)
	{
		return $this->app->bin->error($text);
	}

	/**
	* @see \Mars\Bin::warning()
	*/
	public function warning($text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->bin->warning($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Bin::info()
	*/
	public function info($text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->bin->info($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Bin::list()
	*/
	public function list(array $data, bool $headers_show = true, string $headers_color = '', string $col1_color = '', string $col2_color = '', int $col_1_left_pad = 3, int $col_2_left_pad = 15)
	{
		return $this->app->bin->list($data, $headers_show, $headers_color, $col1_color, $col2_color, $col_1_left_pad, $col_2_left_pad);
	}

	/**
	* @see \Mars\Bin::del()
	*/
	public function del()
	{
		$this->app->bin->del();
	}

	/**
	* @see \Mars\Bin::padStringLeft()
	*/
	public function padStringLeft(string $str, int $pad_length) : string
	{
		return $this->app->bin->padStringLeft($str, $pad_length);
	}

	/**
	* @see \Mars\Bin::padStringRight()
	*/
	public function padStringRight(string $str, int $pad_length) : string
	{
		return $this->app->bin->padStringRight($str, $pad_length);
	}

	/**
	* @see \Mars\Bin::padString()
	*/
	public function padString(string $str, int $pad_length, int $pad_length_left = 0) : string
	{
		return $this->app->bin->padStringRight($str, $pad_length, $pad_length_left);
	}
}
