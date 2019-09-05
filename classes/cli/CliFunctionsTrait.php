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
	* @see \Mars\Cli::print()
	*/
	public function print(string $text, string $color = '', bool $newline = true, bool $die = false)
	{
		return $this->app->cli->print($text, $color, $newline, $die);
	}

	/**
	* Outputs a CLI message
	* @param string $text The text of the message
	* @param string $title Unused
	* @param bool $escape_html Unused
	* @return $this
	*/	
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		return $this->app->cli->message($text);
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
	* @see \Mars\Cli::del()
	*/
	public function del()
	{
		$this->app->cli->del();
	}
}
