<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class EscapeTest extends Base
{
	public function testHtml()
	{
		$escape = $this->app->escape;

		$this->assertSame($escape->html('<b>test\' test " test<b>'), '&lt;b&gt;test&apos; test &quot; test&lt;b&gt;');
	}

	public function testHtmlx2()
	{
		$escape = $this->app->escape;

		$this->assertSame($escape->htmlx2("<b>test' test \" test<b>"), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot; test&amp;lt;b&amp;gt;");
		$this->assertSame($escape->htmlx2("<b>test' test \"\n test<b>"), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot;<br />\n test&amp;lt;b&amp;gt;");
		$this->assertSame($escape->htmlx2("<b>test' test \"\n test<b>", false), "&amp;lt;b&amp;gt;test&amp;apos; test &amp;quot;\n test&amp;lt;b&amp;gt;");
	}

	public function testJs()
	{
		$escape = $this->app->escape;

		$this->assertSame($escape->js("alert('test')"), 'alert(&apos;test&apos;)');
	}

	public function testJsString()
	{
		$escape = $this->app->escape;

		$this->assertSame($escape->jsString("joe's"), "joe\'s");
	}

	public function testPath()
	{
		$escape = $this->app->escape;

		$filename = $this->app->path . 'dir/somefile.txt';

		$this->assertSame($escape->path($filename), 'dir/somefile.txt');
	}
}
