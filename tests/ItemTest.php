<?php

use Mars\App;
use Mars\Item;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
class Car extends Item
{
	protected static string $table = 'cars';

	protected static array $default_override = ['color' => 'mycolor'];

	protected static array $default_ignore = ['name'];
}

/**
 * @ignore
 */
final class ItemTest extends Base
{
	public static function setUpBeforeClass() : void
	{
		$db = App::get()->db;
		$db->query('DROP TABLE IF EXISTS cars');
		$db->query('
			CREATE TABLE cars (
				id 	INT AUTO_INCREMENT,
				name	varchar(255),
				color varchar(255),

				PRIMARY KEY(id)
			)');
		$db->query("INSERT INTO cars VALUES(1, 'mercedes', 'black')");
		$db->query("INSERT INTO cars VALUES(2, 'ferrari', 'red')");
		$db->query("INSERT INTO cars VALUES(3, '2', 'blue')");
	}

	public static function tearDownAfterClass() : void
	{
		$db = App::get()->db;

		$db->query('DROP TABLE IF EXISTS cars');
	}

	public function testSetId()
	{
		$car = new Car;

		$this->assertSame($car->getId(), 0);

		$car->setId(10);
		$this->assertSame($car->getId(), 10);
	}

	public function testIs()
	{
		$car = new Car;
		$this->assertFalse($car->is());

		$car->setId(10);
		$this->assertTrue($car->is());
	}

	public function testGetRow()
	{
		$car = new Car;
		$row = $car->getRow(1);

		$this->assertSame($row->id, 1);
		$this->assertSame($row->name, 'mercedes');
	}

	public function testLoad()
	{
		$car = new Car;
		$car->load(2);
		$this->assertSame($car->id, 2);
		$this->assertSame($car->name, 'ferrari');

		$car = new Car;
		$car->load('2');
		$this->assertSame($car->id, 2);
		$this->assertSame($car->name, 'ferrari');

		$car = new Car;
		$car->load('mercedes');
		$this->assertSame($car->id, 1);
		$this->assertSame($car->name, 'mercedes');

		$car = new Car;
		$car->load(['id' => 4, 'name' => 'renault', 'color' => 'green']);
		$this->assertSame($car->id, 4);
		$this->assertSame($car->name, 'renault');
		$this->assertSame($car->color, 'green');

		$car = new Car(null);
		$this->assertSame($car->id, 0);
		$this->assertFalse(isset($car->name));
		$this->assertSame($car->color, 'mycolor');
	}

	public function testLoadByName()
	{
		$car = new Car;
		$car->loadByName(2);

		$this->assertSame($car->id, 3);
		$this->assertSame($car->name, '2');
	}

	public function testLoadBySql()
	{
		$car = new Car;
		$car->loadBySql("SELECT * FROm cars WHERE id = 100");
		$this->assertSame($car->id, 0);

		$car = new Car;
		$car->loadBySql("SELECT * FROm cars WHERE id = 2");
		$this->assertSame($car->id, 2);
		$this->assertSame($car->name, 'ferrari');
	}

	public function testInsert()
	{
		$car = new Car;
		$car->name = 'BMW';
		$car->color = 'black';
		$id = $car->insert();

		$this->assertGreaterThan(0, $id);

		$car = new Car($id);
		$this->assertSame($car->name, 'BMW');
	}

	public function testUpdate()
	{
		$car = new Car(3);
		$car->color = 'yellow';
		$car->update();

		$car = new Car(3);
		$this->assertSame($car->color, 'yellow');
	}

	public function testDelete()
	{
		$car = new Car;
		$car->name = 'ToDelete';
		$car->color = 'black';
		$id = $car->insert();

		$this->assertGreaterThan(0, $id);

		$car = new Car($id);
		$car->delete();

		$car = new Car($id);
		$this->assertSame($car->getId(), 0);
	}

	public function testBind()
	{
		$car = new Car(2);
		$car->bind(['id' => 40, 'color' => 'brown', 'some_prop' => 'abc']);

		$this->assertSame($car->id, 2);
		$this->assertSame($car->color, 'brown');
		$this->assertFalse(isset($car->some_prop));
	}

	public function testBindList()
	{
		$car = new Car(2);
		$car->bindList(['color' => 'brown', 'some_prop' => 'abc'], ['color']);

		$this->assertSame($car->id, 2);
		$this->assertSame($car->color, 'brown');
		$this->assertFalse(isset($car->some_prop));
	}

	public function testIsOriginal()
	{
		$car = new Car(2);

		$this->assertTrue($car->isOriginal('color'));
		$this->assertFalse($car->isOriginal('foo'));
	}

	public function testGetOriginal()
	{
		$car = new Car(2);
		$car->color = 'white';

		$this->assertSame($car->getOriginal('color'), 'red');
	}

	public function testCanUpdate()
	{
		$car = new Car(2);
		$this->assertFalse($car->canUpdate('color'));

		$car->color = 'white';

		$this->assertTrue($car->canUpdate('color'));
	}

	public function testFlip()
	{
		$car = new Car(2);
		$car->color = 'white';

		$car->flip('color');
		$this->assertSame($car->color, 'red');
		$this->assertSame($car->getOriginal('color'), 'white');

		$car->flip('color');
		$this->assertSame($car->color, 'white');
		$this->assertSame($car->getOriginal('color'), 'red');
	}
}
