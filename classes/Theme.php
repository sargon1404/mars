<?php
/**
* The Theme "Class"
* @package Mars
*/

namespace Mars;

/**
* The Theme "Class"
* Trait implementing the Theme functionality
*/
trait Theme
{
	/**
	* @var string $header_template The template which will be used to render the header
	*/
	public string $header_template = 'header';

	/**
	* @var string $footer_template The template which will be used to render the footer
	*/
	public string $footer_template = 'footer';

	/**
	* @var string $content_template The template which will be used to render the content
	*/
	public string $content_template = 'content';

	/**
	* @var string $layout The layout used to render the theme, if any
	*/
	public string $layout = '';

	/**
	* @var string $templates_dir The path for the theme's templates folder
	*/
	public string $templates_dir = '';

	/**
	* @var string $images_dir The path for the theme's images folder
	*/
	public string $images_dir = '';

	/**
	* @var string $images_url The url of the theme's images folder
	*/
	public string $images_url = '';

	/**
	* @var array $vars The theme's vars are stored here
	*/
	public array $vars = [];

	/**
	* @var bool $css_output If true, will output the main css file
	*/
	public bool $css_output = true;

	/**
	* @var bool $javascript_output If true, will output the main js file
	*/
	public bool $javascript_output = true;

	/**
	* @var array Array with the list of loaded templates
	*/
	protected array $templates_loaded = [];

	/**
	* @var Template $engine The engine used to parse the template
	*/
	protected ?Templates $engine = null;

	/**
	* @internal
	*/
	protected $obj = null;

	/**
	* @internal
	*/
	protected array $objs = [];

	/**
	* @var array $foreach_keys Array where existing vars with the same name as a foreach key are temporarily stored
	*/
	protected array $foreach_keys = [];

	/**
	* @var array $foreach_values Array where existing vars with the same name as a foreach value are temporarily stored
	*/
	protected array $foreach_values = [];

	/**
	* @var array $foreach_loops Array where existing loop counts are temporarily stored
	*/
	protected array $foreach_loops = [];

	/**
	* @var array $foreach_loops_count Array storing the loop index for each foreach cycle
	*/
	protected array $foreach_loops_count = [];

	/**
	* @var string $css_file The name of the main css file
	*/
	protected string $css_file = 'style.css';

	/**
	* @var string $javascript_file The name of the main js file
	*/
	protected string $javascript_file = 'javascript.js';

	/**
	* @var string $cache_dir The folder where the cache files are stored
	*/
	protected string $cache_dir = '';

	/**
	* @var string $cache_url The url pointing to the folder where the cache files are stored
	*/
	protected string $cache_url = '';

	/**
	* @var string $templates_cache_dir The folder where the templates will be cached
	*/
	protected string $templates_cache_dir = '';

	/**
	* @internal
	*/
	protected static string $base_dir = 'themes';


	/**
	* Adds a supported modifier to the list
	* @param string $name The name of the modifier
	* @param string $function The name of the function handling the modifier
	* @param int $priority The priority of the modifier
	* @param bool $escape If false, the value won't be html escaped
	* @return $this
	*/
	public function addSupportedModifier(string $name, string $function, int $priority, bool $escape = true)
	{
		$this->engine->addSupportedModifier($name, $function, $priority, $escape);

		return $this;
	}

	/**
	* Removes a supported modifier
	* @param string $name The name of the modifier
	* @return $this
	*/
	public function removeSupportedModifier(string $name)
	{
		$this->engine->removeSupportedModifier($name);

		return $this;
	}

	/**
	* Prepares the theme
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
		$this->prepareProperties();
		$this->prepareVars();
	}

	/**
	* Prepares the paths
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		$this->cache_dir = $this->app->cache_dir;
		$this->cache_url = $this->app->cache_url;
		$this->templates_cache_dir = $this->cache_dir . App::CACHE_DIRS['templates'];
		$this->templates_dir = $this->dir . App::EXTENSIONS_DIRS['templates'];
		$this->images_dir = $this->dir . App::EXTENSIONS_DIRS['images'];
		$this->images_url = $this->base_url . App::EXTENSIONS_DIRS['images'];
	}

	/**
	* Prepares the properties
	*/
	protected function prepareProperties()
	{
		$this->css_output = $this->app->config->css_output;
		$this->javascript_output = $this->app->config->javascript_output;
	}

