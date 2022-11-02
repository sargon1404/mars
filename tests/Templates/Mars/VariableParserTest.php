<?php

use Mars\Templates\Mars\VariableParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
* @ignore
*/
final class VariableParserTest extends Base
{
	public function testParse()
	{
		$parser = new VariableParser($this->app);
		$this->assertSame($parser->parse('{{ my_str123 }}'), '<?php echo $this->app->escape->html($strings[\'my_str123\']);?>');
		$this->assertSame($parser->parse('{{ $myvar }}'), '<?php echo $this->app->escape->html($vars[\'myvar\']);?>');
		$this->assertSame($parser->parse('{{ $myobj.prop }}'), '<?php echo $this->app->escape->html($vars[\'myobj\']->prop);?>');
		$this->assertSame($parser->parse('{{ $myobj->prop }}'), '<?php echo $this->app->escape->html($vars[\'myobj\']->prop);?>');
		$this->assertSame($parser->parse('{{ $myarr#prop }}'), '<?php echo $this->app->escape->html($vars[\'myarr\'][\'prop\']);?>');
		$this->assertSame($parser->parse('{{ $myarr[\'prop\'] }}'), '<?php echo $this->app->escape->html($vars[\'myarr\'][\'prop\']);?>');

		$this->assertSame($parser->parse('{{ my_str123|raw }}'), '<?php echo $strings[\'my_str123\'];?>');
		$this->assertSame($parser->parse('{{ $myvar | raw }}'), '<?php echo $vars[\'myvar\'];?>');
		$this->assertSame($parser->parse('{{ $myvar | lower| trim }}'), '<?php echo $this->app->escape->html(strtolower(trim($vars[\'myvar\'])));?>');
		$this->assertSame($parser->parse('{{ $myvar | raw | lower | trim }}'), '<?php echo strtolower(trim($vars[\'myvar\']));?>');

		$this->assertSame($parser->parse('{{ trim($myvar) }}'), '<?php echo $this->app->escape->html(trim($vars[\'myvar\']));?>');
		$this->assertSame($parser->parse('{{ trim($myvar) | raw }}'), '<?php echo trim($vars[\'myvar\']);?>');
		$this->assertSame($parser->parse('{{ myfunc($myvar) | trim }}'), '<?php echo $this->app->escape->html(trim(myfunc($vars[\'myvar\'])));?>');
		$this->assertSame($parser->parse('{{ myfunc($myvar) | raw | trim }}'), '<?php echo trim(myfunc($vars[\'myvar\']));?>');
	}
}
