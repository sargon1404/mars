<?php
/**
* The Request Class
* @package Mars
*/

namespace Mars;

/**
* The Request Class
* Handles the input [$_GET, $_POST, $_COOKIE] interactions
*/
class Request
{
	use AppTrait;

	/**
	* @var array $server Array containing the server data
	*/
	public array $server = [];

	/**
	* @var array $get Array containing the get data
	*/
	public array $get = [];

	/**
	* @var array $post Array containing the post data
	*/
	public array $post = [];

	/**
	* @var array $cookie Array containing the cookie data
	*/
	public array $cookie = [];

	/**
	* @var string $method The request method. get/post.
	*/
	public string $method = '';

	/**
	* @var int $cookie_expires The cookie's expires timestamp
	*/
	protected string $cookie_expires = '';

	/**
	* @var string $cookie_path The cookie's path
	*/
	protected string $cookie_path = '';

	/**
	* @var string $cookie_domain The cookie's domain
	*/
	protected string $cookie_domain = '';

	/**
	* @var array $upload_disallowed_extensions The extensions of the files which are disallowed at upload
	*/
	protected array $upload_disallowed_extensions = ['php', 'cgi', 'pl', 'py', 'exe', 'sh', 'bin'];

	/**
	* Builds the request object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->server = $_SERVER;
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookie = $_COOKIE;

		//trim the get and post data
		if ($this->get) {
			$this->get = $this->trim($this->get);
		}
		if ($this->post) {
			$this->post = $this->trim($this->post);
		}

		$this->cookie_expires = time() + 3600 * 24 * $this->app->config->cookie_expires_days;
		$this->cookie_path = $this->app->config->cookie_path;
		$this->cookie_domain = $this->app->config->cookie_domain;

		$this->method = 'get';
		if ($this->server('REQUEST_METHOD', true) == 'post') {
			$this->method = 'post';
		}
	}

	/**
	* Trims an input value
	* @param string|array $value The value to trim
	* @return string|array
	*/
	protected function trim(string|array $value)
	{
		if (is_array($value)) {
			return array_map([$this, 'trim'], $value);
		}

		return trim($value);
	}

	/**
	* Determines if $_GET[$name] is set
	* @param string $name The name of the get variable
	* @return bool True if set, false otherwise
	*/
	public function isGet(string $name) : bool
	{
		return isset($this->get[$name]);
	}

	/**
	* Determines if $_POST[$name] is set
	* @param string $name The name of the post variable
	*  @return bool True if set, false otherwise
	*/
	public function isPost(string $name) : bool
	{
		return isset($this->post[$name]);
	}

	/**
	* Determines if $_COOKIE[$name] is set
	* @param string $name The name of the cookie
	* @return bool  @return bool True if set, false otherwise
	*/
	public function isCookie(string $name) : bool
	{
		return isset($this->cookie[$name]);
	}

	/**
	* Determines if $_SERVER[$name] is set
	* @param string $name The name of the cookie
	*  @return bool True if set, false otherwise
	*/
	public function isServer(string $name) : bool
	{
		return isset($this->server[$name]);
	}

	/**
	* Sets $_GET[$name] to $value
	* @param string $name The name of the get variable
	* @param string $value The value it will be set to
	* @return $this
	*/
	public function setGet(string $name, string $value)
	{
		$this->get[$name] = $value;

		return $this;
	}

	/**
	* Sets $_POST[$name] to $value
	* @param string $name The name of the post variable
	* @param string $value The value it will be set to
	* @return $this
	*/
	public function setPost(string $name, string $value)
	{
		$this->post[$name] = $value;

		return $this;
	}

