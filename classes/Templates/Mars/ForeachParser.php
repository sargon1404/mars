<?php
/**
* The Foreach Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

/**
* The Foreach Hander
*/
class ForeachParser
{
	use \Mars\AppTrait;
	
	/**
	* @var array $foreach_keys Array where existing vars with the same name as a foreach key are temporarily stored
	*/
	protected array $foreach_keys = [];

	/**
	* @var array $foreach_values Array where existing vars with the same name as a foreach value are temporarily stored
	*/
	protected array $foreach_values = [];

	/**
	* @see \Mars\Templates\DriverInterface::parse()
	* {@inheritdoc}
	*/
	public function parse(string $content, array $params = []) : string
	{
		$content = preg_replace_callback('/\{\%\s*foreach(.*) as (.*)\%\}/isU', function (array $match) {
			$variable_parser = new VariableParser($this->app);

			$variable = $variable_parser->replaceVariables(trim($match[1]));
			$expression = trim($match[2]);
			
			$key = '';
			$value = '';
			if (str_contains($expression, '=>')) {
				//there is also an expression in the foreach
				$parts = explode('=>', $expression);
				$key = ltrim(trim($parts[0]), '$');
				$value = ltrim(trim($parts[1]), '$');
			} else {
				$value = ltrim($expression, '$');
			}
			
			$key_data = '$' . $key;
			$value_data = '$' . $value;
			if (!$key) {
				$key_data = 'null';
			}
			
			$code = '<?php if(' . $variable . '){ ';
			$code.= '$this->templates->driver->getHandler(\'foreach\')->setForeachData(\'' . $key . '\', \'' . $value . '\');' . "\n";
			$code.= 'foreach(' . $variable . ' as ' . $variable_parser->replaceVariables($expression) . ') {' . "\n";
			$code.= ' ?>' . "\n";
			
			return $code;
		}, $content);

		$content = preg_replace('/\{\%\s*endforeach\s*\%\}/U', '<?php } $this->templates->driver->getHandler(\'foreach\')->restoreForeachData(); }?>', $content);

		return $content;
	}
	
	/**
	* Sets a foreach key & value as vars
	* @param string $key The name of the key
	* @param string $value The name of the value
	*/
	public function setForeachData(string $key, string $value)
	{
		if ($key) {
			$this->foreach_keys[$key] = $this->app->theme->getVar($key);
		}

		$this->foreach_values[$value] = $this->app->theme->getVar($value);
	}
	
	/**
	* Restores the key/value vars to the previous values
	*/
	public function restoreForeachData()
	{
		$keys = array_keys($this->foreach_keys);
		$name = array_pop($keys);
		$value = array_pop($this->foreach_keys);
		
		if ($value !== null) {
			$this->app->theme->addVar($name, $value);
		}

		$keys = array_keys($this->foreach_values);
		$name = array_pop($keys);
		$value = array_pop($this->foreach_values);
		
		if ($value !== null) {
			$this->app->theme->addVar($name, $value);
		}
	}
}
