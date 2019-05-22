<?php
/**
* The Templates Engine Class
* @package Mars
*/

namespace Mars;

/**
* The Templates Engine Class
*
* The currently supported modifiers are:
* raw,js,jscode,lower,upper,url,urlencode,urlrawencode,timestamp,date,date,cut,cut_middle,empty,strip_tags,nl2br,trim,http,https,ajax,to_url,ip,e,escape,round,count,number,dir
*
* Syntax for including subtemplates
* {% include template_name %}
* template_name must not include the .tpl extension
*
* Syntax for IF structures:
* {% if $var %}
* {% elseif %}
* {% else %}
* {% endif %}
*
* Syntax for FOREACH structures
* {% foreach $foo as $bar %} OR {% foreach $foo as $i,$bar %} OR {% foreach $foo as $bar cycle=5 cycle_start=<tr> cycle_end=</tr> %}
* {{ $bar.element1 }}
* {{ $bar.element2 }}
* {% endforeach %}
*/
class Templates
{
	/**
	* @var array $supported_modifiers Array listing the supported modifiers in the format modifier => [function, priority, escape]
	*/
	protected $supported_modifiers = [
		//escape modifiers
		'e' => ['\mars\App::e', 40],
		'escape' => ['\mars\App::e', 40],
		'html' => ['\mars\App::e', 40],
		'de' => ['\mars\App::de', 50],
		'ex2' => ['\mars\App::ex2', 60],
		'js' => ['\mars\App::ejs', 20, false],
		'ejs' => ['\mars\App::ejs', 20, false],
		'jsc' => ['\mars\App::ejsc', 20, false],
		'ejsc' => ['\mars\App::ejsc', 20, false],
		'jscode' => ['\mars\App::ejsc', 20, false],

		//base modifiers
		'nl2br' => ['nl2br', 100],
		'lower' => ['strtolower', 10],
		'upper' => ['strtoupper', 10],
		'urlencode' => ['urlencode', 10],
		'urlrawencode' => ['urlrawencode', 10],
		'count' => ['count', 10],
		'trim' => ['trim', 10],
		'strip_tags' => ['strip_tags', 10],

		//format modifiers
		'timestamp' => ['$this->app->format->timestamp', 10],
		'datetime' => ['$this->app->format->timestamp', 10],
		'date' => ['$this->app->format->date', 10],
		'time' => ['$this->app->format->time', 10],
		'empty' => ['$this->app->format->empty', 10],
		'round' => ['$this->app->format->round', 10],
		'number' => ['$this->app->format->number', 10],
		'size' => ['$this->app->format->size', 10],
		'ip' => ['$this->app->format->ip', 10],
		'to_url' => ['$this->app->format->toUrl', 10],

		//text modifiers
		'cut' => ['$this->app->text->cut', 10],
		'cut_middle' => ['$this->app->text->cutMiddle', 10],

		//url modifiers
		'url' => ['$this->app->escape->url', 10],
		'http' => ['$this->app->uri->addHttp', 10],
		'https' => ['$this->app->uri->addHttps', 10],
		'ajax' => ['$this->app->uri->addAjax', 10],

		//misc
		'dir' => ['$this->app->escape->dir', 10],
	];

	/**
	* @internal
	*/
	protected $double_escape = false;

	/**
	* @internal
	*/
	protected $variable_preg = '/(\$[a-z0-9_\.\->#\[\]\'"]*)/is';

	/**
	* Builds the Template object
	*/
	public function __construct()
	{
		ini_set('pcre.backtrack_limit', 10000000);
	}

	/**
	* Parses the template content and returns it
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parse(string $content) : string
	{
		$content = $this->parseInclude($content);
		$content = $this->parseIf($content);
		$content = $this->parseForeach($content);
		$content = $this->parseVariables($content);

		return $content;
	}

	/**
	* Parses the variables in content
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parseVariables(string $content) : string
	{
		$this->double_escape = true;
		$content = preg_replace_callback('/\{\{\{(.*)\}\}\}/U', [$this, 'parseVariable'], $content);

		$this->double_escape = false;
		$content = preg_replace_callback('/\{\{(.*)\}\}/U', [$this, 'parseVariable'], $content);

		return $content;
	}

	/**
	* Parses a variable. Callback of parseVariables()
	* @param array $match The callback match
	* @return string The parsed variable
	*/
	protected function parseVariable(array $match) : string
	{
		$modifiers = $this->breakVariable($match[1], $value);

		$start_pos = strpos($value, '(');
		$end_pos = strrpos($value, ')');

		//is the 'variable' a function?
		if ($start_pos !== false && $end_pos !== false) {
			$value = preg_replace_callback('/([^\(]*)\((.*)\)/s', function (array $match) {
				$obj = $this->buildVariable($match[1], false);
				$params = $this->replaceVariables($match[2]);

				return $obj . '(' . $params . ')';
			}, $value);

			return $this->applyModifiers($value, $modifiers, false);
		}

		$value = $this->buildVariable($value);

		return $this->applyModifiers($value, $modifiers);
	}

