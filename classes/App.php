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
	use AppFunctionsTrait;

	/**
	* @var float $version The version
	*/
	public string $version = '1.00';

	/**
	* @var string $ip The ip used to make the request
	*/
	public string $ip = '';

	/**
	* @var string $useragent The useragent
	*/
	public string $useragent = '';

	/**
	* @var bool $is_bin True if the app is run as a bin script
	*/
	public bool $is_bin = false;

	/**
	* @var bool $is_https True if the page is loaded with https, false otherwise
	*/
	public bool $is_https = false;

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
	* @var string $scheme The page's scheme: http:// or https://
	*/
	public string $scheme = '';

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
	* @var string current_url The url of the current page determined from $_SERVER. Does't include the QUERY_STRING
	*/
	public string $current_url = '';

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
	* @var string $namespace The root namespace
	*/
	public string $namespace = "App\\";

	/**
	* @var string $extensions_namespace The root namespace for extensions
	*/
	public string $extensions_namespace = "App\\Extensions\\";

	/**
	* @var string $log_path The folder where the log files are stored
	*/
	public string $log_path = '';

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
		'log' => 'log',
		'cache' => 'cache',
		'libraries' => 'libraries',
		'extensions' => 'extensions'
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

		if (!$this->is_bin) {
			$this->scheme = $this->getScheme();
			$this->current_url = $this->scheme . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
			$this->full_url = $this->scheme . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
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
		$this->boot->libraries();
		$this->boot->db();
		$this->boot->base();
		$this->boot->env();
		$this->boot->document();
		$this->boot->system();

		$this->plugins->run('app_boot', $this);
	}

	/**
	* Prepares the data
	*/
	public function setData()
	{
		if (!$this->is_bin) {
			$this->ip = $this->getIp();
			$this->useragent = $this->getUseragent();
		}

		$this->setGzip();
		$this->setDirs();
	}

	/**
	* Prepares the data, after the database is available
	*/
	public function setDataAfterDb()
	{
		$this->setUrls();
		$this->setDevelopment();
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
	* Sets the gzip properties
	*/
	protected function setGzip()
	{
		$this->accepts_gzip = false;

		if (!empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			if (str_contains(strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip')) {
				$this->accepts_gzip = true;
			}
		}

		if ($this->accepts_gzip && $this->config->gzip) {
			$this->can_gzip = true;
		}
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
	* Returns the location on the disk where the site is installed
	* @return string
	*/
	protected function getPath() : string
	{
		return dirname(__DIR__, 3) . '/';
	}

	/**
	* Returns the scheme: http/https
	* @return string
	*/
	protected function getScheme() : string
	{
		if (isset($_SERVER['HTTPS'])) {
			$this->is_https = true;
			return 'https://';
		}

		return 'http://';
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
	* Assigns the dirs as app properties
	* @param array $dirs The dirs to assign
	*/
	protected function assignDirs(array $dirs, string $base_path = '', string $prefix = '', string $suffix = 'path')
	{
		if (!$base_path) {
			$base_path = $this->path;
		}
		if ($prefix) {
			$prefix.= '_';
		}
		if ($suffix) {
			$suffix = '_' . $suffix;
		}

		foreach ($dirs as $name => $dir) {
			$name = $prefix . $name . $suffix;

			$this->$name = $base_path . $dir . '/';
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
}
