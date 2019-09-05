<?php
/**
* The Cli Class
* @package Mars
*/

namespace Mars;

/**
* The Cli Class
*/
class Cli
{
	/**
	* @param array $argv List of commands
	*/
	protected $commands = [];
	/**
	* @param array $options List of options
	*/
	protected $options = [];


	/**
	* Builds the CLI object
	*/
	public function __construct()
	{
		global $argv;

		if (!isset($argv[1])) {
			return;
		}

		$this->commands = explode(':', $argv[1]);

		if (count($argv) > 2) {
			$options = array_slice($argv, 2, count($argv) - 2);

			$this->parseOptions($options);
		}
	}

	/**
	* Parses the command line options
	* @param array $options The options to parse
	*/
	protected function parseOptions(array $options)
	{
		foreach ($options as $option) {
			$str = '';

			if (strpos($option, '--') === 0) {
				$str = substr($option, 2);
			} else if (strpos($option, '-') === 0) {
				$str = substr($option, 1);
			}

			$parts = explode('=', $str);
			$name = $parts[0];
			$value = $parts[1] ?? '';

			if (!$value) {
				$value = true;
			}

			$this->options[$name] = $value;
		}
	}
	
	/**
	* Returns the options
	* @return array The options
	*/
	public function getOptions() : array
	{
		return $this->options;	
	}
	
	/**
	* Alias for getOptions
	* @see \Mars\Cli::getOptions()
	*/
	public function getArgvs() : array
	{
		return $this->getOptions();
	}

	/**
	* Returns the value of a command line option
	* @param string $name The name of the option
	* @return string The option
	*/
	public function getOption(string $name) : string
	{
		if (!isset($this->options[$name])) {
			return '';
		}

		return $this->options[$name];
	}

	/**
	* Returns true if a command line option has been defined
	* @param string $name The name of the option
	* @return bool
	*/
	public function isOption(string $name) : bool
	{
		if (!isset($this->options[$name])) {
			return false;
		}

		return true;
	}

	/**
	* Returns the main command name
	* @return string
	*/
	public function getCommandName() : string
	{
		return $this->getCommandString(0);
	}

	/**
	* Returns the main command action
	* @return string
	*/
	public function getCommandAction() : string
	{
		return $this->getCommandString(1);
	}

	/**
	* Returns a command string, by index
	* @param int $index The index
	*/
	public function getCommandString(int $index) : string
	{
		if (empty($this->commands[$index])) {
			return '';
		}

		return $this->commands[$index];
	}

	/**
	* Outputs a question and returns the answer from stdin
	* @param string $question The question
	* @return string The answer
	*/
	public function ask(string $question) : string
	{
		echo $question . ': ';

		return read();
	}

	/**
	* Reads a line from stdin and returns it
	* @return string
	*/
	public function read() : string
	{
		return trim(fgets(STDIN));
	}

	/**
	* Outputs a text message
	* @param string $text The text to output
	* @param string $color The color to print the text with
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @return $this
	*/
	public function print(string $text, string $color = '', bool $newline = true, bool $die = false)
	{
		if ($color) {
			echo "\e[{$color}m";
		}

		echo $text;

		if ($newline) {
			echo "\n";
		}

		if ($die) {
			die;
		}
		
		return $this;
	}
	
	/**
	* Outputs a message
	* @param string $text The text to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @return $this
	*/
	public function message(string $text, bool $newline = true, bool $die = false)
	{
		return $this->print($text, $newline, $die);
	}

	/**
	* Outputs an error
	* @param string $text The text to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	* @return $this
	*/
	public function error(string $text, bool $newline = true, bool $die = false)
	{
		return $this->print($text, '91', $newline, $die);
	}
	
	/**
	* @see Cli::error()
	*/
	public function errorAndDie(string $text, bool $newline = true)
	{
		static::error($text, $newline, true);
	}

	/**
	* Outputs a warning
	* @param string $text The text to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	* @return $this
	*/
	public function warning(string $text, bool $newline = true, bool $die = false)
	{
		static::print($text, '93', $newline, $die);
	}

	/**
	* Outputs an info string
	* @param string $text The text to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	* @return $this
	*/
	public function info(string $text, bool $newline = true, bool $die = false)
	{
		static::print($text, '32', $newline, $die);
	}

	/**
	* Outputs a delimitator
	*/
	public function del()
	{
		echo "-----------------------------------------------\n\n";
	}
}