	/**
	* Replaces all variables in a string
	* @param string The string
	* @return string The string with the replaced vars
	*/
	protected function replaceVariables(string $str) : string
	{
		$str = trim($str);

		$str = preg_replace_callback($this->variable_preg, function (array $match) {
			return $this->buildVariable($match[1], false);
		}, $str);

		return $str;
	}

	/**
	* Applies the modifiers from $modifiers to $value. Returns the php code.
	* See the class description for a list of supported modifiers
	* @param string $value The value to which the modifiers will be applied
	* @param array $modifiers Array with the modifiers to be applied
	* @param bool $apply_escape Set to true to escape the value
	* @return string Returns the php code.
	*/
	public function applyModifiers(string $value, array $modifiers, bool $apply_escape = true) : string
	{
		//add the de modifier, or the escape modifier, if required
		if ($this->double_escape) {
			$modifiers[] = 'ex2';
		} elseif ($this->canEscapeModifiers($modifiers, $apply_escape)) {
			$modifiers[] = 'escape';
		}

		$list = $this->getModifiersList($modifiers);

		return '<?php echo ' . $this->buildModifiers($value, $list) . ';?>';
	}

	/**
	* Determines if, based on modifiers, the value can be escaped
	* @param array $modifiers Array with the modifiers to be applied
	* @param bool $apply_escape Set to true to escape the value
	* @return bool
	*/
	protected function canEscapeModifiers(array $modifiers, bool $apply_escape = true) : bool
	{
		if (!$apply_escape) {
			return false;
		}
		if (in_array('raw', $modifiers)) {
			return false;
		}

		foreach ($modifiers as $modifier) {
			if (!isset($this->supported_modifiers[$modifier])) {
				continue;
			}
			if (!isset($this->supported_modifiers[$modifier][2])) {
				continue;
			}
			if (!$this->supported_modifiers[$modifier][2]) {
				return false;
			}
		}

		return true;
	}

	/**
	* Returns the list of functions to apply
	* @param array $modifiers The modifiers
	* @return array The list of functions
	*/
	protected function getModifiersList(array $modifiers) : array
	{
		$list = [];
		foreach ($modifiers as $modifier) {
			if (isset($this->supported_modifiers[$modifier])) {
				$list[] = $this->supported_modifiers[$modifier];
			}
		}

		//sort the list by priority
		uasort($list, function ($a, $b) {
			return $b[1] <=> $a[1];
		});

		return array_unique(array_column($list, 0));
	}

	/**
	* Builds the modifiers functions
	* @param string $value The value
	* @param array $list The list of functions
	* @return string The modifiers functions string
	*/
	protected function buildModifiers($value, array $list) : string
	{
		if (!$list) {
			return $value;
		}

		end($list);
		$last = key($list);
		$count = count($list);

		$list[$last].= '(' . $value;

		return implode('(', $list) . str_repeat(')', $count);
	}

	/**
	* Breaks the $var variable into parts: value/modifiers
	* @param string $var The variable to break
	* @param string $value The value part [out]
	* @return array $modifiers The modifiers
	*/
	protected function breakVariable(string $var, ?string &$value) : array
	{
		$modifiers = [];
		$parts = explode('|', $var);
		$value = trim($parts[0]);

		if (count($parts) > 1) {
			$modifiers = array_slice($parts, 1);
			$modifiers = array_map('trim', $modifiers);
			$modifiers = array_map('strtolower', $modifiers);
		}

		return $modifiers;
	}

	/**
	* Builds a variable from $value. Returns $vars['item'] if $value= item
	* @param string $value The value
	* @param bool $parse_lang If true, and $value isn't a variable, will return the language string
	* @return string The variable
	*/
	protected function buildVariable(string $value, bool $parse_lang = true) : string
	{
		//if we don't have a $ as the first char, this is a language string
		if ($value[0] != '$') {
			if ($parse_lang) {
				return "\$strings['{$value}']";
			} else {
				return $value;
			}
		}

		$value = ltrim($value, '$');

		//replace . with ->, if not inside quotes
		if (strpos($value, '.') !== false) {
			$value = preg_replace('/["\'][^"\']*["\'](*SKIP)(*FAIL)|\./i', '->', $value);
		}

		//replace # arrays with [] arrays. Eg: item#prop => item['prop']
		if (strpos($value, '#') !== false) {
			$value = preg_replace('/#([^\-\[#]*)/s', "['$1']", $value);
		}

		$o_pos = strpos($value, '->');
		$a_pos = strpos($value, '[');
		if ($o_pos === false && $a_pos === false) {
			//scalar value
			return '$vars[\'' . $value . '\']';
		} else {
			$pos = $o_pos;
			if ($a_pos && $o_pos === false) {
				$pos = $a_pos;
			} elseif ($o_pos && $a_pos === false) {
				$pos = $o_pos;
			} else {
				if ($a_pos < $o_pos) {
					$pos = $a_pos;
				}
			}

			$var_name = substr($value, 0, $pos);

			return '$vars[\'' . $var_name . '\']' . substr($value, $pos);
		}
	}

