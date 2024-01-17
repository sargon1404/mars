<?php
/**
* The Variable Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

use Mars\App;

/**
 * The Variable Hander
 */
class VariableParser
{
	use \Mars\AppTrait;

	/**
	 * @internal
	 */
	protected string $variable_preg = '/(\$[a-z0-9_\.\->#\[\]\'"]*)/is';

	/**
	 * @var array $supported_modifiers Array listing the supported modifiers in the format modifier => [function, priority, escape]
	 */
	protected array $supported_modifiers = [
		//escape modifiers
		'html' => ['$this->app->escape->html', 40],
		'htmlx2' => ['$this->app->escape->htmlx2', 60],
		'js' => ['$this->app->escape->js', 20, false],
		'jsstring' => ['$this->app->escape->jsString', 20, false],
		'path' => ['$this->app->escape->path', 10, false],

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
		'datetime' => ['$this->app->format->datetime', 10],
		'date' => ['$this->app->format->date', 10],
		'time' => ['$this->app->format->time', 10],
		'round' => ['$this->app->format->round', 10],
		'number' => ['$this->app->format->number', 10],
		'size' => ['$this->app->format->size', 10],

		//text modifiers
		'cut' => ['$this->app->text->cut', 10],
		'cut_middle' => ['$this->app->text->cutMiddle', 10],

		//url modifiers
		'http' => ['$this->app->uri->toHttp', 10],
		'https' => ['$this->app->uri->toHttps', 10],
		'ajax' => ['$this->app->uri->addAjax', 10],
	];

	/**
	 * Builds the VariableParser object
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Adds a supported modifier to the list
	 * @param string $name The name of the modifier
	 * @param string $function The name of the function handling the modifier
	 * @param int $priority The priority of the modifier
	 * @param bool $escape If false, the value won't be html escaped
	 * @return $this
	 */
	public function addSupportedModifier(string $name, string $function, int $priority = 10, bool $escape = true)
	{
		$this->supported_modifiers[$name] = [$function, $priority, $escape];

		return $this;
	}

	/**
	 * Removes a supported modifier
	 * @param string $name The name of the modifier
	 * @return $this
	 */
	public function removeSupportedModifier(string $name)
	{
		unset($this->supported_modifiers[$name]);

		return $this;
	}

	/**
	 * @see \Mars\Templates\DriverInterface::parse()
	 * {@inheritdoc}
	 */
	public function parse(string $content, array $params = []) : string
	{
		return preg_replace_callback('/\{\{(.*)\}\}/U', function (array $match) {
			return $this->parseVariable($match);
		}, $content);
	}

	/**
	 * Parses a variable

	 */
	protected function parseVariable(array $match) : string
	{
		[$value, $modifiers] = $this->breakVariable($match[1]);

		$start_pos = strpos($value, '(');
		$end_pos = strrpos($value, ')');

		//is the 'variable' a function?
		if ($start_pos !== false && $end_pos !== false) {
			$value = preg_replace_callback('/([^\(]*)\((.*)\)/s', function (array $match) {
				$var = $this->buildVariable($match[1], false);
				$params = $this->replaceVariables($match[2]);

				return $var . '(' . $params . ')';
			}, $value);

			return $this->applyModifiers($value, $modifiers);
		} else {
			return $this->applyModifiers($this->buildVariable($value), $modifiers);
		}
	}

	/**
	 * Breaks the $var variable into parts: value/modifiers
	 * @param string $var The variable to break
	 * @return array $modifiers The modifiers
	 */
	protected function breakVariable(string $var) : array
	{
		$parts = explode('|', $var);
		$value = trim($parts[0]);
		$modifiers = [];

		if (count($parts) > 1) {
			$modifiers = array_slice($parts, 1);
			$modifiers = array_map('trim', $modifiers);
			$modifiers = array_map('strtolower', $modifiers);
		}

		return [$value, $modifiers];
	}

	/**
	 * Builds a variable from $value. Returns $vars['item'] if $value=item
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
		if (str_contains($value, '.')) {
			$value = preg_replace('/["\'][^"\']*["\'](*SKIP)(*FAIL)|\./i', '->', $value);
		}
		//replace # arrays with [] arrays. Eg: item#prop => item['prop']
		if (str_contains($value, '#')) {
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
	 * Replaces all variables in a string
	 * @param string The string
	 * @return string The string with the replaced vars
	 */
	public function replaceVariables(string $str) : string
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
	protected function applyModifiers(string $value, array $modifiers, bool $apply_escape = true) : string
	{
		//add the de modifier, or the escape modifier, if required
		if ($this->canEscapeModifiers($modifiers, $apply_escape)) {
			$modifiers[] = 'html';
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
}
