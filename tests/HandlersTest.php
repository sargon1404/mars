<?php

use Mars\Handlers;

include_once(__DIR__ . '/Base.php');

class HandlerObj
{
	public function get()
	{
		return 'test123';
	}
}

class HandlerList
{
	public $handlers;

	public function __construct()
	{
		$this->handlers = new Handlers([]);
	}
}

/**
* @ignore
*/
final class HandlersTest extends Base
{
	public function testMain()
	{
		$list = new HandlerList;
		$list->handlers->add('my_handler', 'HandlerObj');

		$this->assertSame($list->handlers->getList(), ['my_handler' => 'HandlerObj']);

		$handler_obj = $list->handlers->get('my_handler');
		$this->assertTrue($handler_obj instanceof HandlerObj);
		$this->assertSame($handler_obj->get(), 'test123');

		$list->handlers->remove('my_handler');

		$this->expectException(\Exception::class);
		$handler_obj = $list->handlers->get('my_handler');
	}
}
