<?php

use Mars\App;
use Mars\Items;

include_once(__DIR__ . '/Base.php');


/**
 * @ignore
 */
class Cars extends Items
{
	protected static string $table = 'cars';
	protected static string $class = 'Car';
}

/**
 * @ignore
 */
final class ItemsTest extends Base
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
		$db->query("INSERT INTO cars VALUES(3, 'bmw', 'blue')");
	}

	public static function tearDownAfterClass() : void
	{
		$db = App::get()->db;

		$db->query('DROP TABLE IF EXISTS cars');
	}

	public function testSetAndGet()
	{
		$data = [['id' => 2, 'name' => 'Nissan', 'color' => 'green'], ['id' => 3, 'name' => 'Renault', 'color' => 'white']];

		$cars = new Cars;
		$cars->set($data);

		$this->assertSame($cars->getCount(), 2);
		$this->assertSame($cars->getIds(), [2, 3]);

		$car = $cars->get(2);
		$this->assertSame($car->id, 2);
		$this->assertSame($car->name, 'Nissan');
		$this->assertSame($car->color, 'green');
	}

	public function testLoadBySql()
	{
		$cars = new Cars;
		$cars->loadBySql("SELECT * FROM cars");

		$this->assertSame($cars->getCount(), 3);
		$this->assertSame($cars->getIds(), [1, 2, 3]);

		$car = $cars->get(2);
		$this->assertSame($car->id, 2);
		$this->assertSame($car->name, 'ferrari');
		$this->assertSame($car->color, 'red');
	}

	public function testLoadIds()
	{
		$cars = new Cars;
		$cars->loadIds([1,3,4]);

		$this->assertSame($cars->getCount(), 2);
		$this->assertSame($cars->getIds(), [1, 3]);

		$this->assertNull($cars->get(2));
		$this->assertNull($cars->get(4));

		$car = $cars->get(3);
		$this->assertSame($car->id, 3);
		$this->assertSame($car->name, 'bmw');
		$this->assertSame($car->color, 'blue');
	}

	public function testDelete()
	{
		$cars = new Cars;
		$this->assertSame($cars->getTotal(), 3);

		$cars->delete([2,3]);
		$this->assertSame($cars->getTotal(), 1);
	}
}
