<?php
/**
* The Cli Functions Trait
* @package Mars
*/

namespace Mars\Cli;

/**
* The Cli Functions Trait
*/
trait CliFunctionsTrait
{

	/**
	* @see \Mars\Cli::getOptions()
	*/
	public function getOptions() : array
	{
		return $this->app->cli->getOptions();
	}

	/**
	* @see \Mars\Cli::checkOptions()
	*/
	public function checkOptions(array $options) : bool
	{
		return $this->app->cli->checkOptions($options);
	}

	/**
	* @see \Mars\Cli::getOptionsMissing()
	*/
	public function getOptionsMissing() : array
	{
		return $this->app->cli->getOptionsMissing();
	}

	/**
	* @see \Mars\Cli::getOptionsLists()
	*/
	public function getOptionsList(int $min_size = 0) : array
	{
		return $this->app->cli->getOptionsList($min_size);
	}

	/**
	* @see \Mars\Cli::getOption()
	*/
	public function getOption($name) : ?string
	{
		return $this->app->cli->getOption($name);
	}

	/**
	* @see \Mars\Cli::isOption()
	*/
	public function isOption($name) : bool
	{
		return $this->app->cli->isOption($name);
	}

	/**
	* @see \Mars\Cli::getColor()
	*/
	public function getColor(string $type) : string
	{
		return $this->app->cli->getColor($type);
	}

	/**
	* @see \Mars\Cli::print()
	*/
	public function print($text, string $color = '', int $pad_left = 0, bool $newline = true, bool $die = false, string $prefix = '', string $suffix = '')
	{
		return $this->app->cli->print($text, $color, $pad_left, $newline, $die, $prefix, $suffix);
	}

	/**
	* @see \Mars\Cli::header()
	*/
	public function header(string $text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->header($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Cli::message()
	*/
	public function message($text, string $title = '', bool $escape_html = true)
	{
		return $this->app->cli->message($text, (int)$title);
	}

	/**
	* @see \Mars\Cli::error()
	*/
	public function error($text, string $title = '', bool $escape_html = true)
	{
		return $this->app->cli->error($text);
	}

	/**
	* @see \Mars\Cli::warning()
	*/
	public function warning($text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->warning($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Cli::info()
	*/
	public function info($text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->info($text, $pad_left, $newline, $die);
	}

	/**
	* @see \Mars\Cli::list()
	*/
	public function list(array $data, bool $headers_show = true, string $headers_color = '', string $col1_color = '', string $col2_color = '', int $col_1_left_pad = 3, int $col_2_left_pad = 15)
	{
		return $this->app->cli->list($data, $headers_show, $headers_color, $col1_color, $col2_color, $col_1_left_pad, $col_2_left_pad);
	}

	/**
	* @see \Mars\Cli::del()
	*/
	public function del()
	{
		$this->app->cli->del();
	}
}
