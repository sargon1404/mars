<?php

use Mars\Entity;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class EntityTest extends Base
{
	protected array $data = ['foo' => 'bar', 'abc' => 'baz'];

	public function testSet()
	{
		$ent = new Entity($this->data);
		$this->assertSame($ent->foo, 'bar');
		$this->assertSame($ent->abc, 'baz');

		$data = new stdClass;
		$data->foo = 'bar';
		$data->abc = 'baz';

		$ent = new Entity($data);
		$this->assertSame($ent->foo, 'bar');
		$this->assertSame($ent->abc, 'baz');
	}

	public function testAdd()
	{
		$ent = new Entity($this->data);

		$new_data = ['foo' => 'xyz', 'myvar' => 'myvalue'];
		$ent->add($new_data);

		$this->assertSame($ent->foo, 'bar');
		$this->assertSame($ent->myvar, 'myvalue');
	}

	public function testHas()
	{
		$ent = new Entity($this->data);
		$this->assertTrue($ent->has('foo'));
		$this->assertFalse($ent->has('qqqq'));
	}

	public function testGet()
	{
		$ent = new Entity($this->data);
		$this->assertSame($ent->get(), $this->data);
		$this->assertSame($ent->get(['foo', 'abc']), $this->data);
		$this->assertSame($ent->get(['abc']), ['abc' => 'baz']);
	}
}