	/**
	* Writes a cookie. The data is first encoded using json encode
	* @param string $name The name of the cookie
	* @param mixed $data The data to be written in the array
	* @param int $expires The expiration date. If null, $this->cookie_expires is used
	* @param string $path The path in which the cookie is valid. If null, $this->cookie_path is used
	* @param string $domain The domain in which the cookie is valid. If null, $this->cookie_domain is used
	* @param bool $encode If true will encode the data
	* @param bool $secure If true, the cookie will only be set over a https connection
	* @param bool $httponly If true the cookie is accesible only over http and not with javascript
	* @return $this
	*/
	public function setCookie(string $name, $data, ?int $expires = null, ?string $path = null, ?string $domain = null, bool $encode = true, bool $secure = false, bool $httponly = true)
	{
		if ($encode) {
			$data = $this->app->encoder->encode($data);
		}

		if ($expires === null) {
			$expires = $this->cookie_expires;
		}
		if ($path === null) {
			$path = $this->cookie_path;
		}
		if ($domain === null) {
			$domain = $this->cookie_domain;
		}

		$this->cookie[$name] = $data;

		setcookie($name, $data, $expires, $path, $domain, $secure, $httponly);

		return $this;
	}

	/**
	* Deletes a cookie
	* @param string $name The name of the cookie to be deleted
	* @param string $path The path in which the cookie is valid. If null, $this->cookie_path is used
	* @param string $domain The domain in which the cookie is valid. If null, $this->cookie_domain is used
	* @return $this
	*/
	public function unsetCookie(string $name, ?string $path = null, ?string $domain = null)
	{
		if ($path === null) {
			$path = $this->cookie_path;
		}
		if ($domain === null) {
			$domain = $this->cookie_domain;
		}

		setcookie($name, '', 0, $path, $domain);

		return $this;
	}

	/**
	* Returns a value from $_SERVER
	* @param string $name The name of the get variable
	* @param bool $to_lower If true, will return the value in lowercase
	* @return string The $_SERVER[$name] value
	*/
	public function server(string $name, bool $to_lower = false) : string
	{
		if (!isset($_SERVER[$name])) {
			return '';
		}

		if ($to_lower) {
			return strtolower($_SERVER[$name]);
		} else {
			return $_SERVER[$name];
		}
	}

	/**
	* Returns a value from get/post
	* @param string $name The name of the variable
	* @param string $source The source: get/post
	* @param string $filter The filter to apply to the value. By default the value is filtered as a string. See class Filter for a list of filters
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array
	*/
	protected function getFromSource(string $name, string $source, string $filter, bool $is_array)
	{
		$value = $this->readValue($name, $source, true, $filter, $is_array);

		return $this->getValue($value, $filter, $is_array);
	}

	/**
	* Returns a value from $_GET
	* @param string $name The name of the get variable
	* @param string $filter The filter to apply to the value. By default the value is filtered as a string. See class Filter for a list of filters
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The $_GET[$name] value
	*/
	public function get(string $name, string $filter = '', bool $is_array = false)
	{
		return $this->getFromSource($name, 'get', $filter, $is_array);
	}

	/**
	* Alias of get() where $is_array is by default considered to be true
	* @param string $name The name of the variable
	* @param string $filter The filter to apply to the value. By default the value is filtered as a string. See class Filter for a list of filters
	* @return array
	*/
	public function getArray(string $name, string $filter = '') : array
	{
		return $this->getFromSource($name, 'get', $filter, true);
	}

	/**
	* Returns a value from $_POST
	* @param string $name The name of the post variable
	* @param string $filter The filter to apply to the value. By default it's considered a string. See class filter for a list of filter type values
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The $_POST[$name] value
	*/
	public function post(string $name, string $filter = '', bool $is_array = false)
	{
		return $this->getFromSource($name, 'post', $filter, $is_array);
	}

	/**
	* Alias of post() where $is_array is by default considered to be true
	* @param string $name The name of the post variable
	* @param string $filter The filter to apply to the value. By default it's considered a string. See class filter for a list of filter type values
	* @return array
	*/
	public function postArray(string $name, string $filter = '') : array
	{
		return $this->getFromSource($name, 'post', $filter, true);
	}

	/**
	* Returns a value from $_POST if is set, from $_GET otherwise
	* @param string $name The name of the get variable
	* @param string $filter The filter to apply to the value. By default it's considered a string
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The $_POST[$name] or the $_GET value
	*/
	public function value(string $name, string $filter = '', bool $is_array = false)
	{
		return $this->getFromSource($name, '', $filter, $is_array);
	}

	/**
	* Alias of value() where $is_array is by default considered to be true
	* @param string $name The name of the get variable
	* @param string $filter The filter to apply to the value. By default it's considered a string
	* @return array
	*/
	public function valueArray(string $name, string $filter = '') : array
	{
		return $this->getFromSource($name, '', $filter, true);
	}

