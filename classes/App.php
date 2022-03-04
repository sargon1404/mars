<?php
/**
* The App Class
* @package Mars
*/

namespace Mars;

/**
* The App Class
* The system's main object
*/
class App
{
	/**
	* @var float $version The version
	*/
	public string $version = '1.00';

	/**
	* @var bool $is_bin True if the app is run as a bin script
	*/
	public bool $is_bin = false;

	/**
	* @var bool $is_https True if the page is loaded with https, false otherwise
	*/
	public bool $is_https = false;

	/**
	* @var string $scheme The page's scheme: http:// or https://
	*/
	public string $scheme = '';

	/**
	* @var string $method The request method. get/post.
	*/
	public string $method = '';

	/**
	* @var string $protocol The server protocol
	*/
	public string $protocol = '';

	/**
	* @var bool $is_https2 True if the page is loaded using HTTP/2
	*/
	public bool $is_http2 = false;

	/**
	* @var string $url The url. Eg: http://mydomain.com/mars
	*/
	public string $url = '';

	/**
	* @var string $url_static The url from where static content is served
	*/
	public string $url_static = '';

	/**
	* @var string $full_url The url of the current page determined from $_SERVER. Includes the QUERY_STRING
	*/
	public string $full_url = '';

	/**
	* @var string $ip The ip used to make the request
	*/
	public string $ip = '';

	/**
	* @var string $useragent The useragent
	*/
	public string $useragent = '';

	/**
	* @var bool $accepts_gzip If true, the user's browser accepts gzipped output
	*/
	public bool $accepts_gzip = false;

	/**
	* @var bool $can_gzip True, if content can be gzipped
	*/
	public bool $can_gzip = false;

	/**
	* @var bool $development If true, the system is run in development mode
	*/
	public bool $development = false;

	/**
	* @var string $path The location on the disk where the site is installed Eg: /var/www/mysite
	*/
	public string $path = '';

	/**
	* @var string $log_path The folder where the log files are stored
	*/
	public string $log_path = '';

	/**
	* @var string $tmp_path The folder where the temporary files are stored
	*/
	public string $tmp_path = '';

	/**
	* @var string $cache_path The folder where the cache files are stored
	*/
	public string $cache_path = '';

	/**
	* @var string $libraries_path The folder where the php libraries are stored
	*/
	public string $libraries_path = '';

	/**
	* @var string $extensions_path The folder where the extensions are stored
	*/
	public string $extensions_path = '';

	/**
	* @var string $extensions_url The url of the extensions folder
	*/
	public string $extensions_url = '';

	/**
	* @var string $content The system's generated content
	*/
	public string $content = '';







	/**
	* @var Config $config The config object
	*/
	public Config $config;

	/**
	* @var Cache $cache The cache object
	*/
	public Cache $cache;

	/**
	* @var Caching $caching The caching object
	*/
	public Caching $caching;

	/**
	* @var Db $db The Db object
	*/
	public Db $db;

	/**
	* @var Escape $escape The escape object
	*/
	public Escape $escape;

	/**
	* @var Filter $filter The filter object
	*/
	public Filter $filter;

	/**
	* @var Format $format The format object
	*/
	public Format $format;

	/**
	* @var Json $json The json object
	*/
	public Json $json;

	/**
	* @var Memcache $memcache The memcache object
	*/
	public Memcache $memcache;

	/**
	* @var Random $random The random object
	*/
	public Random $random;

	/**
	* @var Serializer $serializer The serializer object
	*/
	public Serializer $serializer;

	/**
	* @var Time $time The time object
	*/
	public Time $time;

	/**
	* @var Timer $timer The timer object
	*/
	public Timer $timer;

	/**
	* @var Sql $sql The SQL object
	*/
	public Sql $sql;

	/**
	* @var Unescape $unescape The unescape object
	*/
	public Unescape $unescape;

	/**
	* @var Uri $uri The uri object
	*/
	public Uri $uri;

	/**
	* @var Validator $validator The validator object
	*/
	public Validator $validator;

	/**
	* @var string $namespace The root namespace
	*/
	public string $namespace = "App\\";

	/**
	* @var string $extensions_namespace The root namespace for extensions
	*/
	public string $extensions_namespace = "App\\Extensions\\";

