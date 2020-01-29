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
	* @param array $colors Array defining the user colors
	*/
	public array $colors = [
		'default' => '0',
		'message' => '0',
		'error' => '91',
		'warning' => '93',
		'info' => '32',
		'header' => '0;33',
		'list_1' => '0;32',
		'list_2' => '0'
	];
	
	/**
	* @param array $argv List of commands
	*/
	protected array $commands = [];

	/**
	* @param array $options List of options
	*/
	protected array $options = [];
	
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
			} elseif (strpos($option, '-') === 0) {
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
	* Returns a color, based on type
	* @param string $type The type of the color
	* @return string The color
	*/
	public function getColor(string $type) : string
	{
		return $this->colors[$type] ?? $this->colorts['default'];
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
		if (!$this->commands) {
			return '';
		}
		
		return $this->commands[0];
	}

	/**
	* Returns the main command action
	* @return string
	*/
	public function getCommandAction() : string
	{
		$slice = array_slice($this->commands, 1);
		
		return implode(':', $slice);
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
		if ($color != '') {
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
	* Outputs a header
	* @param string $text The text to output
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @return $this
	*/
	public function header(string $text, bool $newline = true, bool $die = false)
	{
		return $this->print($text, $this->colors['header'], $newline, $die);
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
		return $this->print($text, $this->colors['message'], $newline, $die);
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
		return $this->print($text, $this->colors['error'], $newline, $die);
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
		static::print($text, $this->colors['warning'], $newline, $die);
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
		static::print($text, $this->colors['info'], $newline, $die);
	}
	
	/**
	* Prints a list on two columns
	* @param array $data The data to print in the format ['header1' => [['col1', 'col2'],['col1', 'col2']], 'header2' => ..]
	* @param bool $headers_show If true, will show the headers
	* @param string $headers_color The color of the headers
	* @param string $col1_color The color of the 1st column
	* @param string $col2_color The color of the 2nd column
	* @return $this
	*/
	public function list(array $data, bool $headers_show = true, string $headers_color = '', string $col1_color = '', string $col2_color = '', int $col_1_left_pad = 3, int $col_2_left_pad = 15)
	{
		if (!$headers_color) {
			$headers_color = $this->colors['header'];
		}
		if (!$col1_color) {
			$col1_color = $this->colors['list_1'];
		}
		if (!$col2_color) {
			$col2_color = $this->colors['list_2'];
		}
		
		$max_length_1 = $this->getMaxLength($data, 0) + $col_2_left_pad;
		$max_length_2 = $this->getMaxLength($data, 1);
		
		foreach ($data as $header => $list) {
			if ($headers_show) {
				$this->print($header, $headers_color);
			}
			
			foreach ($list as $item) {
				$this->print($this->padString($item[0], $max_length_1, $col_1_left_pad), $col1_color, false);
				$this->print($this->padString($item[1], $max_length_2), $col2_color, false);
				echo "\n";
			}
		}
		
		return $this;
	}
	
	/**
	* Pads a string to match a certain length
	* @param string $str The string to pad
	* @param int $pad_length The max length
	* @param int $pad_length_left If specified, will add $pad_length_left chars to the left
	* @return string The padded string
	*/
	protected function padString(string $str, int $pad_length, int $pad_length_left = 0) : string
	{
		$str = str_pad($str, $pad_length);
		if ($pad_length_left) {
			$str = str_pad($str, strlen($str) + $pad_length_left, ' ', STR_PAD_LEFT);
		}
		
		return $str;
	}
	
	/**
	* Returns the max length of a column
	* @param array $data The data where to look for the max length
	* @param int $index The index of the column
	* @return int The max length
	*/
	protected function getMaxLength(array $data, int $index) : int
	{
		$max_length = 0;

		foreach ($data as $list) {
			foreach ($list as $item) {
				$length = strlen($item[$index]);
				if ($length > $max_length) {
					$max_length = $length;
				}
			}
		}
		
		return $max_length;
	}

	/**
	* Outputs a delimitator
	*/
	public function del()
	{
		echo "-----------------------------------------------\n\n";
	}
}
