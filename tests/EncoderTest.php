<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

final class EncoderTest extends Base
{
	protected $data = ['test', '123'];

	protected $expected = '["test","123"]';

	public function testEncode()
	{
		$encoder = $this->app->encoder;

		$str = $encoder->encode(null);
		$this->assertSame($str, '');

		$str = $encoder->encode($this->data);
		$this->assertSame($str, $this->expected);
	}

	public function testDecode()
	{
		$encoder = $this->app->encoder;

		$data = $encoder->decode('');
		$this->assertSame($data, '');

		$data = $encoder->decode($this->expected);
		$this->assertSame($data, $this->data);
	}
}