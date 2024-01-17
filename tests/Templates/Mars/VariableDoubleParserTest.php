<?php

use Mars\Templates\Mars\VariableDoubleParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class VariableDoubleParserTest extends Base
{
	public function testParse()
	{
		$parser = new VariableDoubleParser($this->app);

		$this->assertSame($parser->parse('{{{ $myvar }}}'), '<?php echo $this->app->escape->htmlx2($vars[\'myvar\']);?>');
		$this->assertSame($parser->parse('{{{ $myvar#prop }}}'), '<?php echo $this->app->escape->htmlx2($vars[\'myvar\'][\'prop\']);?>');
		$this->assertSame($parser->parse('{{{ $myvar | trim}}}'), '<?php echo $this->app->escape->htmlx2(trim($vars[\'myvar\']));?>');
	}
}
