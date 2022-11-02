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
	* @see \Mars\Templates\DriverInterface::parse()
	* {@inheritdoc}
	*/
	public function parse(string $content, array $params = []) : string
	{
		$content = preg_replace_callback('/\{\%\s*foreach(.*) as (.*)\%\}/isU', function (array $match) {
			$variable_parser = new VariableParser($this->app);

			$variable = $variable_parser->replaceVariables(trim($match[1]));
			$expression = trim($match[2]);

			$code = '<?php if(' . $variable . '){ ';
			$code.= 'foreach(' . $variable . ' as ' . $expression . '){';

			return $code;
		}, $content);

		$content = preg_replace('/\{\%\s*endforeach\s*\%\}/U', '<?php }}?>', $content);

		return $content;
	}
}
