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
	public function print(string $text, string $color = '', bool $newline = true, bool $die = false)
	{
		return $this->app->cli->print($text, $color, $newline, $die);
	}

	/**
	* @see \Mars\Cli::header()
	*/
	public function header(string $text, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->header($text, $newline, $die);
	}

	/**
	* Outputs a CLI message
	* @param string $text The text of the message
	* @param int $pad_left The number of spaces to prefix $text with
	* @param bool $escape_html Unused
	* @return $this
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		return $this->app->cli->message($text, (int)$title);
	}

	/**
	* Outputs a CLI error
	* @param string $text The text of the error
	* @param string $title Unused
	* @param bool $escape_html Unused
	* @return $this
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		return $this->app->cli->error($text);
	}

	/**
	* @see \Mars\Cli::errorAndDie()
	*/
	public function errorAndDie(string $text, bool $newline = true)
	{
		return $this->app->cli->errorAndDie($text, $newline);
	}

	/**
	* @see \Mars\Cli::warning()
	*/
	public function warning(string $text, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->warning($text, $newline, $die);
	}

	/**
	* @see \Mars\Cli::info()
	*/
	public function info(string $text, bool $newline = true, bool $die = false)
	{
		return $this->app->cli->info($text, $newline, $die);
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
