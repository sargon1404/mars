<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

final class UnescapeTest extends Base
{
	public function testHtml()
	{
		$unescape = $this->app->unescape;

		$this->assertSame($unescape->html('&lt;b&gt;test&apos; test &quot; test&lt;b&gt;'), '<b>test\' test " test<b>');
	}
}