	/**
	* Parses $content for subtemplates
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parseInclude(string $content) : string
	{
		return preg_replace_callback('/\{\%\s*include(.*)\%\}/U', [$this, 'parseIncludeCallback'], $content);
	}

	/**
	* Callback for parse_include
	* @param array $match Callback match
	* @return string
	*/
	protected function parseIncludeCallback(array $match) : string
	{
		$template_name = trim($match[1]);
		if (!$template_name) {
			return '';
		}

		return '<?php $this->renderSubtemplate(\'' . $template_name . '\');?>';
	}

	/**
	* Parses $content for IF structures
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parseIf(string $content) : string
	{
		$content = preg_replace_callback('/\{\%\s*if(.*)\s*\%\}/iU', [$this, 'parseIfCallback'], $content);
		$content = preg_replace_callback('/\{\%\s*elseif(.*)\s*\%\}/isU', [$this, 'parseElseifCallback'], $content);
		$content = preg_replace('/\{\%\s*else\s*\%\}/iU', '<?php } else { ?>', $content);
		$content = preg_replace('/\{\%\s*endif\s*\%\}/iU', '<?php } ?>', $content);

		return $content;
	}

	/**
	* Callback for parse_if
	* @param array $match Callback match
	* @return string
	*/
	protected function parseIfCallback(array $match) : string
	{
		return '<?php if(' . $this->getCondition($match) . '){ ?>';
	}

	/**
	* Callback for parse_if
	* @param array $match Callback match
	* @return string
	*/
	protected function parseElseifCallback(array $match) : string
	{
		return '<?php } elseif(' . $this->getCondition($match) . '){?>';
	}

	/**
	* Returns an if condition from $match
	* @param array $match Callback match
	* @return string
	*/
	protected function getCondition(array $match) : string
	{
		$condition = $this->trimParentheses($match[1]);

		$condition = preg_replace_callback($this->variable_preg, function (array $match) {
			return $this->buildVariable($match[1], false);
		}, $condition);

		return $condition;
	}

	/**
	* Trims the parentheses of string, if any
	* @param string $str The string
	* @return string
	*/
	protected function trimParentheses(string $str) : string
	{
		$str = trim($str);

		if ($str[0] == '(') {
			$str = substr($str, 1);
			$len = strlen($str);

			if ($str[$len - 1] == ')') {
				$str = substr($str, 0, $len - 1);
			}

			$str = trim($str);
		}

		return $str;
	}

	/**
	* Parses $content for FOREACH structures
	* @param string $content The content to parse
	* @return string The parsed content
	*/
	public function parseForeach(string $content) : string
	{
		$content = preg_replace_callback('/\{\%\s*foreach(.*) as (.*)\%\}/isU', [$this, 'parseForeachCallback'], $content);
		$content = preg_replace('/\{\%\s*endforeach\s*\%\}/U', '<?php } $this->restoreForeachVar();  }?>' . "\n", $content);

		return $content;
	}

	/**
	* Callback for parse_foreach
	* @param array $match Callback match
	* @return string
	*/
	protected function parseForeachCallback(array $match) : string
	{
		$var = $this->buildVariable(trim($match[1], ' ('));
		$expression = trim($match[2], ' )');

		$key = '';
		$value = '';

		if (strpos($expression, '=>') !== false) {
			//there is also an expression in the foreach
			$parts = explode('=>', $expression);
			$key = ltrim(trim($parts[0]), '$');
			$value = ltrim(trim($parts[1]), '$');
		} else {
			$value = ltrim($expression, '$');
		}

		$loop_index = md5($key . $value . time() . mt_rand(0, 999999)) . mt_rand(0, 999999);

		$key_data = '$' . $key;
		$value_data = '$' . $value;
		if (!$key) {
			$key_data = 'null';
		}

		$code = '<?php if(' . $var . '){ ' . "\n";
		$code.= '$this->setForeachData(\'' . $loop_index . '\', \'' . $key . '\', \'' . $value . '\');' . "\n";

		if ($key) {
			$code.= 'foreach(' . $var . ' as $' . $key . ' => $' . $value . '){' . "\n";
		} else {
			$code.= 'foreach(' . $var . ' as $' . $value . '){' . "\n";
		}

		$code.= '$this->loopForeach(\'' . $loop_index . '\', \'' . $key . '\',' . $key_data . ',\'' . $value . '\',' . $value_data . '); ' . "\n";

		$code.= '?>';

		return $code;
	}
}