	/**
	* Sets the theme vars
	*/
	protected function prepareVars()
	{
		$this->addVar('app', $this->app);
		$this->addVar('this', $this);
		$this->addVar('theme', $this);
		$this->addVar('config', $this->app->config);

		$this->addVar('html', $this->app->html);
		$this->addVar('ui', $this->app->ui);
		$this->addVar('uri', $this->app->uri);
		$this->addVar('escape', $this->app->escape);
		$this->addVar('format', $this->app->format);

		$this->addVar('plugins', $this->app->plugins);

		$this->addVar('request', $this->app->request);
		$this->addVar('get', $this->app->request->get);
		$this->addVar('post', $this->app->request->post);
	}

	/**
	* Returns the list of loaded templates
	* @return array
	*/
	public function getLoadedTemplates() : array
	{
		return $this->templates_loaded;
	}

	/***************** VARS METHODS *********************************/

	/**
	* Adds a theme variable.
	* @param string $name The name of the var
	* @param mixed $value The value of the var
	* @return $this
	*/
	public function addVar(string $name, $value)
	{
		$this->vars[$name] = $value;

		return $this;
	}

	/**
	* Adds a theme variable,by reference
	* @param string $name The name of the var
	* @param mixed $value The value of the var
	* @return $this
	*/
	public function addVarr(string $name, &$value)
	{
		$this->vars[$name] = &$value;

		return $this;
	}

	/**
	* Adds template variables
	* @param array $values Adds each element [$name=>$value] from $values as theme variables
	* @return $this
	*/
	public function addVars(array $values)
	{
		if (!$values) {
			return $this;
		}

		foreach ($values as $name => $value) {
			$this->vars[$name] = $value;
		}

		return $this;
	}

	/**
	* Appends to a theme variable
	* @param string $name The name of the var
	* @param mixed $value The value to append
	* @return $this
	*/
	public function appendToVar(string $name, $value)
	{
		if (!isset($this->vars[$name])) {
			$this->vars[$name] = '';
		}

		$this->vars[$name].= $value;

		return $this;
	}

	/**
	* Unsets a theme variable
	* @param string $name The name of the var
	* @return $this
	*/
	public function unsetVar(string $name)
	{
		unset($this->vars[$name]);

		return $this;
	}

	/**
	* Unsets theme variables
	* @param array $values Array with the name of the vars to unset
	* @return $this
	*/
	public function unsetVars(array $values)
	{
		foreach ($values as $name) {
			unset($this->vars[$name]);
		}

		return $this;
	}

	/************** TEMPLATES METHODS **************************/

	/**
	* Alias for render
	* Renders/Outputs a template
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	*/
	public function renderTemplate(string $template, array $vars = [])
	{
		$this->render($template, $vars);
	}

	/**
	* Renders/Outputs a template
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	*/
	public function render(string $template, array $vars = [])
	{
		echo $this->getTemplate($template, $vars);
	}

	/**
	* Loads a template and returns it's content
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	* @return string The template content
	*/
	public function getTemplate(string $template, array $vars = []) : string
	{
		if ($vars) {
			$this->addVars($vars);
		}

		if ($this->app->config->debug) {
			$this->templates_loaded[] = $template;
		}

		$current_obj = $this->obj;
		$this->unsetObj();

		$cache_filename = $this->getTemplateCacheFilename($template);

		if ($this->development || !is_file($cache_filename)) {
			$filename = $this->getTemplateFilename($template);

			$this->writeTemplate($filename, $cache_filename);
		}

		$content = $this->includeTemplate($cache_filename);

		$this->setObj($current_obj);

		$content = $this->app->plugins->filter('theme_get_template', $content, $template, $this);

		return $content;
	}

