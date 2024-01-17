<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class SerializerTest extends Base
{
	protected $data = ['test1', 'test2'];

	protected $expected = 'a:2:{i:0;s:5:"test1";i:1;s:5:"test2";}';

	protected $expected_encoded = 'YToyOntpOjA7czo1OiJ0ZXN0MSI7aToxO3M6NToidGVzdDIiO30=';

	protected $driver = '';

	public function setUp() : void
	{
		parent::setUp();

		$this->driver = $this->app->config->serializer_driver;
	}

	public function tearDown() : void
	{
		$this->app->config->serializer_driver = $this->driver;
	}


	public function testPhp()
	{
		$this->app->config->serializer_driver = 'php';

		$serializer = new Serializer($this->app);
		$this->assertEquals($serializer->serialize($this->data, true), $this->expected_encoded);
		$this->assertEquals($serializer->serialize($this->data, false), $this->expected);
		$this->assertEquals($serializer->serialize($this->data, true, false), $this->expected_encoded);
		$this->assertEquals($serializer->serialize($this->data, false, false), $this->expected);
	}

	public function testIgbinary()
	{
		$this->app->config->serializer_driver = 'igbinary';

		$serializer = new Serializer($this->app);
		$this->assertEquals($serializer->serialize($this->data, true), $this->expected_encoded);
		$this->assertEquals($serializer->serialize($this->data, false), $this->expected);
		$this->assertNotSame($serializer->serialize($this->data, true, false), $this->expected_encoded);
		$this->assertNotSame($serializer->serialize($this->data, false, false), $this->expected);
	}
}
