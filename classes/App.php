<?php
/**
* The App Class
* @package Mars
*/

namespace Mars;

use Mars\Alerts\Errors;
use Mars\Alerts\Info;
use Mars\Alerts\Messages;
use Mars\Alerts\Warnings;
use Mars\System\Language;
use Mars\System\Plugins;
use Mars\System\Theme;

/**
* The App Class
* The system's main object
*/
class App
{
	use AppUtilsTrait;

	/**
	* @var float $version The version
	*/
	public readonly string $version;

	/**
	* @var bool $is_bin True if the app is run as a bin script
	*/
	public readonly bool $is_bin;

	/**
	* @var bool $is_https True if the page is loaded with https, false otherwise
	*/
	public readonly bool $is_https;

	/**
	* @var string $scheme The page's scheme: http:// or https://
	*/
	public readonly string $scheme;

	/**
	* @var string $method The request method. get/post.
	*/
	public readonly string $method;

	/**
	* @var string $protocol The server protocol
	*/
	public readonly string $protocol;

	/**
	* @var bool $is_https2 True if the page is loaded using HTTP/2
	*/
	public readonly bool $is_http2;

	/**
	* @var string $url The url. Eg: http://mydomain.com/mars
	*/
	public readonly string $url;

	/**
	* @var string $url_static The url from where static content is served
	*/
	public string $url_static = '';

	/**
	* @var string $full_url The url of the current page determined from $_SERVER. Includes the QUERY_STRING
	*/
	public readonly string $full_url;

	/**
	* @var string $ip The ip used to make the request
	*/
	public readonly string $ip;

	/**
	* @var string $useragent The useragent
	*/
	public readonly string $useragent;

	/**
	* @var bool $accepts_gzip If true, the user's browser accepts gzipped output
	*/
	public readonly bool $accepts_gzip;

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
	public readonly string $path;

	/**
	* @var string $log_path The folder where the log files are stored
	*/
	public readonly string $log_path;

	/**
	* @var string $tmp_path The folder where the temporary files are stored
	*/
	public readonly string $tmp_path;

	/**
	* @var string $cache_path The folder where the cache files are stored
	*/
	public readonly string $cache_path;

	/**
	* @var string $libraries_path The folder where the php libraries are stored
	*/
	public readonly string $libraries_path;

	/**
	* @var string $extensions_path The folder where the extensions are stored
	*/
	public readonly string $extensions_path;

	/**
	* @var string $extensions_url The url of the extensions folder
	*/
	public readonly string $extensions_url;

	/**
	* @var Accelerator $accelerator The accelerator object
	*/
	public Accelerator $accelerator;

	/**
	* @var Alerts $alerts The alerts object
	*/
	public Alerts $alerts;

	/**
	* @var Cache $cache The cache object
	*/
	public Cache $cache;

	/**
	* @var Caching $caching The caching object
	*/
	public Caching $caching;

	/**
	* @var Config $config The config object
	*/
	public Config $config;

	/**
	* @var Db $db The Db object
	*/
	public Db $db;

	/**
	* @var Device $device The device object
	*/
	public Device $device;

	/**
	* @var Dir $dir The dir object
	*/
	public Dir $dir;

	/**
	* @var Document $document The document object
	*/
	public Document $document;

	/**
	* @var Errors $errors The errors object
	*/
	public Errors $errors;

	/**
	* @var Escape $escape The escape object
	*/
	public Escape $escape;

	/**
	* @var Filter $filter The filter object
	*/
	public Filter $filter;

	/**
	* @var File $file The file object
	*/
	public File $file;

	/**
	* @var Format $format The format object
	*/
	public Format $format;

	/**
	* @var Html $html The html object
	*/
	public Html $html;

	/**
	* @var Info $info The info object
	*/
	public Info $info;

	/**
	* @var Json $json The json object
	*/
	public Json $json;

	/**
	* @var Language $lang The language object
	*/
	public Language $lang;

	/**
	* @var Log $log The log object
	*/
	public Log $log;

	/**
	* @var Mail $mail The mail object
	*/
	public Mail $mail;

	/**
	* @var Memcache $memcache The memcache object
	*/
	public Memcache $memcache;

	/**
	* @var Messages $messages The messages object
	*/
	public Messages $messages;

	/**
	* @var Plugins $plugins The plugins object
	*/
	public Plugins $plugins;

	/**
	* @var Random $random The random object
	*/
	public Random $random;

	/**
	* @var Registry $registry The registry object
	*/
	public Registry $registry;

	/**
	* @var Response $response The response object
	*/
	public Response $response;

	/**
	* @var Request $request The request object
	*/
	public Request $request;

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
	* @var Screens $screens The Screens object
	*/
	public Screens $screens;

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
	* @var Warnings $warnings The warnings object
	*/
	public Warnings $warnings;

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
		$this->version = '1.0';
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
		$this->boot->env();
		$this->boot->document();
		$this->boot->system();

		$this->plugins->run('app_boot', $this);
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
		$this->development = $this->config->development;
	}

	/**
	* Prepares the properties, after the database is available
	*/
	public function boot2()
	{
		$this->setUrls();
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
	* Returns the user's IP
	* @return string The ip
	*/
	public function getIp() : string
	{
		if (!empty($this->ip)) {
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
		if (!empty($this->useragent)) {
			return $this->useragent;
		}

		return $_SERVER['HTTP_USER_AGENT'] ?? '';
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
		$this->plugins->run('app_start', $this);

		ob_start();
	}

	/**
	* End the output and sets $this->app->content
	*/
	public function end()
	{
		$content = ob_get_clean();

		$this->plugins->run('app_end1', $this);

		if ($this->config->debug) {
			$this->timer->start('app_output_content');
		}

		//grab the content template first
		ob_start();
		$this->theme->renderContent();
		$content = ob_get_clean();
	}

	/**
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->alerts->errors->count()) {
			return false;
		}

		return true;
	}



	/**
	* Outputs the app's content
	*/
	public function output()
	{
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


	/**********************SCREENS FUNCTIONS***************************************/

	/**
	* Displays a fatal error screen
	* @param $text The error's text
	* @param bool $escape_html If true will escape the error message
	* @see \Mars\Document\Screen::fatalError()
	*/
	public function fatalError(string $text, bool $escape_html = true)
	{
		$this->screens->fatalError($text, $escape_html);
	}

	/**
	* Displays an error screen
	* @param string $text The error's text
	* @param string $title The error's title, if any
	* @param bool $escape_html If true will escape the title and error message
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		$this->screens->error($text, $title, $escape_html);
	}

	/**
	* Displayes a message screen
	* @param string $text The text of the message
	* @param string $title The title of the message, if any
	* @param bool $escape_html If true will escape the title and message
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		$this->screens->message($text, $title, $escape_html);
	}

	/**
	* Displays the Permission Denied screen
	* @see \Mars\Document\Screen::permissionDenied()
	*/
	public function permissionDenied()
	{
		$this->screens->permissionDenied();
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