	/**
	* Caches, if required, and returns the content of a template
	* @param string $filename The filename from where the template will be loaded
	* @param string $cache_filename The filename used to cache the template
	* @param bool $development If true, the template won't be cached
	* @return string The template content
	*/
	protected function getTemplateContent(string $filename, string $cache_filename, bool $development = false) : string
	{
		if ($this->development || !is_file($cache_filename) || $development) {
			$this->writeTemplate($filename, $cache_filename);
		}

		return $this->includeTemplate($cache_filename);
	}

	/**
	* Loads a template and returns it's content from $filename
	* @param string $filename The filename from where the template will be loaded
	* @param bool $development If true, the template won't be cached
	* @return string The template content
	*/
	public function getTemplateFromFilename(string $filename, bool $development = false) : string
	{
		$filename_rel = $this->app->file->getRel($filename);
		if ($this->app->config->debug) {
			$this->templates_loaded[] = $filename_rel;
		}

		$cache_filename = $this->getItemCacheFilename('file', '', $filename_rel);

		return $this->getTemplateContent($filename, $cache_filename, $development);
	}

	/**
	* Loads $filename, parses it and then writes it in the cache folder
	* @param string $filename The filename from where the template will be loaded
	* @param string $cache_filename The filename used to cache the template
	* @return bool True if the template was written, false on failure
	*/
	protected function writeTemplate(string $filename, string $cache_filename) : bool
	{
		$content = file_get_contents($filename);

		if ($content === false) {
			return false;
		}

		$content = $this->parseTemplate($content);

		return file_put_contents($cache_filename, $content);
	}

	/**
	* Returns the filename corresponding to $template
	* @param string $template The name of the template
	* @return string The filename
	*/
	public function getTemplateFilename(string $template) : string
	{
		return $this->templates_dir . $template . '.' . App::FILE_EXTENSIONS['templates'];
	}

	/**
	* Returns the filename corresponding to $template
	* @param string $dir The dir where the template is located
	* @param string $template The name of the template
	* @return string The filename
	*/
	public function buildTemplateFilename(string $dir, string $template)
	{
		return App::sl($dir) . $template . '.' . App::FILE_EXTENSIONS['templates'];
	}

	/**
	* Generates a cache filename for a template
	* @param string $template The name of the template
	* @return string The filename
	*/
	public function getTemplateCacheFilename(string $template) : string
	{
		$parts = [
			$this->name,
			$this->cleanCacheFilenamePart($this->layout),
			$this->cleanCacheFilenamePart($template),
			$this->app->device->type,
			$this->app->config->key,
			'theme'
		];

		return $this->getCacheFilename($parts);
	}

	/**
	* Generates a cache filename for an custom item
	* @param string $type The item's type
	* @param string $name The item's name
	* @param string $template The name of the template
	* @param string $layout The layout, if any
	* @return string The filename
	*/
	public function getItemCacheFilename(string $type, string $name, string $template, string $layout = '') : string
	{
		$parts = [
			$this->cleanCacheFilenamePart($type),
			$this->cleanCacheFilenamePart($name),
			$this->cleanCacheFilenamePart($layout),
			$this->cleanCacheFilenamePart($template),
			$this->app->device->type,
			$this->app->config->key,
			'ext'
		];

		return $this->getCacheFilename($parts);
	}

	/**
	* Returns the filename under which a file will be cached
	* @param array $parts The cache parts
	* @return string The filename
	*/
	protected function getCacheFilename(array $parts) : string
	{
		//filter out the empty parts
		$parts = array_filter($parts);

		return $this->templates_cache_dir . implode('-', $parts) . '.php';
	}

	/**
	* Cleans a part used in a filename, when caching
	* @param string $part The part to clean
	* @return string The cleaned part
	*/
	protected function cleanCacheFilenamePart($part) : string
	{
		return trim(str_replace(['/', '.'], '-', $part), '-');
	}

