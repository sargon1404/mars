<?php
/**
* The Config Class
* @package Mars
*/

namespace Mars;

/**
* The Config Class
* Stores the system's config options
*/
class Config extends Data
{
	/**
   * @var bool $log_errors If true, will log all errors to the log files
   */
	public bool $log_errors =  true;

	/**
   * @var int $log_error_reporting The log error reporting level
   */
	public int $log_error_reporting =  E_ALL;

	/**
	* @var string $log_suffix The suffix format of the log files
	*/
	public string $log_suffix = 'd-F-Y';

	/**
	* @var string $log_date_format The format of the log dates
	*/
	public string $log_date_format = 'm-d-Y H:i:s';

	/**
	* @var string $url The url of the site
	*/
	public string $url = '';

	/**
	* @var string $url_static The base url from where the static resources will be served
	*/
	public string $url_static = '';

	/**
	* @var string $key The secret key of the site
	*/
	public string $key = '';

	/**
	* @var string $open_basedir If specified, will limit the files which are accessible to the specified folder. If the value is true the installation dir is used
	*/
	public bool|string $open_basedir = true;

	/**
	* @var bool $gzip If true, will gzip the output
	*/
	public bool $gzip = false;

	/**
	* @var array $trusted_proxies The trusted proxies from which we'll accept the HTTP_X_FORWARDED_FOR header
	*/
	public array $trusted_proxies = [];

	/**
	* @var bool $debug Set to true to enable debug mode
	*/
	public bool $debug = false;

	/**
	* @var string|array $debug_ips If specified, will enable debug only for the listed IPs. Works only if debug is false
	*/
	public string|array $debug_ips = [];

	/**
	* @var bool $development Set to true to enable development mode
	*/
	public bool $development = false;

	/**
	* @var string $development_device Will use this value as device, if specified. Valid values: 'desktop', 'tablet', 'smartphone'
	*/
	public string $development_device = '';

	/**
	* Change the driver only if you know what you're doing! Preferably at installation time.
	* You might try to unserialize data which has been serialized with a different driver, otherwise
	* @var string $serializer_driver The serializer driver. Supported options: php, igbinary
	*/
	public string $serializer_driver = 'php';

	/**
	* @var bool $device_start If false, will not start the device detection functionality
	*/
	public bool $device_start = true;

	/**
	* @var bool $session_start If false, will not start the session functionality
	*/
	public bool $session_start = true;

	/**
	* @var string $session_save_path The path where the sessions will be saved
	*/
	public string $session_save_path = '';

	/**
	* @var string $session_name The session name
	*/
	public string $session_name = '';

	/**
	* @var int $session_cookie_lifetime The lifetime of the session cookie
	*/
	public ?int $session_cookie_lifetime = null;

	/**
	* @var string $session_cookie_path The path of the session cookie
	*/
	public ?string $session_cookie_path = null;

	/**
	* @var string $session_cookie_domain The domain of the session cookie
	*/
	public ?string $session_cookie_domain = null;

	/**
	* @var bool $session_cookie_secure If true the session cookie will only be sent over secure connections.
	*/
	public ?bool $session_cookie_secure = null;

	/**
	* @var bool $session_cookie_httponly If true then httponly flag will be set for the session cookie
	*/
	public ?bool $session_cookie_httponly = true;

	/**
	* @var string $session_cookie_samesite The samesite value of the session_cookie
	*/
	public ?string $session_cookie_samesite = null;

	/**
	* @var string $session_driver The session driver. Supported options: php, db, memcache
	*/
	public string $session_driver = 'php';

	/**
	* @var string $session_driver The table where sessions are stored if the session_driver=db
	*/
	public string $session_table = 'php';

	/**
	* @var string $session_prefix Prefix to apply to all session keys
	*/
	public string $session_prefix = '';

	/**
	* @var int $cookie_expire_days The interval, in days, for which the cookies will be valid
	*/
	public int $cookie_expire_days = 30;

	/**
	* @var string $cookie_path The path of the cookies
	*/
	public string $cookie_path = '/';

	/**
	* @var string $cookie_domain The domain of the cookies
	*/
	public string $cookie_domain = '';

	/**
	* @var bool $cookie_secure If true the cookies will only be sent over secure connections.
	*/
	public bool $cookie_secure = false;

	/**
	* @var bool $cookie_httponly If true then httponly flag will be set for the cookies
	*/
	public bool $cookie_httponly = true;

	/**
	* @var string $cookie_samesite The samesite value of the cookies
	*/
	public string $cookie_samesite = '';

	/**
	* @var string $lang The default language
	*/
	public string $lang = 'english';

	/**
	* @var string $lang The default theme
	*/
	public string $theme = 'theme';

	/**
	* @var string $css_version Version param to apply to all css stylesheets
	*/
	public string $css_version = '1';

	/**
	* @var string $javascript_version Version param to apply to all JS scripts
	*/
	public string $javascript_version = '1';