	/**
	* Returns a value from $_COOKIE
	* @param string $name The name of the variable
	* @param string $filter The filter to apply to the value. By default it's considered a string
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The $_COOKIE[$name] value
	*/
	public function cookie(string $name, string $filter = '', bool $is_array = false)
	{
		return $this->getFromSource($name, 'cookie', $filter, $is_array);
	}

	/**
	* Reads a cookie.
	* The difference between read_cookie and cookie is that read_cookie will json decode the cookie data
	* @param string $name The name of the cookie
	* @return array The cookie's data
	*/
	public function readCookie(string $name) : array
	{
		if (!isset($this->cookie[$name])) {
			return [];
		}

		return $this->app->encoder->decode($this->cookie[$name]);
	}

	/**
	* Reads a value from get|post|cookie.
	* @param string $name The name
	* @param string $source The source of the input: get|post|cookie
	* @param bool $return_default If true and the value isn't found, will return the default value for this type of filter
	* @param string $filter The filter to apply to the value.
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The value
	*/
	protected function readValue(string $name, string $source, bool $return_default = true, string $filter = '', bool $is_array = false)
	{
		if ($source == 'post') {
			if (isset($this->post[$name])) {
				return $this->post[$name];
			}
		} elseif ($source == 'get') {
			if (isset($this->get[$name])) {
				return $this->get[$name];
			}
		} elseif ($source == 'cookie') {
			if (isset($this->cookie[$name])) {
				return $this->cookie[$name];
			}
		} else {
			if (isset($this->post[$name])) {
				return $this->post[$name];
			} elseif (isset($this->get[$name])) {
				return $this->get[$name];
			}
		}

		if ($return_default) {
			return $this->getDefaultValue($filter, $is_array);
		} else {
			return null;
		}
	}

	/**
	* Returns the default value for a certain filter
	* @param string $filter The filter to apply to the value.
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array The default value
	*/
	protected function getDefaultValue(string $filter = '', bool $is_array = false)
	{
		if ($is_array) {
			return [];
		}

		return $this->app->filter->defaultValue($filter);
	}

	/**
	* Processes a value and returns it
	* @param mixed $value The value
	* @param string $filter The filter to apply to the value.
	* @param bool $is_array If true, will force the returned value to an array
	* @return string|array
	*/
	protected function getValue($value, string $filter, bool $is_array = false)
	{
		if ($filter == 'ids') {
			//if the ids value is a string, it's probably comma-delimited.
			$is_array = true;

			if (!is_array($value)) {
				$value = explode(',', $value);
			}
		}

		//convert the value to/from array
		if (is_array($value)) {
			if (!$is_array) {
				$value = reset($value);
			}
		} else {
			if ($is_array) {
				settype($value, 'array');
			}
		}

		return $this->app->filter->value($value, $filter);
	}

	/**
	* For each element in the $data array, will check if a corresponding value with the same name exists in the post/get data.
	* If it does, will set the value in the $data to those values.
	* @param array|object $data The data to be filled
	* @param array $array_fields Fields which can be filled with arrays
	* @param array $ignore_fields Fields to ignore when filling.
	* @param string $key_prefix Key prefix which is used when filling the data
	* @param bool $use_post If true will use the post data. If false,the get data
	* @return array Returns the filled $data
	*/
	public function fill(array|object &$data, array $array_fields = [], array $ignore_fields = [], string $key_prefix = '', bool $use_post = true)
	{
		if (!$data) {
			return $data;
		}

		$request_data = $this->post;
		if (!$use_post) {
			$request_data = $this->get;
		}

		foreach ($data as $key => $val) {
			$index = $key_prefix . $key;

			if (in_array($key, $ignore_fields)) {
				continue;
			}
			if (!isset($request_data[$index])) {
				continue;
			}

			$is_array = false;
			if (in_array($index, $array_fields)) {
				$is_array = true;
			}

			$value = '';
			if ($use_post) {
				$value = $this->post($index, $this->getFillFilter($val), $is_array);
			} else {
				$value = $this->get($index, $this->getFillFilter($val), $is_array);
			}

			if (is_array($data)) {
				$data[$key] = $value;
			} else {
				$data->$key = $value;
			}
		}

		return $data;
	}

