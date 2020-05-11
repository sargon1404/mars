<?php
/**
* The Log Class
* @package Mars
*/

namespace Mars;

/**
* The Log Class
* The system's log object
*/
class Log
{
	use AppTrait;

	/**
	* @var string $suffix The log file's suffix
	*/
	public string $suffix = '';

	/**
	* @var string $date The log date
	*/
	public string $date = '';

	/**
	* @var array $handles The log files's handles
	*/
	protected array $handles = [];

	/**
	* @var bool $is_cli If true, will use the cli log file
	*/
	protected bool $is_cli = false;

	/**
	* Builds the log objects
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->is_cli = $this->app->is_cli;

		$ext = '.php';
		if ($this->is_cli) {
			$ext = '.cli.php';
		}

		$this->suffix = date('d-F-Y') . '.php';
		$this->date = date('d-m-Y H:i:s');

		set_error_handler([$this, 'handleError'], $this->app->config->log_error_types);
	}

	/**
	* Destroys the log objects. Closes the log file's handle
	*/
	public function __destruct()
	{
		if ($this->handles) {
			foreach ($this->handles as $handle) {
				fclose($handle);
			}
		}
	}

	/**
	* Logs a string by using a basic format
	* @param string $type The log type. Eg: error,warning,info. Any string can be used as type
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	*/
	public function log(string $type, string $str, string $file = '', string $line = '')
	{
		if (!isset($this->handles[$type])) {
			$this->start($type);
		}

		$text = "{$str} ({$this->date})";
		if ($file || $line) {
			$text.= "[{$file}:{$line}]";
		}
		$text.= "\n";

		fwrite($this->handles[$type], $text);
	}

	/**
	* Logs a string by using an extended format
	* @param string $type The log type. Eg: error,warning,info. Any string can be used as type
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	*/
	public function logExtended(string $type, string $str, string $file = '', string $line = '')
	{
		if (!isset($this->handles[$type])) {
			$this->start($type);
		}

		$text = '[DATE: ' . $this->date . ']' . "\n";
		if (!$this->is_cli) {
			$text.= '[URL: ' . $this->app->full_url . ']' . "\n";
		}

		if ($file) {
			$text.= '[FILE: ' . $file . ']';
		}
		if ($line) {
			$text.= '[LINE: ' . $line . ']';
		}
		if ($file || $line) {
			$text.= "\n";
		}

		$text.= "\n" . $str . "\n\n";
		$text.= "------------------------\n\n";

		fwrite($this->handles[$type], $text);
	}

	/**
	* Starts/Creates the log file, if it doesn't exist
	* @param string $type The log type
	*/
	protected function start(string $type)
	{
		$filename = $this->app->log_dir . basename($type) . '-' . $this->suffix;

		$exists = false;
		if (is_file($filename)) {
			$exists = true;
		}

		$this->handles[$type] = fopen($filename, 'a');
		if (!$this->handles[$type]) {
			throw new \Exception('Error writing the log file. Please make sure the log folder is writeable');
		}

		if (!$exists) {
			fwrite($this->handles[$type], '<?php die; ?>' . "\n");
		}
	}

	/**
	* Callback for set_error_handler
	* @internal
	*/
	public function handleError(string $err_no, string $err_str, string $err_file, string $err_line) : bool
	{
		$str = $err_str . "\n";
		$this->error($str, $err_file, $err_line);

		return false;
	}

	/**
	* Logs an error
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	* @return $this
	*/
	public function error(string $str, string $file = '', string $line = '')
	{
		$this->logExtended('errors', $str, $file, $line);
	}

	/**
	* Logs a message
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	* @return $this
	*/
	public function message(string $str, string $file = '', string $line = '')
	{
		$this->log('message', $str, $file, $line);
	}

	/**
	* Logs a warning
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	* @return $this
	*/
	public function warning(string $str, string $file = '', string $line = '')
	{
		$this->log('warnings', $str, $file, $line);
	}

	/**
	* Logs an info
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	* @return $this
	*/
	public function info(string $str, string $file = '', string $line = '')
	{
		$this->log('info', $str, $file, $line);
	}

	/**
	* Logs a a system message
	* @param string $str The string to log
	* @param string $file The file in which the logging occured Shold be __FILE__
	* @param string $line The line where the logging occured. Should be __LINE__
	* @return $this
	*/
	public function system(string $str, string $file = '', string $line = '')
	{
		$this->log('system', $str, $file, $line);
	}
}
