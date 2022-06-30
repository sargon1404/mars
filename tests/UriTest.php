<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

final class UriTest extends Base
{
	public function setUp() : void
	{
		parent::setUp();

		$this->app->url = 'https://localhost/mars/';
		$this->app->path = '/var/www/mars/';
	}

	public function testIsUrl()
	{
		$uri = $this->app->uri;

		$this->assertTrue($uri->isUrl('http://www.google.com/'));
		$this->assertTrue($uri->isUrl('http://www.google.com/?v=1&q=test'));
		$this->assertTrue($uri->isUrl('https://www.google.com/'));
		$this->assertTrue($uri->isUrl('https://www.google.com/?v=1&q=test'));
		$this->assertFalse($uri->isUrl('://www.google.com/?v=1&q=test'));
		$this->assertFalse($uri->isUrl('www.google.com/?v=1&q=test'));
	}

	public function testIsLocal()
	{
		$uri = $this->app->uri;

		$this->assertTrue($uri->isLocal('https://localhost/mars/'));
		$this->assertTrue($uri->isLocal('https://localhost/mars/page.php'));
		$this->assertTrue($uri->isLocal('https://localhost/mars/page.php?qqq=test'));
		$this->assertFalse($uri->isLocal('https://localhost/ma'));
		$this->assertFalse($uri->isLocal('http://www.google.com/'));
	}

	/*public function testGetFromLocalUrl()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->getFromLocalUrl('https://google/mars/'),  '');
		$this->assertEquals($uri->getFromLocalUrl('https://localhost/mars/page.php'), '/var/www/mars/page.php');
		$this->assertEquals($uri->getFromLocalUrl('https://localhost/mars/dir1/dir2/page.php'), '/var/www/mars/dir1/dir2/page.php');
	}*/

	public function testBuild()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->build('https://www.google.com/', []), 'https://www.google.com/');
		$this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test']), 'https://www.google.com/?v=1&q=test');
		$this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => '']), 'https://www.google.com/?v=1&q=test');
		$this->assertEquals($uri->build('https://www.google.com/', ['v' => 1, 'q' => 'test', 'y' => ''], false), 'https://www.google.com/?v=1&q=test&y=');
		$this->assertEquals($uri->build('https://www.google.com/page.php?x=123', ['v' => 1, 'q' => 'test']), 'https://www.google.com/page.php?x=123&v=1&q=test');
	}

	public function testBuildPath()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->buildPath('https://localhost/mars', ['test1', 'test2']), 'https://localhost/mars/test1/test2/');
		$this->assertEquals($uri->buildPath('https://localhost/mars/', ['test1', 'test2']), 'https://localhost/mars/test1/test2/');
		$this->assertEquals($uri->buildPath('https://localhost/mars/', ['te st1', 'te?st2']), 'https://localhost/mars/te%20st1/te%3Fst2/');
	}

	public function testInQuery()
	{
		$uri = $this->app->uri;

		$this->assertFalse($uri->inQuery('https://localhost/mars/', 'test'));
		$this->assertFalse($uri->inQuery('https://localhost/mars/?v=1&j=test', 'test'));
		$this->assertFalse($uri->inQuery('https://localhost/mars/v=1&j=test', 'test'));
		$this->assertTrue($uri->inQuery('https://localhost/mars/?v=1&test=test', 'test'));
		$this->assertTrue($uri->inQuery('https://localhost/mars/?v=1&test=1', 'test'));
	}

	public function testToHttp()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->toHttp('https://localhost/mars'), 'http://localhost/mars');
		$this->assertEquals($uri->toHttp('http://localhost/mars'), 'http://localhost/mars');
		$this->assertEquals($uri->toHttp('localhost/mars'), 'http://localhost/mars');
	}

	public function testToHtts()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->toHttps('https://localhost/mars'), 'https://localhost/mars');
		$this->assertEquals($uri->toHttps('http://localhost/mars'), 'https://localhost/mars');
		$this->assertEquals($uri->toHttps('localhost/mars'), 'https://localhost/mars');
	}

	public function testAddScheme()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->addScheme('https://localhost/mars'), 'https://localhost/mars');
		$this->assertEquals($uri->addScheme('http://localhost/mars'), 'http://localhost/mars');
		$this->assertEquals($uri->addScheme('https://localhost/mars', 'http'), 'https://localhost/mars');
		$this->assertEquals($uri->addScheme('http://localhost/mars', 'https'), 'http://localhost/mars');
		$this->assertEquals($uri->addScheme('localhost/mars', 'https'), 'https://localhost/mars');
		$this->assertEquals($uri->addScheme('localhost/mars', 'https://'), 'https://localhost/mars');
		$this->assertEquals($uri->addScheme('localhost/mars', 'http'), 'http://localhost/mars');
		$this->assertEquals($uri->addScheme('localhost/mars', 'http://'), 'http://localhost/mars');
	}

	public function testStripScheme()
	{
		$uri = $this->app->uri;

		$this->assertEquals($uri->stripScheme('localhost/mars'), 'localhost/mars');
		$this->assertEquals($uri->stripScheme('http://localhost/mars'), 'localhost/mars');
		$this->assertEquals($uri->stripScheme('https://localhost/mars'), 'localhost/mars');
	}
}
