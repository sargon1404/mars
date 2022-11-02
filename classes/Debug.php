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
	public array $info = [];

	/**
	* Outputs all the debug info
	*/
	public function output()
	{
		echo '<div id="debug-info">';

		$this->app->plugins->run('debug_output_start', $this);

		$this->outputInfo();
		$this->outputExecutionTime();
		$this->outputOpcacheInfo();
		$this->outputPreloadInfo();

		$this->app->plugins->run('debug_output_middle', $this);

		$this->outputDbQueries();
		$this->outputPlugins();
		$this->outputLoadedTemplates();

		$this->app->plugins->run('debug_output_end', $this);

		echo '</div>';
	}

	/**
	* Outputs basic debug info
	*/
	protected function outputInfo()
	{
		echo '<table class="grid debug-grid">';
		echo '<tr><th colspan="3">Debug Info</th></tr>';
		echo '<tr><td><strong>Execution Time</strong></td><td>' . $this->info['execution_time'] . 's</td></tr>';
		echo '<tr><td><strong>Output Size</strong></td><td>' . $this->app->format->filesize($this->info['output_size']) . '</td></tr>';
		echo '<tr><td><strong>Memory Usage</strong></td><td>' . $this->app->format->filesize(memory_get_usage(true)) . '</td></tr>';
		echo '<tr><td><strong>Memory Peak Usage</strong></td><td>' . $this->app->format->filesize(memory_get_peak_usage(true)) . '</td></tr>';
		echo '<tr><td><strong>DB Queries</strong></td><td>' . count($this->app->db->queries) . '</td></tr>';
		echo '<tr><td><strong>Loaded Templates</strong></td><td>' . count($this->app->theme->getLoadedTemplates()) . '</td></tr>';
		echo '<tr><td><strong>Included Files</strong></td><td>' . count(get_included_files()) . '</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs execution time info
	*/
	protected function outputExecutionTime()
	{
		$execution_time = $this->info['execution_time'];
		$db_time = $this->app->db->queries_time;
		$plugins_time = $this->getPluginsExecTime();

		echo '<table class="grid debug-grid">';
		echo '<tr><th colspan="3">Execution Time</th></tr>';
		echo '<tr><td><strong>Execution Time</strong></td><td>' . $execution_time . 's</td><td></td></tr>';
		echo "<tr><td><strong>DB Queries</strong></td><td>{$db_time}s</td><td>" . $this->app->format->percentage($db_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Plugins</strong></td><td>{$plugins_time}s</td><td>" . $this->app->format->percentage($plugins_time, $execution_time) . '%</td></tr>';
		echo "<tr><td><strong>Generate Output</strong></td><td>{$this->info['output_content_time']}s</td><td>" . $this->app->format->percentage($this->info['output_content_time'], $execution_time) . '%</td></tr>';
		echo '</table><br><br>';
	}

	/**
	* Outputs mysql info
	*/
	protected function outputDbQueries()
	{
		$execution_time = $this->info['execution_time'];
		$db_time = $this->app->db->queries_time;

		echo '<table class="grid debug-grid debug-db-grid">';
		echo '<tr><th colspan="4">Queries</th></tr>';

		$i = 1;
		foreach ($this->app->db->queries as $query) {
			echo "<tr><td>{$i}</td><td><div class=\"debug-query\">" . $this->app->escape->html($query[0]) . '</div><div class="debug-query-params">' . $this->getDbQueryParams($query[1]) . "</div></td><td>{$query[2]}s</td><td>" . $this->app->format->percentage($query[2], $db_time) . '%</td></tr>';
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
	*/
	protected function outputPlugins()
	{
		$execution_time = $this->info['execution_time'];
		$plugins = $this->app->plugins->getPlugins();
		if (!$plugins) {
			return;
		}

		echo '<table class="grid debug-grid debug-grid-plugins">';
		echo '<tr><th colspan="3">Plugins</th></tr>';

		foreach ($plugins as $plugin) {
			if (!isset($this->app->plugins->exec_time[$plugin->name])) {
				continue;
			}

			$exec_time = $this->app->plugins->exec_time[$plugin->name];

			echo "<tr><td>" . $this->app->escape->html($plugin->title) . "</td><td>" . floatval($exec_time) . "</td><td>" . $this->app->format->percentage($exec_time, $execution_time) . '%</td></tr>';
		}

		echo '</table><br><br>';

		echo '<table class="grid debug-grid debug-grid-hooks" style="width:auto;">';
		echo '<tr><th colspan="3">Hooks Execution Time</th></tr>';

		foreach ($this->app->plugins->hooks_exec_time as $hook => $exec_time) {
			echo "<tr><td>" . $this->app->escape->html($hook) . "</td><td>" . floatval($exec_time) . "</td><td>" . $this->app->format->percentage($exec_time, $execution_time) . '%</td></tr>';
		}

		echo '</table><br><br>';

		if ($this->app->request->get->has('show-hooks')) {
			$hooks = $this->app->plugins->getHooks();
			echo '<table class="grid debug-grid debug-grid-hooks">';
			echo '<tr><th colspan="3">Hooks</th></tr>';

			foreach ($hooks as $hook => $hook_data) {
				echo "<tr><td>" . $this->app->escape->html($hook) . '</td></tr>';
			}

			echo '</table><br><br>';
		}
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
		echo '<table class="grid debug-grid debug-grid-templates">';
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

		echo '<table class="grid debug-grid debug-grid-opcache">';
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
			echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['used_memory']) . '</td></tr>';
			echo '<tr><td><strong>Memory: Free</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['free_memory']) . '</td></tr>';
			echo '<tr><td><strong>Memory: Wasted</strong></td><td>' . $this->app->format->filesize($info['memory_usage']['wasted_memory']) . '</td></tr>';
			echo '<tr><td><strong>Total Files</strong></td><td>' . count($files) . '</td></tr>';
			echo '<tr><td><strong>From Cache</strong></td><td>' . $from_cache . '</td></tr>';
			echo '<tr><td><strong>From Disk</strong></td><td>' . $from_disk . '</td></tr>';
		}

		echo '</table><br><br>';

		if ($uncached_files) {
			echo '<table class="grid debug-grid debug-grid-opcache-uncached">';
			echo '<tr><th>Files Read From Disk</th></tr>';
			foreach ($uncached_files as $file) {
				echo '<tr><td>' . $this->app->escape->html($file) . '</td></tr>';
			}
			echo '</table><br><br>';
		}
	}

	protected function outputPreloadInfo()
	{
		$info = opcache_get_status(true);

		echo '<table class="grid debug-grid preload-grid">';
		echo '<tr><th colspan="3">Preload Info</th></tr>';
		echo '<tr><td><strong>Enabled</strong></td><td>' . (isset($info['preload_statistics']) ? 'Yes' : 'No') . '</td></tr>';

		if (isset($info['preload_statistics'])) {
			if (isset($info['preload_statistics']['functions'])) {
				echo '<tr><td><strong>Preloaded Functions</strong></td><td>' . count($info['preload_statistics']['functions']) . '</td></tr>';
			}
			if (isset($info['preload_statistics']['classes'])) {
				echo '<tr><td><strong>Preloaded Scripts</strong></td><td>' . count($info['preload_statistics']['classes']) . '</td></tr>';
			}
			echo '<tr><td><strong>Memory: Used</strong></td><td>' . $this->app->format->filesize($info['preload_statistics']['memory_consumption']) . '</td></tr>';
		}

		echo '</table><br><br>';
	}

	/**
	* Outputs the memory usage
	* @param string $text Text to output before the memory usage
	* @param bool $die If true,will call die after the mem usage is printed
	*/
	public function outputMemoryUsage(string $text = '', bool $die = false)
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
	public function backtrace(int $options = DEBUG_BACKTRACE_IGNORE_ARGS)
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
	public function dump(bool $die = false)
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
	public function startTrace(string $filename, bool $show_params = true, int $params = 6)
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
	public function stopTrace()
	{
		xdebug_stop_trace();
	}

	/**
	* Starts the code coverage. Should be used in pair with getCoverage
	* Xdebug must be available
	*/
	public function startCoverage()
	{
		xdebug_start_code_coverage();
	}

	/**
	* Ends the code coverage. Should be used in pair with startCoverage
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public function getCoverage(bool $die = false)
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
	public function functionStackbool($die = false)
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
	public function printFunctionStack()
	{
		xdebug_get_function_stack();
	}

	/**
	* Dumps the headers
	* Xdebug must be available
	* @param bool $die If true, will call die after the data is printed
	*/
	public function headers(bool $die = false)
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
	public function get(bool $die = false)
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