	/**
	* @var App $instance The app instance
	*/
	protected static App $instance;

	/**
	* @var AppBooter $boot The booter object
	*/
	protected AppBooter $boot;

	/**
	* @const array DIRS The locations of the used dirs
	*/
	public const DIRS = [
		'log_path' => 'log',
		'tmp_path' => 'tmp',
		'cache_path' => 'cache',
		'libraries_path' => 'libraries',
		'extensions_path' => 'extensions'
	];

	/**
	* @const array URLS The locations of the used urls
	*/
	public const URLS = [
		'extensions' => 'extensions',
		'cache' => 'cache'
	];

	/**
	* @const array EXTENSIONS_DIR The locations of the used extensions subdirs
	*/
	public const EXTENSIONS_DIRS = [
		'languages' => 'languages/',
		'templates' => 'templates/',
		'images' => 'images/'
	];

	/**
	* @const array MOBILE_DORS The locations of the used mobile subdirs
	*/
	public const MOBILE_DIRS = [
		'desktop' => 'desktop/',
		'mobile' => 'mobile/',
		'tablets' => 'tablets/',
		'smartphones' => 'smartphones/'
	];

	/**
	* @const array CACHE_DIRS The locations of the cache subdirs
	*/
	public const CACHE_DIRS = [
		'templates' => 'templates/'
	];

	/**
	* @const array FILE_EXTENSIONS Common file extensions
	*/
	public const FILE_EXTENSIONS = [
		'templates' => 'tpl'
	];

	/**
	* Protected constructor
	*/
	protected function __construct()
	{
		$this->path = $this->getPath();
		$this->is_bin = $this->getIsBin();

		if (!$this->is_bin) {
			$this->is_https = $this->getIsHttps();
			$this->scheme = $this->getScheme();
			$this->method = $this->getMethod();
			$this->protocol = $this->getProtocol();
			$this->is_http2 = $this->getIsHttp2();
			$this->full_url = $this->getFullUrl();
		}

		$this->setDirs();
	}

	/**
	* Instantiates the App object
	* @return App The app instance
	*/
	public static function instantiate() : App
	{
		static::$instance = new static;

		return static::$instance;
	}

	/**
	* Returns the app instance
	* @return App The app instance
	*/
	public static function get() : App
	{
		return static::$instance;
	}

	/**
	* Boots the App
	* @return App The app instance
	*/
	public function boot()
	{
		$this->loadBooter();

		$this->boot->minimum();
		$this->boot1();
		$this->boot->caching();
		$this->boot->libraries();
		$this->boot->db();
		$this->boot2();
		$this->boot->base();
		/*$this->boot->env();
		$this->boot->document();
		$this->boot->system();

		$this->plugins->run('app_boot', $this);*/
	}

	/**
	* Loads the dependencies class and initializes the required dependencies
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}

	/**
	* Includes the autoload file for libraries
	*/
	protected function loadLibraries()
	{
		require_once($this->libraries_path . 'php/vendor/autoload.php');
	}

	/**
	* Prepares the properties, after the minimum boot has finished
	*/
	public function boot1()
	{
		if (!$this->is_bin) {
			$this->ip = $this->getIp();
			$this->useragent = $this->getUseragent();
			$this->accepts_gzip = $this->getAcceptsGzip();
			$this->can_gzip = $this->canGzip();
		}

		$this->config->normalize();
	}

	/**
	* Prepares the properties, after the database is available
	*/
	public function boot2()
	{
		$this->setUrls();
		$this->setDevelopment();
	}

	/**
	* Returns the location on the disk where the site is installed
	* @return string
	*/
	protected function getPath() : string
	{
		return dirname(__DIR__, 3) . '/';
	}

	/**
	* Returns true if this is a bin script
	* @return bool
	*/
	protected function getIsBin() : bool
	{
		if (php_sapi_name() == 'cli') {
			return true;
		}

		return false;
	}

	/**
	* Returns true if this is a https request
	* @return bool
	*/
	protected function getIsHttps() : bool
	{
		if (empty($_SERVER['HTTPS'])) {
			return false;
		}

		return true;
	}

