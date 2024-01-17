<?php
/**
* The Bin Class
* @package Mars
*/

namespace Mars;

/**
 * The Bin Class
 */
class Bin
{
	use AppTrait;

	/**
	 * @param array $colors Array defining the user colors
	 */
	public array $colors = [
		'' => '0',
		'default' => '0',
		'message' => '0',
		'error' => '0;41',
		'warning' => '93',
		'info' => '32',
		'header' => '0;33',
		'list_1' => '0;32',
		'list_2' => '0',
		'white' => '1;37',
		'black' => '0;30',
		'grey' => '1;30',
		'light_grey' => '0;37',
		'red' => '0;31',
		'light_red' => '1;31',
		'green' => '0;32',
		'light_green' => '1;32',
		'brown' => '0;33',
		'yellow' => '1;33',
		'blue' => '0;34',
		'ligth_blue' => '1;34',
		'magenta' => '0;35',
		'cyan' => '1;36',
	];

	/**
	 * @var Handlers $handlers The handlers object
	 */
	public readonly Handlers $handlers;

	/**
	 * @param array $argv List of arguments
	 */
	protected array $argv = [];

	/**
	 * @param array $commands List of commands
	 */
	protected array $commands = [];

	/**
	 * @param array $options List of options
	 */
	protected array $options = [];

	/**
	 * @param string $newline The newline
	 */
	protected string $newline = "\n";

	/**
	 * @var array $supported_handlers The list of supported handlers
	 */
	protected array $supported_handlers = [
		'list' => '\Mars\Bin\Listing',
		'list_multi' => '\Mars\Bin\ListingMulti',
		'table' => '\Mars\Bin\Table'
	];

	/**
	 * Builds the Bin object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		global $argv;
		$this->app = $app;
		$this->handlers = new Handlers($this->supported_handlers, $this->app);
		$this->argv = $argv ?? [];

		if (isset($argv[1])) {
			$this->commands = $this->getCommands();
			$this->options = $this->getOptions();
		}

		if (!$this->app->is_bin) {
			$this->newline = '<br>';
		}
	}

	/**
	 * Returns the arguments list, from CLI arguments
	 * @return array
	 */
	public function getArgv() : array
	{
		return $this->argv;
	}

	/**
	 * Returns an argument value
	 * @return string The argument value
	 */
	public function getArg(int $index) : string
	{
		return $this->argv[$index + 1] ?? '';
	}