	/**
	* Fills with get data
	* @param array|object $data The data to be filled
	* @param array $array_fields Fields which can be filled with arrays
	* @param array $ignore_fields Fields to ignore when filling.
	* @param string $key_prefix Key prefix which is used when filling the data
	* @return array Returns the filled $data
	*/
	public function fillFromGet(array|object &$data, array $array_fields = [], array $ignore_fields = [], string $key_prefix = '')
	{
		return $this->fill($data, $array_fields, $ignore_fields, $key_prefix, false);
	}

	/**
	* Returns the fill filter of a value
	* @param string $value The value
	* @return string The filtered value
	*/
	protected function getFillFilter($value) : string
	{
		if (is_int($value)) {
			return 'i';
		} elseif (is_float($value)) {
			return 'f';
		}

		return '';
	}

	/**
	* Returns true if this is an ajax/json request
	* @param string $response_param The response type param
	* @return bool
	*/
	public function isAjax(string $response_param = '') : bool
	{
		$response = $this->getResponse($response_param);

		if ($response == 'ajax' || $response == 'json') {
			return true;
		}

		return false;
	}

	/**
	* Returns the type of the requested response [Eg: html|ajax]
	* @param string $response_param The response type param. Defaults to 'response'
	* @return string The response type
	*/
	public function getResponse(string $response_param = '') : string
	{
		if (!$response_param) {
			$response_param = 'response';
		}

		return $this->value($response_param);
	}

	/**
	* Returns the action to be performed
	* @param string $action_param The action param
	* @return string The action
	*/
	public function getAction(string $action_param = 'action') : string
	{
		return $this->value($action_param);
	}

	/**
	* Gets the 'order by' value
	* @param array $fields Array with values in the format: query_param => db_field. If empty it will return the content of $this->value($orderby_param)
	* @param string $default_field The default db_field
	* @param string $orderby_param The name of the orderby param
	* @return string The 'order by' value
	*/
	public function getOrderBy(array $fields = [], string $default_field = '', string $orderby_param = 'order_by') : string
	{
		$orderby = $this->value($orderby_param);
		if ($fields) {
			if (isset($fields[$orderby])) {
				return $fields[$orderby];
			} else {
				return $default_field;
			}
		} else {
			return $orderby;
		}
	}

	/**
	* Returns the order value
	* @param string $order_param The name of the order param
	* @return string The order value; asc/desc
	*/
	public function getOrder(string $order_param = 'order') : string
	{
		$order = strtolower($this->value($order_param));

		if ($order == 'asc') {
			return 'ASC';
		} elseif ($order == 'desc') {
			return 'DESC';
		} else {
			return '';
		}
	}

	/**
	* Gets the current page of the pagination system
	* @param string $page_param The name of the page param
	* @return int The value of the current page
	*/
	public function getPage(string $page_param = 'page') : int
	{
		$page = $this->value($page_param, 'i');
		if ($page <= 0) {
			$page = 1;
		}

		return $page;
	}

	/**
	* Checks if a file is an uploaded file
	* @param string $name The name of the file to check ($_FILES[$name])
	* @return bool Returns true if the file is uploaded
	*/
	public function isUploadedFile(string $name) : bool
	{
		if (!isset($_FILES[$name])) {
			return false;
		}

		if (is_array($_FILES[$name]['tmp_name'])) {
			$filename = reset($_FILES[$name]['tmp_name']);
		} else {
			$filename = $_FILES[$name]['tmp_name'];
		}

		if (!$filename) {
			return false;
		}

		if (!is_uploaded_file($filename)) {
			return false;
		}

		return true;
	}

	/**
	* Basic upload file
	* @param string $name The name of the file to upload as defined in $_FILES
	* @param string $filename The filename where the file will be uploaded
	* @return bool Returns true if the file was succesfully uploaded
	*/
	public function uploadFile(string $name, string $filename) : bool
	{
		if (!move_uploaded_file($_FILES[$name]['tmp_name'], $filename)) {
			return false;
		}

		return true;
	}