	/**
	* Parses the template content
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	protected function parseTemplate(string $content) : string
	{
		return $this->engine->parse($content);
	}

	/**
	* Includes a template and returns it's content
	* @param string $filename The filename of the template
	* @return string The template's content
	*/
	protected function includeTemplate(string $filename) : string
	{
		$app = $this->app;
		$strings = &$this->app->lang->strings;
		$vars = &$this->vars;

		ob_start();

		include($filename);

		return ob_get_clean();
	}

	/**
	* Returns a subtemplate as a result of an {% include subtemplate_name %}
	* @param string $subtemplate The name of the subtemplate
	* @return string The subtemplate's content
	*/
	public function getSubtemplate(string $subtemplate) : string
	{
		if (!$this->obj) {
			return $this->getTemplate($subtemplate);
		}

		$template = '';
		$layout = '';

		//split the subtemplate name in case the layout is also specified
		$parts = explode('/', $subtemplate);

		if (count($parts) == 1) { //only the template name is specified
			$template = $parts[0];
		} else {
			if (count($parts) > 2) {
				//more than 2 parts specified,most likely a slight syntax error ( /layout/template instead of layout/template),which we'll silently try to fix
				if ($parts[0] == '') {
					array_shift($parts);
				}
			}

			$layout = $parts[0];
			$template = $parts[1];
			if (!$layout) {
				$layout = null;
			}
		}

		return $this->obj->getTemplate($template, $layout);
	}

	/**
	* Renders a subtemplate
	* @param string $subtemplate The name of the subtemplate
	*/
	public function renderSubtemplate(string $subtemplate)
	{
		echo $this->getSubtemplate($subtemplate);
	}

	/**************** RENDER METHODS *************************************/

	/**
	* Outputs the header
	*/
	public function renderHeader()
	{
		echo $this->getTemplate($this->header_template);
	}

	/**
	* Outputs the content template
	*/
	public function renderContent()
	{
		echo $this->getTemplate($this->content_template);
	}

	/**
	* Outputs the footer
	*/
	public function renderFooter()
	{
		echo $this->getTemplate($this->footer_template);
	}

	/**************** OUTPUT METHODS *************************************/

	/**
	* Outputs the system generated content
	*/
	public function outputContent()
	{
		echo $this->app->content;
	}

	/**
	* Outputs the language code
	*/
	public function outputLangCode()
	{
		echo App::e($this->app->lang->code);
	}

	/**
	* Outputs the page encoding
	*/
	public function outputEncoding()
	{
		echo '<meta charset="' . App::e($this->app->lang->encoding) . '" />' . "\n";
	}

	/**
	* Outputs the title
	*/
	public function outputTitle()
	{
		$title = $this->app->title->get();

		$title = $this->app->plugins->filter('theme_output_title', $title);

		echo '<title>' . App::e($title) . '</title>' . "\n";
	}

	/**
	* Outputs the start tag for javascript inline code
	*/
	public function outputJavascriptCodeStart()
	{
		echo '<script type="text/javascript">' . "\n";
	}

	/**
	* Outputs the start tag for javascript inline code
	*/
	public function outputJavascriptCodeEnd()
	{
		echo '</script>' . "\n";
	}

	/**
	* Outputs javascript inline code
	* @param string $code The js code to output
	*/
	public function outputJavascriptCode(string $code)
	{
		if (!$code) {
			return;
		}

		$this->outputJavascriptCodeStart();
		echo $code . "\n";
		$this->outputJavascriptCodeEnd();
	}

	/**
	* Outputs css inline code
	* @param string $code The js code to output
	*/
	public function outputCssCode(string $code)
	{
		if (!$code) {
			return;
		}

		echo '<style type="text/css">' . "\n";
		echo $code . "\n";
		echo '</style>' . "\n";
	}

	/**
	* Outputs the loaded css files
	* @param bool $location The location of the urls: head|footer
	*/
	public function outputCssUrls(string $location)
	{
		$this->app->css->output($location);
	}

	/**
	* Outputs the loaded javascript files
	* @param bool $location The location of the urls: head|footer
	*/
	public function outputJavascriptUrls(string $location)
	{
		$this->app->javascript->output($location);
	}

