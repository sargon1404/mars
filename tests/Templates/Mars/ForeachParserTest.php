<?php

use Mars\Templates\Mars\ForeachParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
* @ignore
*/
final class ForeachParserTest extends Base
{
	public function testParse()
	{
		$parser = new ForeachParser($this->app);

		$this->assertSame($parser->parse('{% foreach $myvar[\'qqqq\'] as $zzzz %}'), '<?php if($vars[\'myvar\'][\'qqqq\']){ foreach($vars[\'myvar\'][\'qqqq\'] as $zzzz){');
		$this->assertSame($parser->parse('{% foreach $myvar[\'qqqq\'] as $key => $zzzz %}'), '<?php if($vars[\'myvar\'][\'qqqq\']){ foreach($vars[\'myvar\'][\'qqqq\'] as $key => $zzzz){');

		$this->assertSame($parser->parse('{% endforeach %}'), '<?php }}?>');
	}
}
