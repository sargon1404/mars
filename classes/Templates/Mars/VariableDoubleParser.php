<?php
/**
* The Double Escape Variable Hander
* @package Mars
*/

namespace Mars\Templates\Mars;

use Mars\App;

/**
 * The Double Escape Variable Hander
 */
class VariableDoubleParser extends VariableParser
{
	use \Mars\AppTrait;

	/**
	 * @see \Mars\Templates\DriverInterface::parse()
	 * {@inheritdoc}
	 */
	public function parse(string $content, array $params = []) : string
	{
		return preg_replace_callback('/\{\{\{(.*)\}\}\}/U', function (array $match) {
			return $this->parseVariable($match);
		}, $content);
	}

	/**
	 * {inheritdoc}
	 */
	protected function applyModifiers(string $value, array $modifiers, bool $apply_escape = true) : string
	{
		$modifiers[] = 'htmlx2';

		return parent::applyModifiers($value, $modifiers, false);
	}
}
