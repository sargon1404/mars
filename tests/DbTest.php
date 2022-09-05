<?php

use Mars\App;
use Mars\Db;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class DbTest extends Base
{
	protected $expected = [
		1 => ['id' => 1, 'col1' => 'test1', 'col2' => 10],
		2 => ['id' => 2, 'col1' => 'test2', 'col2' => 20],
		3 => ['id' => 3, 'col1' => 'test3', 'col2' => 30],
		4 => ['id' => 4, 'col1' => 'test4', 'col2' => 40],
	];
	protected $data = ['col1' => 'test', 'col2' => 100];

	public static function setUpBeforeClass() : void
	{
		$db = new Db(App::get());
		$db->query('
			CREATE TABLE select_test (
				id 	INT AUTO_INCREMENT,
				col1	varchar(255),
				col2  INT,

				PRIMARY KEY(id)
			)');
		$db->query("INSERT INTO select_test VALUES(1, 'test1', 10)");
		$db->query("INSERT INTO select_test VALUES(2, 'test2', 20)");
		$db->query("INSERT INTO select_test VALUES(3, 'test3', 30)");
		$db->query("INSERT INTO select_test VALUES(4, 'test4', 40)");

		$db->query('
			CREATE TABLE insert_test (
				id 	INT AUTO_INCREMENT,
				col1	varchar(255),
				col2  INT,

				PRIMARY KEY(id)
			)');
	}

	public static function tearDownAfterClass() : void
	{
		$db = new Db(App::get());

		$db->query("DROP TABLE select_test");
		$db->query("DROP TABLE insert_test");
	}

	public function setUp() : void
	{
		parent::setUp();

		$this->app->config->db_database = 'mars';
	}

	protected function assertRowAsObject($row, $key)
	{
		$this->assertObjectHasAttributeAndValue('id', $this->expected[$key]['id'], $row);
		$this->assertObjectHasAttributeAndValue('col1', $this->expected[$key]['col1'], $row);
		$this->assertObjectHasAttributeAndValue('col2', $this->expected[$key]['col2'], $row);
	}

	protected function assertRowAsArray($row, $key)
	{
		$this->assertArrayHasKeyAndValue('id', $this->expected[$key]['id'], $row);
		$this->assertArrayHasKeyAndValue('col1', $this->expected[$key]['col1'], $row);
		$this->assertArrayHasKeyAndValue('col2', $this->expected[$key]['col2'], $row);
	}

	protected function assertRowAsInsertedObject($row, $id)
	{
		$data = $this->data;
		$data['id'] = $id;

		$this->assertObjectHasAttributeAndValue('id', $data['id'], $row);
		$this->assertObjectHasAttributeAndValue('col1', $data['col1'], $row);
		$this->assertObjectHasAttributeAndValue('col2', $data['col2'], $row);
	}

	public function testConnection()
	{
		$db = new Db($this->app);

		$this->assertEquals($db->count('select_test'), 4);
	}

	public function testInvalidQuery()
	{
		$this->expectException(\Exception::class);

		$db = new Db($this->app);

		$db->query("SELECT col10 FROm select_test");
	}

	public function testQueryResults()
	{
		$db = new Db($this->app);

		//fetch()
		$row = $db->query('SELECT * FROM select_test WHERE id = 1')->fetch();
		$this->assertRowAsObject($row, 1);

		//fetchObject()
		$row = $db->query('SELECT * FROM select_test WHERE id = 1')->fetchObject();
		$this->assertRowAsObject($row, 1);

		//fetchArray()
		$row = $db->query('SELECT * FROM select_test WHERE id = 2')->fetchArray();
		$this->assertRowAsArray($row, 2);

		//fetchRow()
		$row = $db->query('SELECT * FROM select_test WHERE id = 2')->fetchRow();
		$this->assertArrayHasKeyAndValue(0, 2, $row);
		$this->assertArrayHasKeyAndValue(1, 'test2', $row);
		$this->assertArrayHasKeyAndValue(2, '20', $row);

		//fetchColumn()
		$col1 = $db->query('SELECT * FROM select_test')->fetchColumn();
		$col2 = $db->query('SELECT * FROM select_test')->fetchColumn(1);
		$this->assertEquals($col1, 1);
		$this->assertEquals($col2, 'test1');

		//fetchAll()
		$rows = $db->query('SELECT * FROM select_test')->fetchAll();
		$this->assertCount(4, $rows);
		$this->assertRowAsObject($rows[0], 1);

		$rows = $db->query('SELECT * FROM select_test')->fetchAll(true);
		$this->assertCount(4, $rows);
		$this->assertRowAsArray($rows[0], 1);

		//fetchAllFromColumn()
		$cols = $db->query('SELECT * FROM select_test')->fetchAllFromColumn();
		$this->assertEquals($cols, [1,2,3,4]);

		$cols = $db->query('SELECT * FROM select_test')->fetchAllFromColumn(1);
		$this->assertEquals($cols, ['test1', 'test2', 'test3', 'test4']);

		//numRows
		$rows_count = $db->query('SELECT * FROM select_test')->numRows();
		$this->assertSame($rows_count, 4);

		//get() as  objects
		$rows = $db->query('SELECT * FROM select_test')->get();
		$this->assertCount(4, $rows);
		$this->assertRowAsObject($rows[0], 1);

		//get() as array
		$rows = $db->query('SELECT * FROM select_test')->get(as_array: true);
		$this->assertCount(4, $rows);
		$this->assertRowAsArray($rows[0], 1);

		//get() with a key field
		$rows = $db->query('SELECT * FROM select_test')->get('col1');
		$this->assertCount(4, $rows);
		$this->assertArrayHasKey('test1', $rows);
		$this->assertArrayHasKey('test2', $rows);
		$this->assertArrayHasKey('test3', $rows);
		$this->assertArrayHasKey('test4', $rows);
		$this->assertRowAsObject($rows['test1'], 1);

		//get() with a field and a no key field
		$rows = $db->query('SELECT * FROM select_test')->get('', 'col2');
		$this->assertCount(4, $rows);
		$this->assertEquals($rows, [10, 20, '30', 40]);

		//get() with a field and a key field
		$rows = $db->query('SELECT * FROM select_test')->get('col1', 'col2');
		$this->assertCount(4, $rows);
		$this->assertEquals($rows, ['test1' => 10, 'test2' => 20, 'test3' => 30, 'test4' => 40]);

		//getResult()
		$result = $db->query('SELECT * FROM select_test WHERE id = 2')->getResult();
		$this->assertEquals($result, 2);

		//getCount()
		$count = $db->query('SELECT COUNT(*) FROM select_test')->getCount();
		$this->assertEquals($count, 4);
	}

	public function testSelect()
	{
		$db = new Db($this->app);

		//select()
		$rows = $db->select('select_test', ['id' => 30]);
		$this->assertCount(0, $rows);

		//select()
		$rows = $db->select('select_test', ['id' => 3]);
		$this->assertCount(1, $rows);
		$this->assertRowAsObject($rows[0], 3);

		//select()
		$rows = $db->select('select_test', [], 'id', 'desc');
		$this->assertCount(4, $rows);
		$this->assertRowAsObject($rows[0], 4);

		//select()
		$rows = $db->select('select_test', [], 'id', 'asc', 1, 2);
		$this->assertCount(1, $rows);
		$this->assertRowAsObject($rows[0], 3);

		//selectOne() - invalid
		$row = $db->selectOne('select_test', ['id' => 30]);
		$this->assertNull($row);

		//selectOne()
		$row = $db->selectOne('select_test', ['id' => 3]);
		$this->assertRowAsObject($row, 3);

		//selectById() - invalid
		$row = $db->selectById('select_test', 20);
		$this->assertNull($row);

		//selectById()
		$row = $db->selectById('select_test', 3);
		$this->assertRowAsObject($row, 3);

		//selectByIds()
		$rows = $db->selectByIds('select_test', [2, 3, 30], 'id', 'desc');
		$this->assertCount(2, $rows);
		$this->assertRowAsObject($row, 3);

		//selectCol()
		$vals = $db->selectCol('select_test', 'col2');
		$this->assertEquals($vals, [10,20,30,40]);

		//selectIds()
		$ids = $db->selectIds('select_test');
		$this->assertEquals($ids, [1,2,3,4]);

		//selectList()
		$list = $db->selectList('select_test', 'id', 'col1');
		$this->assertEquals($list, [1 => 'test1', 2 => 'test2', 3 => 'test3' ,4 => 'test4']);

		//selectResult
		$val = $db->selectResult('select_test', 'col1');
		$this->assertEquals($val, 'test1');
	}

	public function testCount()
	{
		$db = new Db($this->app);

		//exists()
		$exists = $db->exists('select_test', ['id' => 3]);
		$this->assertTrue($exists);

		$exists = $db->exists('select_test', ['id' => [3, 4]]);
		$this->assertTrue($exists);

		$exists = $db->exists('select_test', ['id' => 30]);
		$this->assertFalse($exists);

		//count()
		$count = $db->count('select_test');
		$this->assertEquals($count, 4);

		$count = $db->count('select_test', ['id' => 3]);
		$this->assertEquals($count, 1);

		$count = $db->count('select_test', ['id' => [3, 4, 40]]);
		$this->assertEquals($count, 2);

		//countById()
		$count = $db->countById('select_test', [3, 4, 40]);
		$this->assertEquals($count, 2);
	}

	public function testInsert()
	{
		$db = new Db($this->app);

		//insert()
		$db->query("TRUNCATE insert_test");

		$id = $db->insert('insert_test', $this->data);
		$this->assertGreaterThan(0, $id);

		$row = $db->query("SELECT * FROM insert_test WHERE col2 = 100")->fetch();
		$this->assertRowAsInsertedObject($row, $id);

		//insertMulti()
		$db->query("TRUNCATE insert_test");

		$data = [['col1' => 'test', 'col2' => 100], ['col1' => 'test2', 'col2' => 200]];
		$affected = $db->insertMulti('insert_test', $data);
		$this->assertEquals($affected, 2);
		$count = $db->count('insert_test');
		$this->assertEquals($count, 2);

		//update()
		$db->query("TRUNCATE insert_test");
		$id = $db->insert('insert_test', $this->data);

		$data = $this->data;
		$data['id'] = $id;
		$data['col1'] = 'abc';

		$affected = $db->update('insert_test', $data);
		$this->assertEquals($affected, 1);
		$row = $db->query("SELECT * FROM insert_test WHERE col1 = :col1", ['col1' => 'abc'])->fetch();
		$this->assertObjectHasAttribute('id', $row);

		//updateById()
		$data['col1'] = 'zxc';
		$affected = $db->updateById('insert_test', $data, $id);
		$this->assertEquals($affected, 1);
		$row = $db->query("SELECT * FROM insert_test WHERE col1 = :col1", ['col1' => 'zxc'])->fetch();
		$this->assertObjectHasAttribute('id', $row);

		//replace()
		$db->query("TRUNCATE insert_test");

		$data = $this->data;
		$data['id'] = 2;

		$id = $db->replace('insert_test', $data);
		$this->assertEquals($id, 2);

		$data['col1'] = '123';
		$id = $db->replace('insert_test', $data);
		$this->assertEquals($id, 2);
		$row = $db->query("SELECT * FROM insert_test WHERE id = 2")->fetch();
		$this->assertIsObject($row);
		$this->assertObjectHasAttributeAndValue('col1', '123', $row);

		//delete()
		$db->query("TRUNCATE insert_test");

		$db->insert('insert_test', $this->data);
		$affected = $db->delete('insert_test', ['col1' => 'test']);
		$count = $db->count('insert_test');
		$this->assertEquals($affected, 1);
		$this->assertEquals($count, 0);

		//deleteById()
		$id = $db->insert('insert_test', $this->data);
		$affected = $db->deleteById('insert_test', $id);
		$count = $db->count('insert_test');
		$this->assertEquals($affected, 1);
		$this->assertEquals($count, 0);

		//deleteByIds()
		$id1 = $db->insert('insert_test', $this->data);
		$id2 = $db->insert('insert_test', $this->data);
		$affected = $db->deleteByIds('insert_test', [$id1, $id2]);
		$count = $db->count('insert_test');
		$this->assertEquals($affected, 2);
		$this->assertEquals($count, 0);
	}

	public function testOtherMethods()
	{
		$db = new Db($this->app);

		//getColumns()
		$cols = $db->getColumns('select_test');
		$this->assertEquals($cols, ['id' => 'int', 'col1' => 'string', 'col2' => 'int']);

		$cols = $db->getColumns('select_test', false);
		$this->assertEquals($cols, ['col1' => 'string', 'col2' => 'int']);

		//bind()
		$values = ['col1' => 'zxc', 'col2' => 456, 'col3' => 'a', 'col4' => 'b'];
		$data = $db->bind('select_test', $values);
		$this->assertEquals($data, ['col1' => 'zxc', 'col2' => 456]);

		$data = $db->bind('select_test', $values, ['col1']);
		$this->assertEquals($data, ['col2' => 456]);

		$data = $db->bind('select_test', $values, [], '456');
		$this->assertEquals($data, ['col1' => 'zxc']);

		//bindList()
		$data = $db->bindList('select_test', $values, ['id', 'col1']);
		$this->assertEquals($data, ['col1' => 'zxc']);

		//fill()
		$data = $db->fill('select_test', [], [], 1, 'test');
		$this->assertEquals($data, ['col1' => 'test', 'col2' => 1]);

		$data = $db->fill('select_test', [], [], 1, 'test', true);
		$this->assertEquals($data, ['id' => 1, 'col1' => 'test', 'col2' => 1]);

		$data = $db->fill('select_test', ['col2' => 10], [], 1, 'test', true);
		$this->assertEquals($data, ['id' => 1, 'col1' => 'test', 'col2' => 10]);

		$data = $db->fill('select_test', ['col2' => 10], ['id', 'col1'], 1, 'test', true);
		$this->assertEquals($data, ['col2' => 10]);
	}
}
