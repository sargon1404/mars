<?php
/**
* The Debug Class
* @package Mars
*/

namespace Mars;

/**
* The Debug Class
* Contains debug functionality and outputs debug info
*/
class Debug
{
	use AppTrait;

	/**
	* @var array $info Debug info
	*/
	public array

 $info = [];

	/**
	* Outputs all the debug info
	*/
	public function output()
	{
		echo '<div id="debug-info">';

		$execution_time = $this->app->timer->getExecutionTime();

		$this->outputInfo($execution_time);
		$this->outputExecutionTime($execution_time);

		$this->outputDbQueries($execution_time);

		$this->outputPlugins($execution_time);

		$this->outputLoadedTemplates();

		$this->outputOpcacheInfo();
		
		$this->outputPreloadInfo();

		echo '</div>';
	}

	/**
	* Outputs basic debug info
	* @param float $execution_time The total execution time
	*/
	protected function outputInfo(float $execution_time)
	{
		echo '<table class="grid debug-grid" style="width:auto;">';
		echo '<tr><th colspan="3">Debug Info</th></tr>';
		echo '<tr><td><strong>Execution Time</strong></td><td>' . $execution_time . 's</td></tr>';
		echo '<tr><td><strong>Output Size</strong></td><td>' . $this->app->format->size($this->info['output_size'] / 1024) . '</td></tr>';
		echo '<tr><td><strong>Memory Usage</strong></td><td>' . $this->app->format->size(memory_get_usage(true) / 1024) . '</td></tr>';
		echo '<tr><td><strong>Memory Peak Usage</strong></td><td>' . $this->app->format->size(memory_get_peak_usage(true) / 1024) . '</td></tr>';
		echo '<tr><td><strong>DB Queries</strong></td><td>' . count($this->app->db->queries) . '</td></tr>';
		echo '<tr><td><strong>Loaded Templates</strong></td><td>' . count($this->app->theme->getLoadedTemplates()) . '</td></tr>';
		echo '<tr><td><strong>Included Files</strong></td><td>' . count(get_included_files()) . '</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs execution time info
	* @param float $execution_time The total execution time
	*/
	protected function outputExecutionTime(float $execution_time)
	{
		$db_time = $this->app->db->queries_time;
		$plugins_time = $this->getPluginsExecTime();

		echo '<table class="grid debug-grid" style="width:auto;">';
		echo '<tr><th colspan="3">Execution Time</th></tr>';
		echo '<tr><td><strong>Execution Time</strong></td><td>' . $execution_time . 's</td><td></td></tr>';
		echo "<tr><td><strong>DB Queries</strong></td><td>{$db_time}s</td><td>" . $this->app->format->percentage($db_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Plugins</strong></td><td>{$plugins_time}s</td><td>" . $this->app->format->percentage($plugins_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Generate Output</strong></td><td>{$this->info['output_content_time']}s</td><td>" . $this->app->format->percentage($this->info['output_content_time'], $execution_time) . '%</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs mysql info
	* @param float $execution_time The total execution time
	*/
	protected function outputDbQueries(float $execution_time)
	{
		$db_time = $this->app->db->queries_time;

		echo '<table class="grid debug-grid debug-db-grid" style="width:100%;text-align:left">';
		echo '<tr><th colspan="4">Queries</th></tr>';

		$i = 1;
		foreach ($this->app->db->queries as $query) {
			echo "<tr><td>{$i}</td><td><div class=\"debug-query\">" . App::e($query[0]) . '</div><div class="debug-query-params">' . $this->getDbQueryParams($query[1]) . "</div></td><td>{$query[2]}s</td><td>" . $this->app->format->percentage($query[2], $db_time) . '%</td></tr>';
			$i++;
		}

		echo '</table><br><br>';
	}

	/**
	* Returns the query params, ready for outputing
	* @param array $params The query params
	* @return string
	*/
	protected function getDbQueryParams(array $params) : string
	{
		if (!$params) {
			return '';
		}

		return \json_encode($params);
	}

	/**
	* Outputs plugins debug info
	* @param float $execution_time The total execution time
	*/
	protected function outputPlugins(float $execution_time)
	{
		$plugins = $this->app->plugins->getPlugins();
		if (!$plugins) {
			return;
		}

		echo '<table class="grid debug-grid debug-grid-plugins" style="width:auto;">';
		echo '<tr><th colspan="3">Plugins</th></tr>';

		foreach ($plugins as $plugin) {
			$plugin_execution_time = $this->app->plugins->exec_time[$plugin->pid];

			echo "<tr><td>" . App::e($plugin->title) . "</td><td>" . floatval($plugin_execution_time) . "</td><td>" . $this->app->format->percentage($plugin_execution_time, $execution_time) . '%</td></tr>';
		}

		echo '</table><br><br>';
	}

	/**
	* Computes the execution time of the plugins
	* @return int
	*/
	protected function getPluginsExecTime() : float
	{
		$time = 0;
		if (!$this->app->plugins->exec_time) {
			return $time;
		}

		return array_sum($this->app->plugins->exec_time);
	}

	/**
	* Outputs info about the loaded templates
	*/
	protected function outputLoadedTemplates()
	{
		echo '<table class="grid debug-grid debug-grid-templates" style="width:auto;">';
		echo '<tr><th colspan="1">Loaded templates</th></tr>';
		echo '<tr><td class="left">';
		App::pp($this->app->theme->getLoadedTemplates(), false, false);
		echo '</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs opcache info
	*/
	protected function outputOpcacheInfo()
	{
		$info = opcache_get_status(true);

		echo '<table class="grid debug-grid" style="width:auto;">';
		echo '<tr><th colspan="3">Opcache Info</th></tr>';
		echo '<tr><td><strong>Enabled</strong></td><td>' . ($info['opcache_enabled'] ? 'Yes' : 'No') . '</td></tr>';

		if ($info['opcache_enabled']) {
			$files = get_included_files();
			$cached_files = $info['scripts'];

			$uncached_files = [];
			foreach ($files as $file) {
				if (isset($cached_files[$file])) {
					continue;
				}

				$uncached_files[] = $this->app->file->getRel($file);
			}

			$from_cache = count($files) - count($uncached_files);
			$from_disk = count($uncached_files);

			echo '<tr><td><strong>Cached Scripts</strong></td><td>' . $info['opcache_statistics']['num_cached_scripts'] . '</td></tr>';
			echo '<tr><td><strong>Cache Hits</strong></td><td>' . $info['opcache_statistics']['hits'] . '</td></tr>';
			echo '<tr><td><strong>Cache Misses</strong></td><td>' . $info['opcache_statistics']['misses'] . '</td></tr>';
			echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->size($info['memory_usage']['used_memory'] / 1024) . '</td></tr>';
			echo '<tr><td><strong>Memory: Free</strong></td><td>' . $this->app->format->size($info['memory_usage']['free_memory'] / 1024) . '</td></tr>';
			echo '<tr><td><strong>Memory: Wasted</strong></td><td>' . $this->app->format->size($info['memory_usage']['wasted_memory'] / 1024) . '</td></tr>';
			echo '<tr><td><strong>Total Files</strong></td><td>' . count($files) . '</td></tr>';
			echo '<tr><td><strong>From Cache</strong></td><td>' . $from_cache . '</td></tr>';
			echo '<tr><td><strong>From Disk</strong></td><td>' . $from_disk . '</td></tr>';
		}

		echo '</table><br><br>';

		if ($uncached_files) {
			echo '<table class="grid debug-grid debug-db-grid" style="width:100%;text-align:left">';
			echo '<tr><th>Files Read From Disk</th></tr>';
			foreach ($uncached_files as $file) {
				echo '<tr><td>' . App::e($file) . '</td></tr>';
			}
			echo '</table><br><br>';
		}
	}
	
	protected function outputPreloadInfo()
	{
		$info = opcache_get_status(true);

		echo '<table class="grid debug-grid" style="width:auto;">';
		echo '<tr><th colspan="3">Preload Info</th></tr>';
		echo '<tr><td><strong>Enabled</strong></td><td>' . (isset($info['preload_statistics']) ? 'Yes' : 'No') . '</td></tr>';
		
		if (isset($info['preload_statistics'])) {
			echo '<tr><td><strong>Preloaded Functions</strong></td><td>' . $info['preload_statistics']['functions'] . '</td></tr>';
			echo '<tr><td><strong>Preloaded Scripts</strong></td><td>' . $info['preload_statistics']['classes'] . '</td></tr>';
			echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->size($info['preload_statistics']['memory_consumption'] / 1024) . '</td></tr>';
		}
		
		echo '</table><br><br>';
	}

	/**
	* Outputs the memory usage
	* @param string $text Text to output before the memory usage
	* @param bool $die If true,will call die after the mem usage is printed
	*/
	public static function outputMemoryUsage(string $text = '', bool $die = false)
	{
		$usage = round(memory_get_usage(true) / 1048576, 4);
		$peak = round(memory_get_peak_usage(true) / 1048576, 4);

		$diff_usage = 0;
		$diff_peak = 0;
		if (isset($GLOBALS['debug_memory_usage'])) {
			$diff_usage = $usage - floatval($GLOBALS['debug_memory_usage']);
			$diff_peak = $peak - floatval($GLOBALS['debug_memory_peak']);
		}

		$GLOBALS['debug_memory_usage'] = $usage;
		$GLOBALS['debug_memory_peak'] = $peak;

		if ($text) {
			echo $text . '<br />';
		}

		echo 'Usage ' . $usage . 'MB  [<span style="color:#ff0000">' . $diff_usage . ' MB</span>]<br />';
		echo 'Peak ' . $peak . 'MB [<span style="color:green">' . $diff_peak . ' MB</span>]<hr />';

		if ($die) {
			die;
		}
	}

	/**
	* Outputs the backtrace
	* @param int $options The backtrace options. By default, the args are not printed. Set $options to 0 for the args to be shown
	*/
	public static function backtrace(int $options = DEBUG_BACKTRACE_IGNORE_ARGS)
	{
		echo '<pre>';
		debug_print_backtrace($options);
		die;
	}

	/**
	* Dumps the superglobals.
	* Xdebug must be available
	* @param bool $die If true,will call die after the mem usage is printed
	*/
	public static function dump(bool $die = false)
	{
		ini_set('xdebug.dump.GET', '*');
		ini_set('xdebug.dump.POST', '*');
		ini_set('xdebug.dump.FILES', '*');
		ini_set('xdebug.dump.COOKIES', '*');
		ini_set('xdebug.dump.SESSION', '*');
		ini_set('xdebug.dump.SERVER', '*');

		xdebug_dump_superglobals();

		if ($die) {
			die;
		}
	}

	/**
	* Starts a trace
	* Xdebug must be available
	* @param string $filename The filename where to save the trace
	* @param bool $show_params If true,will include the params
	* @param int $params The params count
	*/
	public static function startTrace(string $filename, bool $show_params = true, int $params = 6)
	{
		if ($show_params) {
			ini_set('xdebug.collect_params', $params);
		}

		xdebug_start_trace($filename);
	}

	/**
	* Stops a trace
	* Xdebug must be available
	*/
	public static function stopTrace()
	{
		xdebug_stop_trace();
	}

	/**
	* Starts the code coverage. Should be used in pair with getCoverage
	* Xdebug must be available
	*/
	public static function startCoverage()
	{
		xdebug_start_code_coverage();
	}

	/**
	* Ends the code coverage. Should be used in pair with startCoverage
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public static function getCoverage(bool $die = false)
	{
		\var_dump(xdebug_get_code_coverage());

		if ($die) {
			die;
		}
	}

	/**
	* Dumps the function stack
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public static function functionStackbool($die = false)
	{
		\var_dump(xdebug_get_function_stack());

		if ($die) {
			die;
		}
	}

	/**
	* Prints the function stack
	* Xdebug must be available
	*/
	public static function printFunctionStack()
	{
		xdebug_get_function_stack();
	}

	/**
	* Dumps the headers
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public static function headers(bool $die = false)
	{
		\var_dump(xdebug_get_headers());
		if ($die) {
			die;
		}
	}

	/**
	* Returns info about the current class/file/line/func
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public static function get(bool $die = false)
	{
		$class = xdebug_call_class();
		$file = xdebug_call_file();
		$line = xdebug_call_line();
		$func = xdebug_call_function();

		echo $file . '<br />';
		if ($class) {
			echo $class . '<br />';
		}
		echo $func . '<br />';
		echo $line . '<hr />';

		if ($die) {
			die;
		}
	}
}
