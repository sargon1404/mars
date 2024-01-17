<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class JsonTest extends Base
{
	protected $data = ['test', '123'];

	protected $expected = '["test","123"]';

	public function testEncode()
	{
		$json = $this->app->json;

		$str = $json->encode(null);
		$this->assertSame($str, '');

		$str = $json->encode($this->data);
		$this->assertSame($str, $this->expected);
	}

	public function testDecode()
	{
		$json = $this->app->json;

		$data = $json->decode('');
		$this->assertSame($data, '');

		$data = $json->decode($this->expected);
		$this->assertSame($data, $this->data);
	}
}