	/**
	* Outputs code in the <head>
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();

		$this->outputCssUrls('first');
		$this->outputCssUrl();
		$this->outputCssUrls('head');

		$this->outputJavascriptUrls('first');
		$this->outputJavascriptUrl();
		$this->outputJavascriptUrls('head');

		$this->outputMeta();
		$this->outputRss();
	}

	/**
	* Outputs code in the <footer>
	*/
	public function outputFooter()
	{
		$this->outputCssUrls('footer');
		$this->outputJavascriptUrls('footer');
	}

	/**
	* Outputs the main css file
	*/
	public function outputCssUrl()
	{
		if (!$this->css_output) {
			return;
		}

		$url = $this->url_static . $this->css_file;

		$this->app->css->outputUrl($url);
	}

	/**
	* Outputs the main javascript file
	*/
	public function outputJavascriptUrl()
	{
		if (!$this->javascript_output) {
			return;
		}

		$url = $this->url_static . $this->javascript_file;

		$this->app->javascript->outputUrl($url);
	}

	/**
	* Outputs the meta tags
	*/
	public function outputMeta()
	{
		$this->app->meta->output();
	}

	/**
	* Outputs the rss tags
	*/
	public function outputRss()
	{
		$this->app->rss->output();
	}

	/**
	* Outputs the favicon
	* @param string $icon_url The url of the png icon
	*/
	public function outputFavicon(string $icon_url = '')
	{
		if (!$icon_url) {
			$icon_url = $this->app->url_static . 'favicon.png';
		}

		echo '<link rel="shortcut icon" type="image/png" href="' . App::e($icon_url) . '" />' . "\n";
	}

	/**
	* Outputs the execution time
	*/
	public function outputExecutionTime()
	{
		echo $this->app->timer->getExecutionTime();
	}

	/**
	* Outputs the memory usage
	*/
	public function outputMemoryUsage()
	{
		echo round(memory_get_peak_usage(true) / (1024 * 1024), 4);
	}

	/**************** OUTPUT MESSAGES *************************************/

	/**
	* Outputs all the alers: messages/errors/notifications/warnings
	*/
	public function outputAlerts()
	{
		$this->outputMessages();
		$this->outputErrors();
		$this->outputNotifications();
		$this->outputWarnings();
	}

	/**
	* Outputs the errors
	*/
	public function outputErrors()
	{
		$errors = $this->getErrors();
		if (!$errors) {
			return;
		}

		$this->addVar('errors', $errors);

		$this->renderTemplate('alerts/errors');
	}

	/**
	* Returns the errors
	* @return array The errors, if any
	*/
	public function getErrors() : array
	{
		$errors = $this->app->errors->get();
		if (!$errors) {
			return [];
		}

		$max_errors = 5;
		$errors_count = count($errors);

		//display only the first $max_errors errors.
		if ($errors_count > $max_errors) {
			$errors = array_slice($errors, 0, $max_errors);
			$errors[] = '....................';
		}

		return $errors;
	}

	/**
	* Outputs the messages
	*/
	public function outputMessages()
	{
		if ($this->app->errors->count()) {
			return;
		}

		$messages = $this->app->messages->get();
		if (!$messages) {
			return;
		}

		$this->addVar('messages', $messages);

		$this->renderTemplate('alerts/messages');
	}

	/**
	* Outputs the notifications
	*/
	public function outputNotifications()
	{
		$notifications = $this->app->notifications->get();
		if (!$notifications) {
			return;
		}

		$this->addVar('notifications', $notifications);

		$this->renderTemplate('alerts/notifications');
	}

	/**
	* Outputs the warnings
	*/
	public function outputWarnings()
	{
		$warnings = $this->app->warnings->get();
		if (!$warnings) {
			return;
		}

		$this->addVar('warnings', $warnings);

		$this->renderTemplate('alerts/warnings');
	}

	/**************TEMPLATES INNER METHODS**************************/

	/**
	* Sets the context object
	* @internal
	*/
	public function setObj($obj)
	{
		$this->obj = $obj;

		array_push($this->objs, $obj);
	}