	/**
	* @var string $html_allowed_elements The allowed html elements; used when filtering html. If null, all elements are allowed
	*/
	public ?string $html_allowed_elements = null;

	/**
	* @var string $html_allowed_attributes The allowed html attributes; used when filtering html
	*/
	public ?string $html_allowed_attributes = '*.class,*.style,img.src,img.alt,a.target,a.rel,a.href,a.title';

	/**
	* @var string $request_type_param The name of the response request param
	*/
	public string $request_type_param = 'type';

	/**
	* @var string $request_action_param The name of the action request param
	*/
	public string $request_action_param = 'action';

	/**
	* @var string $request_orderby_param The name of the orderby request param
	*/
	public string $request_orderby_param = 'order_by';

	/**
	* @var string $request_order_param The name of the order request param
	*/
	public string $request_order_param = 'order';

	/**
	* @var string $request_page_param The name of the page request param
	*/
	public string $request_page_param = 'page';

	/**
	* @var bool $plugins_enable True, if plugins are enabled
	*/
	public bool $plugins_enable = true;

	/**
	* @var int $pagination_max_links The max number of pagination links to show
	*/
	public int $pagination_max_links = 10;

	/**
	* @var int $pagination_items_per_page The number of items that should be displayed on each page
	*/
	public int $pagination_items_per_page = 30;

	/**
	* @var bool $image_optimize If true, the images will be optimized when processed/uploaded
	*/
	public bool $image_optimize = true;

	/**
	* @var string $image_background The background to apply when resizing/cutting.. images...
	*/
	public string $image_background_color = 'ffffff';

	/**
	* @var int $image_jpg_quality The quality of jpg images
	*/
	public int $image_jpg_quality = 80;

	/**
	* @var string $image_jpg_optimize_command The command used to optimize the jpg images
	*/
	public string $image_jpg_optimize_command = 'jpegoptim --strip-all -m 80 {FILENAME}';

	/**
	* @var int $image_png_quality The quality of png images
	*/
	public int $image_png_quality = 6;

	/**
	* @var string $image_png_optimize_command The command used to optimize the png images
	*/
	public string $image_png_optimize_command = 'convert {FILENAME} -strip {FILENAME}';

	/**
	* @var string $image_gif_optimize_command The command used to optimize gif images
	*/
	public string $image_gif_optimize_command = 'gifsicle {FILENAME} -o {FILENAME}';

	/**
	* @var int $image_webp_quality The quality of webp images
	*/
	public int $image_webp_quality = 80;

	/**
	* @var string $image_watermark_background The color of the watermark's background
	*/
	public string $image_watermark_background = '000000';

	/**
	* @var string $image_watermark_opacity The opacity of the watermark
	*/
	public string $image_watermark_opacity = '80';

	/**
	* @var bool $image_watermark_text_ttf If true, will render the watermark text using a ttf font
	*/
	public bool $image_watermark_text_ttf = false;

	/**
	* @var string $image_watermark_text_font The font used to draw the watermark text
	*/
	public string $image_watermark_text_font = '5';

	/**
	* @var string $image_watermark_text_color The color of the watermark text
	*/
	public string $image_watermark_text_color = 'ffffff';

	/**
	* @var int $image_watermark_text_size The size of the watermark text
	*/
	public string $image_watermark_text_size = '20';

	/**
	* @var string $image_watermark_text_angle The angle of the watermark text
	*/
	public string $image_watermark_text_angle = '0';

	/**
	* @var string $image_watermark_padding_top The top/bottom padding of the watermark text
	*/
	public string $image_watermark_padding_top = '10';

	/**
	* @var string $image_watermark_padding_left The left/right padding of the watermark text
	*/
	public string $image_watermark_padding_left = '15';

	/**
	* @var string $image_watermark_margin_top The top/bottom margin of the watermark text
	*/
	public string $image_watermark_margin_top = '20';

	/**
	* @var string $image_watermark_margin_left The left/right margin of the watermark text
	*/
	public string $image_watermark_margin_left = '30';

	/**
	* Builds the Config object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->read();
	}

	/**
	* Reads the config settings from the config.php file
	* @return static
	*/
	public function read() : static
	{
		$this->readFile('config.php');

		return $this;
	}

	/**
	* Reads the config settings from the specified $filename
	* @param string $filename The filename
	* @return static
	*/
	public function readFile(string $filename) : static
	{
		$config = require($this->app->path . $filename);

		$this->assign($config);

		return $this;
	}

	/**
	* Normalizes the config options
	*/
	public function normalize()
	{
		if ($this->device_start) {
			$this->session_start = true;
		}

		if ($this->development) {
			$this->content_cache_enable = false;
			$this->css_version = time();
			$this->javascript_version = time();
		}

		if ($this->debug_ips) {
			if (in_array($this->app->ip, $this->debug_ips)) {
				$this->debug = true;
			}
		}

		if ($this->debug) {
			$this->db_debug = true;
		}
	}
}