	/**
	* Returns the scheme: http/https
	* @return string
	*/
	protected function getScheme() : string
	{
		if ($this->is_https) {
			return 'https://';
		}

		return 'http://';
	}

	/**
	* Returns the request method: get/post/put
	* @return string
	*/
	protected function getMethod() : string
	{
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	/**
	* Returns the server protocol
	*/
	protected function getProtocol() : string
	{
		return $_SERVER['SERVER_PROTOCOL'];
	}

	/**
	* Returns true if the protocol is HTTP/2
	*/
	protected function getIsHttp2() : bool
	{
		$version = (int)str_replace('HTTP/', '', $this->protocol);

		return $version == 2;
	}

	/**
	* Returns the full url of the current page
	* @return string
	*/
	protected function getFullUrl() : string
	{
		$url = $this->scheme . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return filter_var($url, FILTER_SANITIZE_URL);
	}

	/**
	* Prepares the base dirs
	*/
	protected function setDirs()
	{
		$this->assignDirs(static::DIRS);
	}

	/**
	* Sets the urls
	*/
	protected function setUrls()
	{
		$this->url = $this->config->url;
		$this->url_static = $this->url;

		if ($this->config->url_static) {
			$this->url_static = $this->config->url_static;
		}

		$this->assignUrls(static::URLS);
	}
















	/**
	* Returns the static url of a dir
	* @param string $url The url key as defined in App::URLS
	* @return string The static url
	*/
	public function getStaticUrl(string $url) : string
	{
		return $this->url_static . static::URLS[$url] . '/';
	}



	/**
	* Sets the development property
	*/
	protected function setDevelopment()
	{
		if ($this->config->development) {
			$this->development = true;
		}
	}



	/**
	* Returns the user's IP
	* @return string The ip
	*/
	public function getIp() : string
	{
		if ($this->ip) {
			return $this->ip;
		}

		$ip = $_SERVER['REMOTE_ADDR'];

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if (in_array($_SERVER['REMOTE_ADDR'], $this->config->trusted_proxies)) {
				//HTTP_X_FORWARDED_FOR can contain multiple IPs. Use only the last one
				$proxy_ip = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

				if (filter_var($proxy_ip, FILTER_VALIDATE_IP)) {
					return $proxy_ip;
				}
			}
		}

		if (filter_var($ip, FILTER_VALIDATE_IP)) {
			return $ip;
		}

		throw new \Exception("Invalid IP: {$ip}");
	}

