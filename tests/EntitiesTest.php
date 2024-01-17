<?php

use Mars\Entities;
use Mars\Entity;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class EntitiesTest extends Base
{
	protected array $data = [];

	protected object $obj1;

	protected object $obj2;

	protected object $obj3;

	public function setUp() : void
	{
		parent::setUp();

		$this->obj1 = new Entity(['foo' => 'bar', 'abc' => 'baz']);
		$this->obj2 = new Entity(['var1' => 'myvalue1', 'var2' => 'myvalue2']);
		$this->obj3 = new Entity(['qwe' => 'rty']);

		$this->data[] = $this->obj1;
		$this->data[] = $this->obj2;
	}

	public function testSet()
	{
		$ent = new Entities($this->data);
		$this->assertSame($ent->getCount(), 2);
		$this->assertSame($ent->get(), $this->data);
	}

	public function testHas()
	{
		$ent = new Entities;
		$this->assertFalse($ent->has());

		$ent = new Entities($this->data);
		$this->assertTrue($ent->has());
	}

	public function testAdd()
	{
		$ent = new Entities($this->data);
		$ent->add($this->obj3);

		$this->assertSame($ent->getCount(), 3);
		$this->assertSame($ent->get(), array_merge($this->data, [$this->obj3]));
	}

	public function testUpdate()
	{
		$ent = new Entities($this->data);
		$this->assertFalse($ent->update(55, $this->obj3));
		$this->assertTrue($ent->update(0, $this->obj3));
		$this->assertSame($ent->getCount(), 2);
	}

	public function testGet()
	{
		$ent = new Entities($this->data);
		$this->assertSame($ent->get(), $this->data);
		$this->assertSame($ent->get(0), $this->obj1);
		$this->assertSame($ent->get(100), null);
	}

	public function testGetObject()
	{
		$data = ['foo' => 'bar', 'abc' => 'baz'];
		$obj = new Entity($data);

		$ent = new Entities;
		$this->assertEquals($ent->getObject($data), $obj);
	}
}
