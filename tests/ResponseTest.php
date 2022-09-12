<?php

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class ResponseTest extends Base
{
	public function testHeaders()
	{
		$response = $this->app->response;

		$this->assertSame($response->headers->get(), []);
		$this->assertNull($response->headers->get('X-Test-Header'));

		$response->headers->add('X-Test-Header', 'test123');
		$response->headers->add('X-Test-Header2', 'test345');
		$this->assertSame($response->headers->get('X-Test-Header'), 'X-Test-Header: test123');
		$this->assertSame($response->headers->get(), ['X-Test-Header' => 'X-Test-Header: test123', 'X-Test-Header2' => 'X-Test-Header2: test345']);

		$response->headers->remove('X-Test-Header');
		$this->assertNull($response->headers->get('X-Test-Header'));
	}
}
