<?php
/**
* The Cli App Class
* @package Mars
*/

namespace Mars;

/**
* The Cli App Class
*/
class Cli extends App
{
	/**
	* @var bool $is_cli True if the app is run as a cli script
	*/
	public $is_cli = true;

	/**
	* @see App::setUrls()
	*/
	protected function setUrls()
	{
		$this->assignUrls();
	}

	/**
	* Outputs a question and returns the answer from stdin
	* @param string $question The question
	* @return string The answer
	*/
	public static function ask(string $question) : string
	{
		echo $question . ': ';

		return read();
	}

	/**
	* Reads a line from stdin and returns it
	* @return string
	*/
	public static function read() : string
	{
		return trim(fgets(STDIN));
	}

	/**
	* Outputs a string
	* @param string $string The string to output
	* @param bool $newline If true will also output a newline
	*/
	public static function print(string $string, bool $newline = true)
	{
		echo $string;

		if ($newline) {
			echo "\n";
		}
	}

	/**
	* Outputs an error
	* @param string $error The error string to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	*/
	public static function printError(string $error, bool $newline = true, bool $die = false)
	{
		echo '******* ' . $error;

		if ($newline) {
			echo "\n";
		}

		if ($die) {
			die;
		}
	}

	/**
	* @see Cli::printError()
	*/
	public static function printErrorAndDie(string $error, bool $newline = true)
	{
		static::printError($error, $newline, true);
	}

	/**
	* Outputs a delimitator
	*/
	public static function printDel()
	{
		echo "-----------------------------------------------\n\n";
	}
}
