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
		$this->assertEquals($filter->filename('dir/sub dir/some filename.jpg'), 'some-filename.jpg');
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

}