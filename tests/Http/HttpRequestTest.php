<?php

use Mars\Http\Request;

include_once(dirname(__DIR__) . '/Base.php');

/**
* @ignore
*/
final class HttpRequestTest extends Base
{
	protected $url = '';

	public function setUp() : void
	{
		parent::setUp();

		$this->url = $this->app->url . 'src/mars/tests/data/request-test.php';
	}

	public function testError()
	{
		$req = new Request($this->app->url . 'src/mars/tests/data/request-test123.php');
		$response = $req->get();

		$this->assertSame($response->code, 404);
		$this->assertFalse($response->ok());
	}

	public function testGet()
	{
		$req = new Request($this->url);
		$response = $req->get();

		$this->assertSame($response->body, 'test12345');
		$this->assertSame($response->code, 200);
		$this->assertTrue($response->ok());
	}

	public function testPost()
	{
		$req = new Request($this->url);
		$response = $req->post(['foo' => 'bar', 'faz' => 'baz']);

		$this->assertSame($response->body, '{"foo":"bar","faz":"baz"}');
		$this->assertSame($response->code, 200);
		$this->assertTrue($response->ok());
		$this->assertSame($response->getJson(), ['foo' => 'bar', 'faz' => 'baz']);
	}

	public function testGetFile()
	{
		$url = $this->app->url . 'src/mars/tests/data/sample.txt';
		$filename = $this->app->path . 'src/mars/tests/data/http-data/sample.txt';

		$req = new Request($url);
		$response = $req->getFile($filename);

		$this->assertSame($response->code, 200);
		$this->assertTrue($response->ok());
		$this->assertTrue(is_file($filename));
		$this->assertSame(file_get_contents($filename), 'test123456');

		unlink($filename);
	}
}
