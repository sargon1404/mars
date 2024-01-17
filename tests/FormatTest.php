<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class FormatTest extends Base
{
	public function testLower()
	{
		$format = $this->app->format;

		$this->assertEquals($format->lower('TesT 123'), 'test 123');
		$this->assertEquals($format->lower(['TesT 123', 'ABCD']), ['test 123', 'abcd']);
	}

	public function testUpper()
	{
		$format = $this->app->format;

		$this->assertEquals($format->upper('TesT 123'), 'TEST 123');
		$this->assertEquals($format->upper(['TesT 123', 'AbcD']), ['TEST 123', 'ABCD']);
	}

	public function testRound()
	{
		$format = $this->app->format;

		$this->assertEquals($format->round('123.7634'), 123.76);
		$this->assertEquals($format->round(['123.7634', '999.45654']), [123.76, '999.46']);
	}

	public function testNumber()
	{
		$format = $this->app->format;

		$this->assertEquals($format->number('45123.7634'), '45,123.76');
		$this->assertEquals($format->number([45123.7634, '999.45654']), ['45,123.76', '999.46']);
	}

	public function testPercentage()
	{
		$format = $this->app->format;

		$this->assertEquals($format->percentage(12, 24), 50);
		$this->assertEquals($format->percentage([30, 80], 200), [15, 40]);
	}

	public function testFilesize()
	{
		$format = $this->app->format;

		$this->assertEquals($format->filesize('76656'), '74.86 KB');
		$this->assertEquals($format->filesize([91761_234_506, 76656]), ['85.46 GB', '74.86 KB']);
	}

	public function testTimeInterval()
	{
		$format = $this->app->format;

		$this->assertEquals($format->timeInterval(90), '1 minute, 30 seconds');
		$this->assertEquals($format->timeInterval(181), '3 minutes, 1 second');
		$this->assertEquals($format->timeInterval(56790), '15 hours, 46 minutes, 30 seconds');
	}

	public function testJsArray()
	{
		$format = $this->app->format;

		$this->assertSame($format->jsArray(['foo' => "b'ar", 'faz' => 'baz', 'zzz' => 123]), "['b\'ar','baz','123']");
		$this->assertSame($format->jsArray(['foo' => "b'ar", 'faz' => 'baz', 'zzz' => 123], false), "[b\\'ar,baz,123]");
		$this->assertSame($format->jsArray(['foo' => "b'ar", 'faz' => 'baz', 'zzz' => 123], true, ['zzz']), "['b\'ar','baz',123]");
	}

	public function testJsObject()
	{
		$format = $this->app->format;

		$this->assertSame($format->jsObject(['foo' => "bar", 'faz' => 'baz', 'zzz' => 123, 'arr' => ['p1' => 'v1', 'p2' => 'v2']]), "{foo: 'bar', faz: 'baz', zzz: '123', arr: ['v1','v2']}");
		$this->assertSame($format->jsObject(['foo' => "bar", 'faz' => 'baz', 'zzz' => 123, 'arr' => ['p1' => 'v1', 'p2' => 'v2']], false), "{foo: bar, faz: baz, zzz: 123, arr: [v1,v2]}");
		$this->assertSame($format->jsObject(['foo' => "bar", 'faz' => 'baz', 'zzz' => 123, 'arr' => ['p1' => 'v1', 'p2' => 'v2']], true, ['zzz', 'p2']), "{foo: 'bar', faz: 'baz', zzz: 123, arr: ['v1',v2]}");
	}
}