	/**
	 * Returns the commands list, from CLI arguments
	 * @return array
	 */
	public function getCommands() : array
	{
		global $argv;
		if ($this->commands) {
			return $this->commands;
		}

		$commands = [];
		foreach ($argv as $i => $option) {
			if (!$i || str_starts_with($option, '-')) {
				continue;
			}

			$commands = explode(':', $option);
			break;
		}

		return $commands;
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
	 * Returns the options, from CLI arguments
	 * @return array
	 */
	public function getOptions() : array
	{
		global $argv;
		if ($this->options) {
			return $this->options;
		}

		$options = [];
		foreach ($argv as $option) {
			if (str_starts_with($option, '--')) {
				$parts = explode('=', substr($option, 2));
				$name = $parts[0];
				$value = $parts[1] ?? '';

				$options[$name] = $value;
			} elseif (str_starts_with($option, '-')) {
				$name = substr($option, 1);
				$options[$name] = true;
			}
		}

		return $options;
	}

	/**
	 * Returns the value of a command line option
	 * @param string $name The name of the option
	 * @return string The option
	 */
	public function getOption(string $name) : ?string
	{
		return $this->options[$name] ?? null;
	}

	/**
	 * Returns true if a command line option has been defined
	 * @param string $name The name of the option
	 * @return bool
	 */
	public function hasOption(string $name) : bool
	{
		return isset($this->options[$name]);
	}

	/**
	 * Returns a color, based on type
	 * @param string $color The color
	 * @return string The color
	 */
	public function getColor(string $color) : string
	{
		return $this->colors[$color] ?? $color;
	}

	/****************STDIN/STDOUT***********************************/

	/**
	 * Outputs a question and returns the answer from stdin
	 * @param string $question The question
	 * @return string The answer
	 */
	public function ask(string $question) : string
	{
		echo $question . ': ';

		return $this->read();
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
	 * Outputs a newline
	 */
	public function printNewline()
	{
		echo $this->newline;
	}

	/**
	 * Prints a text, by repeating $text
	 * @param string $text The text to print
	 * @param string $color The color to print the text with
	 * @param bool $newline If true will also output a newline
	 */
	public function printRepeat(string $text, int $repeat, string $color = '', bool $newline = true)
	{
		$this->print(str_repeat($text, $repeat), $color, $newline);
	}

	/**
	 * Outputs a delimitator
	 * @param int $chars The number of chars to print
	 */
	public function printDel(int $chars = 60)
	{
		$this->printRepeat('-', $chars);
	}

	/**
	 * Outputs text
	 * @param string $text The text to output
	 * @param string $color The color to print the text with
	 * @param bool $newline If true will also output a newline
	 * @return static
	 */
	public function print(string $text, string $color = '', bool $newline = true) : static
	{
		if ($color) {
			$color = $this->getColor($color);
			echo "\e[{$color}m{$text}\e[0m";
		} else {
			echo $text;
		}

		if ($newline) {
			echo $this->newline;
		}

		return $this;
	}

	/**
	 * Outputs a header
	 * @param string $text The text to output
	 * @return static
	 */
	public function header(string $text) : static
	{
		return $this->print($text, $this->colors['header']);
	}


	/**
	 * Outputs a message
	 * @param string $text The text to output
	 * @return static
	 */
	public function message($text) : static
	{
		$this->print($text, $this->colors['message']);

		return $this;
	}

	/**
	 * Outputs an error and dies
	 * @param string $text The text to output
	 * @param bool $die If true, will exit the script after outputing the error
	 */
	public function error($text, bool $die = true)
	{
		echo "\n";
		$this->print($text, $this->colors['error']);
		echo "\n";

		if ($die) {
			die;
		}
	}

	/**
	 * Outputs a warning
	 * @param string $text The text to output
	 * @return static
	 */
	public function warning(string $text) : static
	{
		$this->print($text, $this->colors['warning']);

		return $this;
	}

	/**
	 * Outputs an info string
	 * @param string $text The text to output
	 * @return static
	 */
	public function info(string $text) : static
	{
		$this->print($text, $this->colors['info']);

		return $this;
	}

	/**
	 * Prints a list
	 * @param array $data The data to print
	 * @param array $colors The colors to use
	 * @param array $paddings_right The number of left chars to apply, if any
	 * @param array $paddings_left The number of left chars to apply, if any
	 * @return static
	 */
	public function printList(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = []) : static
	{
		$handler = $this->handlers->get('list');
		$handler->print($data, $colors, $paddings_right, $paddings_left);

		return $this;
	}

	/**
	 * Prints a list, with multiple sections
	 * @param array $data The data to print
	 * @param array $colors The colors to use
	 * @param array $paddings_right The number of left chars to apply, if any
	 * @param array $paddings_left The number of left chars to apply, if any
	 * @return static
	 */
	public function printListMulti(array $data, array $colors = [], array $paddings_right = [], array $paddings_left = []) : static
	{
		$handler = $this->handlers->get('list_multi');
		$handler->print($data, $colors, $paddings_right, $paddings_left);

		return $this;
	}

	/**
	 * Prints a table
	 * @param array $headers The header data
	 * @param array $data The data to print
	 * @param array $colors The colors to use. $colors[0] is the header's color
	 * @param array $align Determines how the headers/cells are align. $align[0] is the header's alignment
	 * @param array $paddings_left The number of left chars to apply, if any
	 * @param array $paddings_right The number of left chars to apply, if any
	 * @return static
	 */
	public function printTable(array $headers, array $data, array $colors = [], array $align = [], array $paddings_left = [], array $paddings_right = []) : static
	{
		$handler = $this->handlers->get('table');
		$handler->print($headers, $data, $colors, $align, $paddings_left, $paddings_right);

		return $this;
	}
}