	/**
	* @internal
	*/
	public function restoreObj()
	{
		array_pop($this->objs);

		$this->obj = end($this->objs);
	}

	/**
	* @internal
	*/
	public function unsetObj()
	{
		$this->obj = null;
	}

	/**
	* Sets a foreach key & value as vars
	* @param string $loop_index The name of the loop index
	* @param string $key The name of the key
	* @param string $value The name of the value
	*/
	protected function setForeachData(string $loop_index, string $key, string $value)
	{
		$this->foreach_loops_count[$loop_index] = -1;

		if ($key) {
			if (isset($this->vars[$key])) {
				$this->foreach_keys[$key] = $this->vars[$key];
			} else {
				$this->foreach_keys[$key] = '';
			}
		}

		if (isset($this->vars[$value])) {
			$this->foreach_values[$value] = $this->vars[$value];
		} else {
			$this->foreach_values[$value] = '';
		}

		$this->foreach_loops[] = $this->vars['loop_index'] ?? -1;
	}

	/**
	* Loops over a foreach construct
	* @param string $loop_index The name of the loop index
	* @param string $key The name of the key
	* @param mixed $key_data The key's data
	* @param string $value The name of the value
	* @param mixed $value_data The value's data
	*/
	protected function loopForeach(string $loop_index, string $key, $key_data, string $value, $value_data)
	{
		$this->foreach_loops_count[$loop_index]++;

		$this->vars['loop_index'] = $this->foreach_loops_count[$loop_index];

		if ($key) {
			$this->vars[$key] = $key_data;
		}

		$this->vars[$value] = $value_data;
	}

	/**
	* Restores the key/value vars to the previous values
	*/
	protected function restoreForeachVar()
	{
		$keys = array_keys($this->foreach_keys);
		$name = array_pop($keys);
		$value = array_pop($this->foreach_keys);
		$this->vars[$name] = $value;

		$keys = array_keys($this->foreach_values);
		$name = array_pop($keys);
		$value = array_pop($this->foreach_values);
		$this->vars[$name] = $value;

		$value = array_pop($this->foreach_loops);

		if ($value != -1) {
			$this->vars['loop_index'] = $value;
		}
	}

	/**
	* Outputs $str at each foreach loop interval matching $interval
	* @param int $interval The interval to output the string at
	* @param string $str The string to output
	* @return string
	*/
	public function cycle(int $interval, string $str, $skip_zero = false) : string
	{
		if ($skip_zero && !$this->vars['loop_index']) {
			return '';
		}

		if ($this->vars['loop_index'] % $interval == 0) {
			return $str;
		}

		return '';
	}

	/**
	* Outputs $str as the start of a cycle
	* @param int $interval The interval to output the string at
	* @param string $str The string to output
	* @return string
	*/
	public function cycleStart(int $interval, string $str) : string
	{
		return $this->cycle($interval, $str);
	}

	/**
	* Outputs $str as the end of a cycle
	* @param int $interval The interval to output the string at
	* @param string $str The string to output
	* @return string
	*/
	public function cycleEnd(int $interval, string $str) : string
	{
		if (($this->vars['loop_index'] + 1) % $interval == 0) {
			return $str;
		}

		return '';
	}

	/**
	* Outputs $str as the end of a cycle
	* @param int $interval The interval to output the string at
	* @param string $str The string to output
	* @return string
	*/
	public function cycleFinish(int $interval, string $str) : string
	{
		if (($this->vars['loop_index'] + 1) % $interval != 0) {
			return $str;
		}

		return '';
	}

	/**
	* Repeats a string
	* @param string $str The string to repeat
	* @param int $multiplier The number of time the string should be repeated
	* @param string $str_end Will output $str_end if muliplier is not zero
	* @return string
	*/
	public function repeat(string $str, int $multiplier, string $str_end = '') : string
	{
		$str = str_repeat($str, $multiplier);

		if ($multiplier) {
			$str.= $str_end;
		}

		return $str;
	}
}
