<?php
/**
* The Theme Trait
* @package Mars
*/

namespace Mars\Extensions;

use Mars\App;
use Mars\Templates;

/**
* The Theme Trait
* Trait implementing the Theme functionality
*/
trait ThemeTrait
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
	* @var string $templates_path The path for the theme's templates folder
	*/
	protected string $templates_path = '';

	/**
	* @var string $images_path The path for the theme's images folder
	*/
	protected string $images_path = '';

	/**
	* @var string $images_url The url of the theme's images folder
	*/
	protected string $images_url = '';

	/**
	* @var string $cache_path The folder where the cache files are stored
	*/
	protected string $cache_path = '';

	/**
	* @var array $vars The theme's vars are stored here
	*/
	protected array $vars = [];

	/**
	* @var array Array with the list of loaded templates
	*/
	protected array $templates_loaded = [];

	/**
	* @var Template templates The engine used to parse the template
	*/
	protected Templates $templates;

	/**
	* @var string $content The generated content
	*/
	protected string $content = '';

	/**
	* @internal
	*/
	protected static string $base_dir = 'themes';

	/**
	* Prepares the theme
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareDevelopment();
		$this->prepareVars();
	}

	/**
	* Prepares the paths
	*/
	protected function preparePaths()
	{
		parent::preparePaths();

		$this->cache_path = $this->app->cache_path . App::CACHE_DIRS['templates'];
		$this->templates_path = $this->path . App::EXTENSIONS_DIRS['templates'];
		$this->images_path = $this->path . App::EXTENSIONS_DIRS['images'];
		$this->images_url = $this->url . App::EXTENSIONS_DIRS['images'];
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
	* @return static
	*/
	public function addVar(string $name, $value) : static
	{
		$this->vars[$name] = $value;

		return $this;
	}

	/**
	* Adds template variables
	* @param array $vars Adds each element [$name=>$value] from $values as theme variables
	* @return static
	*/
	public function addVars(array $vars) : static
	{
		if (!$vars) {
			return $this;
		}

		foreach ($vars as $name => $value) {
			$this->vars[$name] = $value;
		}

		return $this;
	}

	/**
	* Unsets a theme variable
	* @param string $name The name of the var
	* @return static
	*/
	public function unsetVar(string $name) : static
	{
		unset($this->vars[$name]);

		return $this;
	}

	/**
	* Unsets theme variables
	* @param array $values Array with the name of the vars to unset
	* @return static
	*/
	public function unsetVars(array $values) : static
	{
		foreach ($values as $name) {
			unset($this->vars[$name]);
		}

		return $this;
	}

	/************** TEMPLATES METHODS **************************/

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
	* Renders/Outputs a template, by filename
	* @param string $filename The filename of the template
	* @param array $vars Vars to pass to the template, if any
	*/
	public function renderFilename(string $filename, array $vars = [])
	{
		echo $this->getTemplateFromFilename($filename, $vars);
	}

	/**
	* Loads a template and returns it's content
	* @param string $template The name of the template
	* @param array $vars Vars to pass to the template, if any
	* @param strint $type The template's type, if any
	* @return string The template content
	*/
	public function getTemplate(string $template, array $vars = [], string $type = '') : string
	{
		if ($this->app->config->debug) {
			$this->templates_loaded[] = $template;
		}

		$filename = $this->getTemplateFilename($template);
		$cache_filename = $this->getTemplateCacheFilename($template, $type);

		$content = $this->getTemplateContent($filename, $cache_filename, $vars, ['template' => $template]);

		return $this->app->plugins->filter('theme_get_template', $content, $template, $vars, $type, $this);
	}

	/**
	* Loads a template and returns it's content
	* @param string $filename The filename of the template
	* @param array $vars Vars to pass to the template, if any
	* @param strint $type The template's type, if any
	* @return string The template content
	*/
	public function getTemplateFromFilename(string $filename, array $vars = [], string $type = 'template') : string
	{
		if ($this->app->config->debug) {
			$this->templates_loaded[] = $filename;
		}

		$cache_filename = $this->getTemplateCacheFilename($filename, $type);

		return $this->getTemplateContent($filename, $cache_filename, $vars);
	}

	/**
	* Returns the contents of a template
	* @param string $filename The filename from where the template will be loaded
	* @param string $cache_filename The filename used to cache the template
	* @param array $vars Vars to pass to the template, if any
	* @param array $params Params to pass to the parser
	* @return string The template content
	*/
	protected function getTemplateContent(string $filename, string $cache_filename, array $vars, array $params = []) : string
	{
		if ($vars) {
			$this->addVars($vars);
		}

		if ($this->development || !is_file($cache_filename)) {
			$this->writeTemplate($filename, $cache_filename, ['filename' => $filename] + $params);
		}

		$content = $this->includeTemplate($cache_filename);

		$content = $this->app->plugins->filter('theme_get_template_content', $content, $filename, $cache_filename, $vars, $this);

		return $content;
	}

	/**
	* Returns the filename corresponding to $template
	* @param string $template The name of the template
	* @return string The filename
	*/
	public function getTemplateFilename(string $template) : string
	{
		return $this->templates_path . $template . '.' . App::FILE_EXTENSIONS['templates'];
	}

	/**
	* Loads $filename, parses it and then writes it in the cache folder
	* @param string $filename The filename from where the template will be loaded
	* @param string $cache_filename The filename used to cache the template
	* @param array $params Params to pass to the parser
	* @return bool True if the template was written, false on failure
	*/
	protected function writeTemplate(string $filename, string $cache_filename, array $params) : bool
	{
		$content = file_get_contents($filename);

		if ($content === false) {
			return false;
		}

		$content = $this->parseTemplate($content, $params);

		return file_put_contents($cache_filename, $content);
	}

	/**
	* Parses the template content
	* @param string $content The content to parse
	* @param array $params Params to pass to the parser
	* @return string The parsed content
	*/
	protected function parseTemplate(string $content, array $params) : string
	{
		return $this->templates->parse($content, $params);
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
	* Generates a cache filename for a template
	* @param string $template The name of the template
	* @return string The filename
	*/
	public function getTemplateCacheFilename(string $template, string $type) : string
	{
		$parts = [
			$this->name,
			$template,
			$type,
			$this->app->config->key,
		];

		$parts = array_filter($parts);

		$name = implode('-', $parts);
		$name = trim(str_replace(['/', '.'], '-', $name), '-');

		return $this->cache_path . $name . '.php';
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
	* @param string $content The content to render
	*/
	public function renderContent(string $content)
	{
		$this->content = $content;

		echo $this->getTemplate($this->content_template, ['content' => $content]);
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
	* Outputs code in the <head>
	*/
	public function outputHead()
	{
		$this->outputTitle();
		$this->outputEncoding();
		$this->outputMeta();
		$this->outputRss();

		$this->outputCssUrls('first');
		$this->outputCssUrls('head');

		$this->outputJavascriptUrls('first');
		$this->outputJavascriptUrls('head');

		$this->app->plugins->run('theme_output_head', $this);
	}

	/**
	* Outputs code in the footer
	*/
	public function outputFooter()
	{
		$this->outputCssUrls('footer');

		$this->outputJavascriptUrls('footer');

		$this->app->plugins->run('theme_output_footer', $this);
	}

	/**
	* Outputs the generated content
	*/
	public function outputContent()
	{
		echo $this->content;
	}

	/**
	* Outputs the language code
	*/
	public function outputLangCode()
	{
		echo $this->app->escape->html($this->app->lang->code);
	}

	/**
	* Outputs the page encoding
	*/
	public function outputEncoding()
	{
		echo '<meta charset="' . $this->app->escape->html($this->app->lang->encoding) . '" />' . "\n";
	}

	/**
	* Outputs the title
	*/
	public function outputTitle()
	{
		$title = $this->app->document->title->get();

		$title = $this->app->plugins->filter('theme_output_title', $title);

		echo '<title>' . $this->app->escape->html($title) . '</title>' . "\n";
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

		echo '<script type="text/javascript">' . "\n";
		echo $code . "\n";
		echo '</script>' . "\n";
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
		$this->app->document->css->output($location);
	}

	/**
	* Outputs the loaded javascript files
	* @param bool $location The location of the urls: head|footer
	*/
	public function outputJavascriptUrls(string $location)
	{
		$this->app->document->javascript->output($location);
	}

	/**
	* Outputs the main css file
	*/
	public function outputCssUrl()
	{
		if (!$this->css_output) {
			return;
		}

		$url = $this->app->url_static . $this->css_file;

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

		$url = $this->app->url_static . $this->javascript_file;

		$this->app->javascript->outputUrl($url);
	}

	/**
	* Outputs the meta tags
	*/
	public function outputMeta()
	{
		$this->app->document->meta->output();
	}

	/**
	* Outputs the rss tags
	*/
	public function outputRss()
	{
		$this->app->document->rss->output();
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

		echo '<link rel="shortcut icon" type="image/png" href="' . $this->app->escape->html($icon_url) . '" />' . "\n";
	}

	/**
	* Outputs the execution time
	*/
	public function outputExecutionTime()
	{
		return $this->app->timer->getExecutionTime();
	}

	/**
	* Returns the memory usage
	*/
	public function outputMemoryUsage()
	{
		return round(memory_get_peak_usage(true) / (1024 * 1024), 4);
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

		$this->render('alerts/errors');
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

		$this->render('alerts/messages');
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

		$this->render('alerts/notifications');
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

		$this->render('alerts/warnings');
	}
}
