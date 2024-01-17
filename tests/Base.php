<?php

use PHPUnit\Framework\TestCase;

use Mars\App;

/**
 * @ignore
 */
abstract class Base extends TestCase
{
	protected $app;

	public function setUp() : void
	{
		$this->app = App::get();
	}

	protected function assertArrayHasKeyAndValue($key, $val, $arr)
	{
		$this->assertArrayHasKey($key, $arr);
		if (isset($arr[$key])) {
			$this->assertEquals($arr[$key], $val);
		}
	}

	protected function assertObjectHasPropertyAndValue($attr, $val, $obj)
	{
		$this->assertObjectHasProperty($attr, $obj);
		if (isset($obj->$attr)) {
			$this->assertEquals($obj->$attr, $val);
		}
	}
}