	/**
	* Uploads a file/files
	* @param string $name The $_FILES[$name] used
	* @param string $upload_dir Destination folder
	* @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
	* @param bool $append_suffix If true, will always generate a random character as a suffix for the uploaded filename
	* @param bool $append_suffix_if_file_exists If $append_suffix is false and $append_suffix_if_file_exists is true and a file with the same name with the one being uploaded exists, a suffix will be used nonetheless
	* @param bool $create_subdir If true, a subdir will be automatically be created inside $upload_dir and the file will be uploaded there
	* @param bool $force_array If a single file is uploaded, by default it will be returned as string; If $force_arrray is true, it will be returned as an array
	* @return array Returns the list of files uploaded, if the upload(s) was successful, null if there were errors
	*/
	public function upload(string $name, string $upload_dir, string|array $allowed_extensions = [], bool $append_suffix = false, bool $append_suffix_if_file_exists = true, bool $create_subdir = false, bool $force_array = false) : ?array
	{
		$ok = true;
		$is_array = true;
		$upload_dir = App::sl($upload_dir);
		$uploaded_files = [];

		$this->app->plugins->run('request_upload', $name, $upload_dir, $allowed_extensions, $append_suffix, $append_suffix_if_file_exists, $this);

		if (!isset($_FILES[$name])) {
			$this->uploadHandleError(UPLOAD_ERR_NO_FILE, '');

			return null;
		}

		if (!is_array($_FILES[$name]['name'])) {
			//convert $_FILES[$name] to an array, if it isn't already
			$_FILES[$name]['tmp_name'] = [$_FILES[$name]['tmp_name']];
			$_FILES[$name]['name'] = [$_FILES[$name]['name']];
			$_FILES[$name]['error'] = [$_FILES[$name]['error']];
			$_FILES[$name]['type'] = [$_FILES[$name]['type']];
			$_FILES[$name]['size'] = [$_FILES[$name]['size']];

			$is_array = false;
		}

		$files_count = count($_FILES[$name]['name']);
		if (!$files_count) {
			return null;
		}

		for ($i = 0; $i < $files_count; $i++) {
			if (empty($_FILES[$name]['tmp_name'][$i])) {
				continue;
			}

			if (!$_FILES[$name]['name'][$i]) {
				if ($_FILES[$name]['tmp_name'][$i]) {
					unlink($_FILES[$name]['tmp_name'][$i]);
				}

				continue;
			}

			$file = $this->app->filter->file($_FILES[$name]['name'][$i]);
			$extension = $this->app->file->getExtension($file);

			if ($create_subdir) {
				//create the subdir
				$upload_dir.= $this->app->file->getSubdir($file);
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir);
				}
			}

			$filename = $upload_dir . $file;

			//check if we can upload this type of file
			if (!$this->uploadExtensionIsNotDisallowed($extension)) {
				$this->uploadHandleErrorExtensionIsDisallowed($file);
				unlink($_FILES[$name]['tmp_name'][$i]);

				$ok = false;
				break;
			}
			if (!$this->uploadExtensionIsAllowed($extension, $allowed_extensions, $out_allowed_extensions)) {
				$this->uploadHandleErrorExtensionIsNotAllowed($file, $out_allowed_extensions);
				unlink($_FILES[$name]['tmp_name'][$i]);

				$ok = false;
				break;
			}

			///generate the suffix
			$suffix_str = $this->uploadGetSuffix($filename, $append_suffix, $append_suffix_if_file_exists);
			if ($suffix_str) {
				$filename = $upload_dir . $this->app->file->getFilename($file) . $suffix_str . '.' . $extension;
			}

