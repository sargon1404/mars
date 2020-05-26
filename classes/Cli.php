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
	use AppTrait;

	/**
	* @param array $colors Array defining the user colors
	*/
	public array $colors = [
		'default' => '0',
		'message' => '0',
		'error' => '0;41',
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
	* @param array $options_list List of options, passed without a name
	*/
	protected array $arguments = [];

	/**
	* @param array $options List of options
	*/
	protected array $options = [];

	/**
	* List of missing options, when calling checkOptions
	*/
	protected array $options_missing = [];

	/**
	* Builds the CLI object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		global $argv;
		$this->app = $app;

		if (!isset($argv[1])) {
			return;
		}

		$this->commands = explode(':', $argv[1]);

		if (count($argv) > 2) {
			$options = array_slice($argv, 2, count($argv) - 2);

			$this->parseOptions($options);
		}

		$this->app->plugins->run('cli_construct', $this);
	}

	/**
	* Parses the command line options
	* @param array $options The options to parse
	*/
	protected function parseOptions(array $options)
	{
		foreach ($options as $option) {
			if (str_starts_with($option, '--')) {
				$parts = explode('=', substr($option, 2));
				$name = $parts[0];
				$value = $parts[1] ?? '';

				$this->options[$name] = $value;
			} elseif (str_starts_with($option, '-')) {
				$name = substr($option, 1);
				$this->options[$name] = true;
			} else {
				$this->arguments[] = $option;
			}
		}
	}

	/**
	* Returns the options
	* @param array $list If specified, will only return the options matching the list
	* @return array The options
	*/
	public function getOptions(array $list = []) : array
	{
		if (!$list) {
			return $this->options;
		}

		$options = [];
		foreach ($list as $option) {
			$names = App::getArray($option);

			foreach ($names as $name) {
				if (isset($this->options[$name])) {
					$options[$name] = $this->options[$name];
				}
			}
		}

		return $options;
	}

	/**
	* Returns the arguments list
	* @param int $size The number of expected arguments
	* @return array The arguments list
	*/
	public function getArguments(int $size) : array
	{
		if ($size) {
			return array_pad($this->arguments, $size, '');
		}

		return $this->arguments;
	}

	/**
	* Returns the first argument
	* @return string The first argument
	*/
	public function getArgument() : string
	{
		$arguments = $this->getArguments(1);

		return reset($arguments);
	}

	/**
	* Alias for getArguments
	* @see \Mars\Cli::getArguments()
	*/
	public function getArgvs(int $size) : array
	{
		return $this->getArguments($size);
	}

	/**
	* Checks that the right number of arguments have been passed
	* @param int $size The
	* @return bool
	*/
	public function checkArguments(int $size) : bool
	{
		if (count($this->arguments) == $size) {
			return true;
		}

		return false;
	}

	/**
	* Returns the value of a command line option
	* @param array|string $name The name of the option. String or array
	* @return string The option
	*/
	public function getOption($name) : ?string
	{
		$names = App::getArray($name);

		foreach ($names as $name) {
			if (isset($this->options[$name])) {
				return $this->options[$name];
			}
		}

		return null;
	}

	/**
	* Returns true if a command line option has been defined
	* @param array|string $name The name of the option. String or array
	* @return bool
	*/
	public function isOption($name) : bool
	{
		$names = App::getArray($name);

		foreach ($names as $name) {
			if (isset($this->options[$name])) {
				return true;
			}
		}

		return false;
	}

	/**
	* Checks if the specified options are found
	* @param array $options The options to check
	* @return bool Returns true if all options are found
	*/
	public function checkOptions(array $options) : bool
	{
		$this->options_missing = [];
		$found = true;

		foreach ($options as $field => $option) {
			$params = App::toArray($option);
			$param_found = false;

			foreach ($params as $name) {
				if (isset($this->options[$name])) {
					$param_found = true;
					break;
				}
			}

			if (!$param_found) {
				$this->options_missing[] = $field;
				$found = false;
			}
		}

		return $found;
	}

	/**
	* Returns an array listing the missing options, if checkOptions returned false
	* @return array
	*/
	public function getOptionsMissing() : array
	{
		return $this->options_missing;
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
	* Returns a color, based on type
	* @param string $type The type of the color
	* @return string The color
	*/
	public function getColor(string $type) : string
	{
		return $this->colors[$type] ?? $this->colorts['default'];
	}

	/**
	* Determines the max line length from multiple lines of text
	* @param array $lines The lines
	* @return int The max length line
	*/
	protected function getMaxLineLength(array $lines) : int
	{
		$max_length = 0;

		foreach ($lines as $line) {
			$length = strlen($line);
			if ($length > $max_length) {
				$max_length = $length;
			}
		}


		return $max_length;
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
	* @param string $text The text to output. String or array for multiple lines
	* @param string|array  $color The color to print the text with
	* @param int $pad_left The number of spaces to prefix $text with
	* @param string $prefix Prefix to print before the text, if any
	* @param string $suffix Suffix to add after text, if any
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @param int $empty_right The number of empty chars to add to the right, if a background is specified
	* @return $this
	*/
	public function print($text, string $color = '', int $pad_left = 0, string $prefix = '', string $suffix = '', bool $newline = true, bool $die = false, int $empty_right = 5)
	{
		if (is_array($text)) {
			$text_array = [];
			foreach ($text as $string) {
				if ($pad_left) {
					$string = $this->padStringLeft($string, $pad_left);
				}

				$text_array[] = $string;
			}

			$text = implode("\n", $text_array);
		} else {
			if ($pad_left) {
				$text = $this->padStringLeft($text, $pad_left);
			}
		}

		if ($color) {
			//if a background might be specified, replace the newlines with empty spaces
			$string = $prefix . $text . $suffix;

			$lines = explode("\n", $string);
			$lines_count = count($lines);
			$max_length = $this->getMaxLineLength($lines) + $pad_left + $empty_right;

			$i = 1;
			foreach ($lines as $line) {
				$length = strlen($line);
				$empty = str_repeat(' ', $max_length - $length);

				echo "\e[{$color}m";
				echo $line, $empty;
				echo "\e[0m";

				if ($i < $lines_count) {
					echo "\n";
				}

				$i++;
			}
		} else {
			echo $prefix, $text, $suffix;
		}

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
	* @param int $pad_left The number of spaces to prefix $text with
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @return $this
	*/
	public function header(string $text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		return $this->print($text, $this->colors['header'], $pad_left, '', '', $newline, $die);
	}

	/**
	* Outputs a message
	* @param string $text The text to output. String or array for multiple lines
	* @param int $pad_left The number of spaces to prefix $text with
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the string
	* @return $this
	*/
	public function message($text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		echo "\n";
		$this->print($text, $this->colors['message'], $pad_left, "\n\n", "\n\n", $newline);
		echo "\n";

		if ($die) {
			die;
		}

		return $this;
	}

	/**
	* Outputs an error and dies
	* @param string $text The text to output. String or array for multiple lines
	* @param bool $newline If true will also output a newline
	*/
	public function error($text, bool $newline = true)
	{
		echo "\n";
		$this->print($text, $this->colors['error'], 5, "\n\n", "\n\n", $newline);
		echo "\n";

		die;
	}

	/**
	* Outputs a warning
	* @param string $text The text to output. String or array for multiple lines
	* @param int $pad_left The number of spaces to prefix $text with
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	* @return $this
	*/
	public function warning(string $text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		echo "\n";
		$this->print($text, $this->colors['warning'], $pad_left, "\n\n", "\n\n", $newline);
		echo "\n";

		if ($die) {
			die;
		}

		return $this;
	}

	/**
	* Outputs an info string
	* @param string $text The text to output. String or array for multiple lines
	* @param int $pad_left The number of spaces to prefix $text with
	* @param bool $newline If true will also output a newline
	* @param bool $die If true, will die after printing the error
	* @return $this
	*/
	public function info(string $text, int $pad_left = 0, bool $newline = true, bool $die = false)
	{
		echo "\n";
		$this->print($text, $this->colors['info'], $pad_left, "\n\n", "\n\n", $newline);
		echo "\n";

		if ($die) {
			die;
		}

		return $this;
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

		$data_count = count($data);
		$i = 1;
		foreach ($data as $header => $list) {
			if ($headers_show) {
				$this->print($header, $headers_color);
			}

			foreach ($list as $item) {
				$this->print($this->padString($item[0], $max_length_1), $col1_color, $col_1_left_pad, '', '', false);
				$this->print($this->padString($item[1], $max_length_2), $col2_color, $col_2_left_pad, '', '', false);
				echo "\n";
			}

			if ($i < $data_count) {
				echo "\n";
			}

			$i++;
		}

		return $this;
	}

	/**
	* Prefixes the string with empty spaces
	* @param string $str The string to pad
	* @param int $pad_length The spaces to prefix the string with
	* @return string The padded string
	*/
	public function padStringLeft(string $str, int $pad_length) : string
	{
		return str_pad($str, strlen($str) + $pad_length, ' ', STR_PAD_LEFT);
	}

	/**
	* Pads the string to the right with empty spaces
	* @param string $str The string to pad
	* @param int $pad_length The spaces to prefix the string with
	* @return string The padded string
	*/
	public function padStringRight(string $str, int $pad_length) : string
	{
		return str_pad($str, $pad_length);
	}

	/**
	* Pads a string to match a certain length
	* @param string $str The string to pad
	* @param int $pad_length The max length
	* @param int $pad_length_left If specified, will add $pad_length_left chars to the left
	* @return string The padded string
	*/
	public function padString(string $str, int $pad_length, int $pad_length_left = 0) : string
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
