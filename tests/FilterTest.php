<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

final class FilterTest extends Base
{
	public function testString()
	{
		$filter = $this->app->filter;

		$this->assertSame($filter->string(12), '12');
		$this->assertSame($filter->string('test'), 'test');
		$this->assertSame($filter->string([12, 'test']), ['12', 'test']);
	}

	public function testInt()
	{
		$filter = $this->app->filter;

		$this->assertSame($filter->int(12), 12);
		$this->assertSame($filter->int(12.78), 12);
		$this->assertSame($filter->int('12.78'), 12);
		$this->assertSame($filter->int([12, 5.67]), [12, 5]);
	}

	public function testFloat()
	{
		$filter = $this->app->filter;

		$this->assertSame($filter->float(12), 12.0);
		$this->assertSame($filter->float(12.67), 12.67);
		$this->assertSame($filter->float('12.78'), 12.78);
		$this->assertSame($filter->float([12, 5.67]), [12.0, 5.67]);
	}

	public function testAbs()
	{
		$filter = $this->app->filter;

		$this->assertSame($filter->abs(12), 12);
		$this->assertSame($filter->abs(-12), 12);
		$this->assertSame($filter->abs(12.67), 12.67);
		$this->assertSame($filter->abs(-12.67), 12.67);
	}

	public function testFilename()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->filename('some filename.jpg'), 'some-filename.jpg');
		$this->assertEquals($filter->filename('../../some filename.jpg'), 'some-filename.jpg');
		$this->assertEquals($filter->filename('/dir/sub dir/some filename.jpg'), 'some-filename.jpg');
	}

	public function testFilepath()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->filepath('some filename.jpg'), 'some-filename.jpg');
		$this->assertEquals($filter->filepath('/dir/sub dir/some filename.jpg'), '/dir/sub dir/some-filename.jpg');
	}

	public function testEmail()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->email('myaddress-123@something.com'), 'myaddress-123@something.com');
		$this->assertEquals($filter->email('myaddress 123@something.com'), 'myaddress123@something.com');
	}

	public function testAlpha()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->alpha('some text# 12345 xyz'), 'sometextxyz');
		$this->assertEquals($filter->alpha('some text# 12345 xyz', true), 'some text  xyz');
	}

	public function testAlnum()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->alnum('some text# 12345 xyz'), 'sometext12345xyz');
		$this->assertEquals($filter->alnum('some text# 12345 xyz', true), 'some text 12345 xyz');
	}

	public function testSlug()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->slug('some url 12'), 'some-url-12');
		$this->assertEquals($filter->slug('some_url--12'), 'some-url-12');
		$this->assertEquals($filter->slug('some_url()12'), 'some-url12');
		$this->assertEquals($filter->slug('some_url/12'), 'some-url12');
		$this->assertEquals($filter->slug('some_url/12', true), 'some-url/12');
	}

	public function testHtml()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->html('<a href="http://www.sss.com" data-attr="12345">xxxx</a>'), '<a href="http://www.sss.com">xxxx</a>');
		$this->assertEquals($filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', 'a'), '<a href="http://www.sss.com">xxxx</a>');
		$this->assertEquals($filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', 'a,span,img'), '<a href="http://www.sss.com"><span>xxxx</span></a>');
		$this->assertEquals($filter->html('<a href="http://www.sss.com" data-attr="12345"><span>xxxx</span></a>', null, 'a.href,img.src'), '<a href="http://www.sss.com"><span>xxxx</span></a>');
	}

	public function testInterval()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->interval(12, 10, 20, 2), 12);
		$this->assertEquals($filter->interval(5, 10, 20, 2), 2);
		$this->assertEquals($filter->interval(25, 10, 20, 2), 2);
	}

	public function testRemove()
	{
		$filter = $this->app->filter;

		$this->assertEquals($filter->remove(['a', 'b', 'c', '12'], 12), ['a', 'b', 'c']);
		$this->assertEquals($filter->remove(['a', 'b', 'c', '12'], '12'), ['a', 'b', 'c']);
		$this->assertEquals($filter->remove(['a', 'b', 'c', '12'], [12, 'b', 'c']), ['a']);
		$this->assertEquals($filter->remove(['a', 'b', 'c', '12'], ['a', '12', 'b', 'c']), []);
	}

	public function testAllowed()
	{
		$filter = $this->app->filter;

		$this->assertEqualsCanonicalizing($filter->allowed(['a', 'b', 'd', 12, 13], ['a', 'b', 'c', '12']), ['a', 'b', 12]);
		$this->assertEqualsCanonicalizing($filter->allowed(['a', 'b', 'd', 12, 13], 'd'), ['d']);
		$this->assertEqualsCanonicalizing($filter->allowed(['a', 'b', 'd', 12, 13], 'z'), []);

		$this->assertEquals($filter->allowed('b', ['a', 'b', 'c']), 'b');
		$this->assertEquals($filter->allowed('b', 'b'), 'b');
		$this->assertEquals($filter->allowed('b', ['a'], 'c'), 'c');
	}
}