			if (move_uploaded_file($_FILES[$name]['tmp_name'][$i], $filename)) {
				$uploaded_files[$file] = $filename;
			} else {
				$error_code = $_FILES[$name]['error'][$i];

				$this->app->log->error('Upload Error : ' . $file . ' - ' . $error_code, __FILE__, __LINE__);

				$this->uploadHandleError($error_code, $file);

				unlink($_FILES[$name]['tmp_name'][$i]);

				$ok = false;
				break;
			}
		}

		if ($ok) {
			if (!$force_array) {
				$uploaded_files = current($uploaded_files);
			}

			$this->app->plugins->run('request_upload_success', $uploaded_files, $name, $upload_dir, $this);

			return $uploaded_files;
		} else {
			$this->app->plugins->run('request_upload_error', $uploaded_files, $name, $upload_dir, $this);

			if ($uploaded_files) {
				//there was an error, but we did upload some files; delete it
				foreach ($uploaded_files as $file) {
					if (is_file($file)) {
						unlink($file);
					}
				}
			}

			return null;
		}
	}

	/**
	* Checks if $filename can be uploaded, based on extension
	* @param string $filename The filename to check
	* @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
	* @return bool Returns true if the file can be uploaded
	*/
	public function canUploadFile(string $filename, string|array $allowed_extensions = []) : bool
	{
		$extension = $this->app->file->getExtension($filename);

		if (!$this->uploadExtensionIsNotDisallowed($extension)) {
			return false;
		}
		if (!$this->uploadExtensionIsAllowed($extension, $allowed_extensions)) {
			return false;
		}

		return true;
	}

	/**
	* Checks if $extension is on the list of disallowed extensions
	* @param string $extension The extension
	* @return bool Returns true if the extension is disallowed
	*/
	public function uploadExtensionIsNotDisallowed(string $extension) : bool
	{
		if (in_array($extension, $this->upload_disallowed_extensions)) {
			return false;
		}

		return true;
	}

	/**
	* Checks if $extension can be uploaded based on $allowed_extensions
	* @param string $extension The extension
	* @param string|array $allowed_extensions Array containing the extensions of the file that are allowed to be uploaded. If '*' is passed all types of files are allowed [minus those deemed unsafe]
	* @param array $out_allowed_extensions Variable which lists the extensions which can be uploaded [out]
	* @return bool Returns true if the extension is on the allowed list
	*/
	public function uploadExtensionIsAllowed(string $extension, string|array $allowed_extensions = '*', ?array &$out_allowed_extensions = []) : bool
	{
		$out_allowed_extensions = $allowed_extensions;

		if (!$allowed_extensions) {
			return false;
		}

		if ($allowed_extensions !== '*' && is_array($allowed_extensions)) {
			if (!in_array($extension, $allowed_extensions)) {
				return false;
			}
		}

		return true;
	}

	/**
	* Generates a random upload suffix
	* @param string $filename The filename to generate the suffix for
	* @param bool $append_suffix If true, will always generate a random character as a suffix for the uploaded filename
	* @param bool $append_suffix_if_file_exists If $append_suffix is false and $append_suffix_if_file_exists is true and a file with the same name with the one being uploaded exists, a suffix will be used nonetheless
	* @return string The suffix, if any
	*/
	protected function uploadGetSuffix(string $filename, bool $append_suffix = true, bool $append_suffix_if_file_exists = true) : string
	{
		if (!$append_suffix && $append_suffix_if_file_exists) {
			if (is_file($filename)) {
				return $this->uploadGetSuffixString();
			} else {
				return '';
			}
		}

		return $this->uploadGetSuffixString();
	}

	/**
	* Generates a random upload suffix
	* @return string The suffix
	*/
	protected function uploadGetSuffixString() : string
	{
		$suffix_chars = 18;

		return '-' . $this->app->random->getString($suffix_chars);
	}

	/**
	* Returns the original file name of an uploaded file
	* @param string $file The file
	* @return string $file The uploaded name
	*/
	public function getUploadedName(string $file) : string
	{
		if (!isset($this->uploaded_files[$file])) {
			return '';
		}

		return $this->uploaded_files[$file];
	}

	/**
	* Handler for upload error: extension is disallowed
	* @param string $file The file which triggered the error
	*/
	protected function uploadHandleErrorExtensionIsDisallowed(string $file)
	{
		$this->upload_error = 'upload_error3';
	}

	/**
	* Handler for upload error: extension is not allowed
	* @param string $file The file which triggered the error
	* @param array $allowed_extensions Array with the allowed extensions
	*/
	protected function uploadHandleErrorExtensionIsNotAllowed(string $file, array $allowed_extensions)
	{
		$this->upload_error = 'upload_error2';
	}

	/**
	* Handler for upload error: extension is not allowed
	* @param string $error_code The error code
	* @param string $file The file which triggered the error
	*/
	protected function uploadHandleError(string $error_code, string $file)
	{
		$this->upload_error = 'upload_error1';
	}
}