	/**
	* Returns the user's useragent
	* @return string The useragent
	*/
	public function getUseragent() : string
	{
		if ($this->useragent) {
			return $this->usergroups;
		}

		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	* Returns true if the browser accepts gzipped content
	* @return bool
	*/
	protected function getAcceptsGzip() : bool
	{
		if (!empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			if (str_contains(strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip')) {
				return true;
			}
		}

		return false;
	}

	/**
	* Returns true if the content can be gzipped
	* @return bool
	*/
	protected function canGzip() : bool
	{
		if ($this->accepts_gzip && $this->config->gzip) {
			return true;
		}

		return false;
	}






	/**
	* Assigns the dirs as app properties
	* @param array $dirs The dirs to assign
	*/
	protected function assignDirs(array $dirs)
	{
		foreach ($dirs as $name => $dir) {
			$this->$name = $this->path . $dir . '/';
		}
	}

	/**
	* Assigns the urls as app properties
	* @param array $urls The urls to assign
	* @param string $base_url The base url
	* @param string $prefix Prefix to place before the url
	* @param string $suffix Suffix to append to the url, to the url, if any
	*/
	protected function assignUrls(array $urls, string $base_url = '', string $prefix = '', string $suffix = 'url')
	{
		if (!$base_url) {
			$base_url = $this->url;
		}
		if ($prefix) {
			$prefix.= '_';
		}
		if ($suffix) {
			$suffix = '_' . $suffix;
		}

		foreach ($urls as $name => $url) {
			$name = $prefix . $name . $suffix;

			$this->$name = $base_url . $url . '/';
		}
	}



	/**
	* Outputs the app's content
	*/
	public function output()
	{
		if ($this->config->debug) {
			$this->timer->start('app_output_content');
		}

		$this->plugins->run('app_output_start', $this);

		//grab the content template first
		ob_start();
		$this->theme->renderContent();
		$content = ob_get_clean();

		$content = $this->plugins->filter('app_output_filter_content', $content, $this);

		ob_start();
		$this->theme->renderHeader();
		echo $content;
		$this->theme->renderFooter();
		$output = ob_get_clean();

		$output = $this->plugins->filter('app_output_filter_output', $output, $this);

		if ($this->config->debug) {
			$output.= $this->getDebugOutput(strlen($output));

			$this->can_gzip = false;
		}

		if ($this->can_gzip) {
			header('Content-encoding: gzip');

			$output = $this->gzip($output);
		}

		//cache the output, if required
		if ($this->caching->can_cache) {
			$this->caching->store($output, $this->can_gzip);
		}

		$output = $this->plugins->filter('app_output_filter', $output, $this);

		echo $output;

		$this->plugins->run('app_output_end', $this);

		die;
	}

	/**
	* Returns the debug output, if debug is on
	* @param int $output_size The size of the generated output
	* @return string
	*/
	protected function getDebugOutput(int $output_size) : string
	{
		$debug = $this->getDebugObj();

		$debug->info['output_size'] = $output_size;
		$debug->info['output_content_time'] = $this->timer->end('app_output_content');

		ob_start();
		$debug->output();
		return ob_get_clean();
	}

	/**
	* @internal
	*/
	protected function getDebugObj()
	{
		return new Debug($this);
	}

	/**
	* Renders/Outputs a template
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	*/
	public function render(string $template, array $vars = [])
	{
		$this->start();

		$this->theme->render($template, $vars);

		$this->end();

		$this->output();

		return $this;
	}

	/**
	* Renders a controller
	* @param Controller $controller The controller
	* @param string $action The action to perform. If null, it will be read from the request data
	*/
	public function renderController(Controller $controller, ?string $action = null)
	{
		if ($action === null) {
			$action = $this->request->getAction();
		}

		$this->start();

		$controller->dispatch($action);

		$this->end();

		$this->output();

		return $this;
	}

	/*********************************************************************************/

	/**
	* Calls gzencode on $content
	* @param string $content The content to gzip
	* return string The gzipped content
	*/
	public function gzip(string $content) : string
	{
		return gzencode($content);
	}

	/**
	* Starts the output buffering.
	*/
	public function start()
	{
		ob_start();
	}

	/**
	* End the output and sets $this->app->content
	*/
	public function end()
	{
		$this->content = ob_get_clean();
	}

	/**
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->errors->count()) {
			return false;
		}

		return true;
	}

	/**********************MESSAGING FUNCTIONS***************************************/

	/**
	* @internal
	*/
	protected function getScreenObj()
	{
		return new Document\Screen($this);
	}

	/**
	* Displays a fatal error screen
	* @param $text The error's text
	* @param bool $escape_html If true will escape the error message
	* @see \Mars\Document\Screen::fatalError()
	*/
	public function fatalError(string $text, bool $escape_html = true)
	{
		$screen = $this->getScreenObj();
		$screen->fatalError($text, $escape_html);
	}

	/**
	* Displays an error screen
	* @param string $text The error's text
	* @param string $title The error's title, if any
	* @param bool $escape_html If true will escape the title and error message
	* @see \Mars\Document\Screen::error()
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		$screen = $this->getScreenObj();
		$screen->error($text, $title, $escape_html);
	}

	/**
	* Displayes a message screen
	* @param string $text The text of the message
	* @param string $title The title of the message, if any
	* @param bool $escape_html If true will escape the title and message
	* @see \Mars\Document\Screen::message()
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		$screen = $this->getScreenObj();
		$screen->message($text, $title, $escape_html);
	}

	/**
	* Displays the Permission Denied screen
	* @see \Mars\Document\Screen::permissionDenied()
	*/
	public function permissionDenied()
	{
		$screen = $this->getScreenObj();
		$screen->permissionDenied();
	}

	/**
	* Redirects the user to the specified page
	* @param string $url The url where the user will be redirected
	*/
	public function redirect(string $url = '')
	{
		if (!$url) {
			$url = $this->url;
		}

		header('Location: ' . $url);
		die;
	}

	/********************** UTILITY FUNCTIONS ***************************************/


	/**
	* Returns a language string
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string The language string
	*/
	public static function __(string $str, array $replace = []) : string
	{
		$str = static::$instance->lang->strings[$str] ?? $str;

		if ($replace) {
			$str = str_replace(array_keys($replace), $replace, $str);
		}

		return $str;
	}

	/**
	* Returns a string based on the count of $items.
	* @param string $str_single If count($items) == 1 will return $this->app->lang->strings[$str_single]
	* @param string $str_multi If count($items) == 1 will return $this->app->lang->strings[$str_multi]. Will also replace {COUNT} with the actual count number
	* @param Countable $items The items to count
	* @param string $count_str The part which will be replaced with the count number. Default: {COUNT}
	* @return string
	*/
	public static function __c(string $str_single, string $str_multi, \Countable $items, string $count_str = '{COUNT}') : string
	{
		$count = count($items);
		if ($count == 1) {
			return static::__($str_single, []);
		} else {
			return static::__($str_multi, [$count_str => $count]);
		}
	}

	/**
	* Escapes a language string. Shorthand for e(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string
	*/
	public static function __e(string $str, array $replace = []) : string
	{
		return static::e(static::__($str, $replace));
	}

	/**
	* Javascript escapes a language string. Shorthand for ejs(__($str))
	* @param string $str The string index as defined in the languages file
	* @param array $replace Array with key & values to be used for to search & replace, if any
	* @return string
	*/
	public function __ejs(string $str, array $replace = []) : string
	{
		return static::ejs(static::__($str, $replace));
	}

	/**
	* Adds a slash at the end of a path, if it's not already there
	* @param string $path The path
	* @return string The path
	*/
	public static function fixPath(string $path) : string
	{
		if (!$path) {
			return '';
		}

		return rtrim($path, '/') . '/';
	}

	/**
	* Converts a string to a class name. Eg: some-action => SomeAction
	* @param string $str The string to convert
	* @return string The class name
	*/
	public static function getClass(string $str) : string
	{
		$str = preg_replace('/[^a-z0-9\- ]/i', '', $str);
		$str = str_replace(' ', '-', $str);

		$str = ucwords($str, '-');
		$str = str_replace('-', '', $str);

		return $str;
	}

	/**
	* Returns a property of an object or an array value
	* @param string $name The name of the property/index
	* @param array|object $data The data to return the property from
	* @return mixed The property
	*/
	public static function getProperty(string $name, array|object $data)
	{
		if (is_array($data)) {
			return $data[$name] ?? null;
		} else {
			return $data->$name ?? null;
		}
	}

	/**
	* Returns an array from an array/object/iterator
	* @param mixed $array The array
	* @return array
	*/
	public static function array($array) : array
	{
		if (!$array) {
			return [];
		}

		if (is_array($array)) {
			return $array;
		} elseif (is_iterable($array)) {
			return iterator_to_array($array);
		} elseif (is_object($array)) {
			return get_object_vars($array);
		} else {
			return (array)$array;
		}
	}

	/**
	* Unsets from $array the specified keys
	* @param array $array The array
	* @param string|array The keys to unset
	* @return array The array
	*/
	public static function unset(array &$array, string|array $keys) : array
	{
		$keys = (array)$keys;

		foreach ($keys as $key) {
			if (isset($array[$key])) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	* Pads a number with a leading 0 if it's below 10. Eg: if $number = 6 returns 06
	* @param int $number The number
	* @return string The number with a leading 0
	*/
	public static function padInt(int $number) : string
	{
		if ($number < 10) {
			return '0' . $number;
		}

		return $number;
	}

	/**
	* Does a print_r on $var and outputs <pre> tags
	* @param mixed $var The variable
	* @param bool $die If true, will call die after
	*/
	public static function pp($var, bool $die = true)
	{
		echo '<pre>';
		\print_r($var);
		echo '</pre>';

		if ($die) {
			die;
		}
	}

	/**
	* Alias for dd
	* @see App::pp()
	*/
	public static function dd($var, bool $die = true)
	{
		static::pp($var, $die);
	}

	/**
	* Prints the debug backtrace
	*/
	public static function backtrace()
	{
		echo '<pre>';
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		echo '</pre>';

		die;
	}
}
