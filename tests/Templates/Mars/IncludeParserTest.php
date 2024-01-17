<?php

use Mars\Templates\Mars\IncludeParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class IncludeParserTest extends Base
{
	public function testParse()
	{
		$parser = new IncludeParser;

		$this->assertSame($parser->parse('{%include%}'), '');
		$this->assertSame($parser->parse('{% include my_template %}', ['template' => 'my_template']), '<?php $this->render(\'my_template\');?>');
	}
}
