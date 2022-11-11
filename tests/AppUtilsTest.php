<?php

use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class AppUtilsTest extends Base
{
	protected array $data = ['foo' => 'bar', 'abc' => 'baz', 'def' => 'bay'];

	public function testfixPath()
	{
		$this->assertSame(App::fixPath('/var/www/html'), '/var/www/html/');
		$this->assertSame(App::fixPath('/var/www/html/'), '/var/www/html/');
	}

	public function testGetClass()
	{
		$this->assertSame(App::getClass('my class'), 'MyClass');
		$this->assertSame(App::getClass('my-class'), 'MyClass');
	}

	public function testGetMethod()
	{
		$this->assertSame(App::getMethod('my method'), 'myMethod');
		$this->assertSame(App::getMethod('my-method'), 'myMethod');
		$this->assertSame(App::getMethod('my_method'), 'myMethod');
		$this->assertSame(App::getMethod('myMethod'), 'myMethod');
	}

	public function testHasProperty()
	{
		$this->assertTrue(App::hasProperty($this->data, 'abc'));
		$this->assertFalse(App::hasProperty($this->data, 'abcde'));

		$obj = (object)$this->data;
		$this->assertTrue(App::hasProperty($obj, 'abc'));
		$this->assertFalse(App::hasProperty($obj, 'abcde'));
	}

	public function testGetProperty()
	{
		$this->assertSame(App::getProperty($this->data, 'abc'), 'baz');
		$this->assertNull(App::getProperty($this->data, 'abcde'));

		$obj = (object)$this->data;
		$this->assertSame(App::getProperty($obj, 'abc'), 'baz');
		$this->assertNull(App::getProperty($obj, 'abcde'));
	}

	public function testGetProperties()
	{
		$this->assertSame(App::getProperties($this->data), $this->data);
		$this->assertSame(App::getProperties((object)$this->data), $this->data);

		$this->assertSame(App::getProperties($this->data, ['foo', 'abc']), ['foo' => 'bar', 'abc' => 'baz']);
		$this->assertSame(App::getProperties((object)$this->data, ['foo', 'abc']), ['foo' => 'bar', 'abc' => 'baz']);
	}

	public function testFilterProperties()
	{
		$this->assertSame(App::filterProperties($this->data, ['def']), ['foo' => 'bar', 'abc' => 'baz']);
		$this->assertSame(App::filterProperties($this->data, ['foo', 'abc']), ['def' => 'bay']);

		$this->assertSame(App::filterProperties((object)$this->data, ['def']), ['foo' => 'bar', 'abc' => 'baz']);
		$this->assertSame(App::filterProperties((object)$this->data, ['foo', 'abc']), ['def' => 'bay']);
	}

	public function testArray()
	{
		$this->assertIsArray(App::array(null));
		$this->assertIsArray(App::array(12));
		$this->assertIsArray(App::array('my string'));
		$this->assertIsArray(App::array(['my string']));
	}

	public function testUnset()
	{
		$this->assertSame(App::unset(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'b']), ['c' => 3]);
		$this->assertSame(App::unset(['a' => 1, 'b' => 2, 'c' => 3], 'a'), ['b' => 2, 'c' => 3]);
	}

	public function testPadInt()
	{
		$this->assertSame(App::padInt(12), '12');
		$this->assertSame(App::padInt(2), '02');
	}
}
