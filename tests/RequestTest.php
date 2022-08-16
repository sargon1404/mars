<?php

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class RequestTest extends Base
{
	public function testRequestHas()
	{
		$request = $this->app->request;

		$this->assertFalse($request->get->has('myvar'));

		$_GET['myvar'] = 123;
		$this->assertTrue($request->get->has('myvar'));
	}

	public function testRequestGet()
	{
		$request = $this->app->request;

		$_GET['myvar'] = ' some string 1234 ';

		$this->assertSame($request->get->get('myvar'), 'some string 1234');
		$this->assertSame($request->get->get('myvar', 'alpha'), 'somestring');
		$this->assertSame($request->get->get('myvar', '', true), ['some string 1234']);
	}

	public function testRequestGetRaw()
	{
		$request = $this->app->request;

		$_GET['myvar'] = ' some string 1234 ';

		$this->assertSame($request->get->getRaw('myvar'), ' some string 1234 ');
	}

	public function testRequestGetAll()
	{
		$request = $this->app->request;

		$_GET = [];
		$_GET['myvar'] = ' some string 1234 ';
		$_GET['myvar2'] = 'xyz';

		$this->assertSame($request->get->getAll(), ['myvar' => ' some string 1234 ', 'myvar2' => 'xyz']);
	}

	public function testRequestSet()
	{
		$request = $this->app->request;

		$_GET['myvar'] = '123';

		$this->assertSame($request->get->get('myvar'), '123');
		$request->get->set('myvar', 'xyz');
		$this->assertSame($request->get->get('myvar'), 'xyz');
	}

	public function testRequestUnset()
	{
		$request = $this->app->request;

		$_GET['myvar'] = '123';

		$this->assertSame($request->get->get('myvar'), '123');
		$request->get->unset('myvar');
		$this->assertFalse($request->get->has('myvar'));
	}

	public function testRequestFill()
	{
		$request = $this->app->request;

		$_GET = [
			'var1'=> 'a',
			'var2' => 'b',
			'var3' => 'c'
		];

		$data = [
			'var1' => '',
			'var2' => ''
		];

		$this->assertSame($request->get->fill($data), ['var1' => 'a', 'var2' => 'b']);
		$this->assertSame($request->get->fill($data, ['var2' => 'int']), ['var1' => 'a', 'var2' => 0]);
		$this->assertSame($request->get->fill($data, ['var2' => 'int'], [], ['var2']), ['var1' => 'a', 'var2' => '']);
	}

	public function testGetType()
	{
		$request = $this->app->request;

		$_REQUEST[$this->app->config->request_type_param] = 'html';

		$this->assertSame($request->getType(), 'html');
	}

	public function testisAjax()
	{
		$request = $this->app->request;

		$_REQUEST[$this->app->config->request_type_param] = 'html';

		$this->assertFalse($request->isAjax());

		$_REQUEST[$this->app->config->request_type_param] = ' ajax ';
		$this->assertTrue($request->isAjax());
	}

	public function testGetOrderBy()
	{
		$request = $this->app->request;

		$_REQUEST[$this->app->config->request_orderby_param] = 'myfield';
		$this->assertSame($request->getOrderBy(), 'myfield');

		$this->assertSame($request->getOrderBy(['myfield', 'val']), 'myfield');
		$this->assertSame($request->getOrderBy(['myfield' => 'mycol', 'val' => 'myval']), 'mycol');
		$this->assertSame($request->getOrderBy(['val']), '');
		$this->assertSame($request->getOrderBy(['val'], 'abc'), 'abc');
	}

	public function testGetOrder()
	{
		$request = $this->app->request;

		$_REQUEST[$this->app->config->request_order_param] = ' ASC ';
		$this->assertSame($request->getOrder(), 'ASC');

		$_REQUEST[$this->app->config->request_order_param] = ' asc ';
		$this->assertSame($request->getOrder(), 'ASC');

		$_REQUEST[$this->app->config->request_order_param] = ' ascx ';
		$this->assertSame($request->getOrder(), '');
	}

	public function testGetPage()
	{
		$request = $this->app->request;

		$_REQUEST[$this->app->config->request_page_param] = 'abc';
		$this->assertSame($request->getPage(), 0);

		$_REQUEST[$this->app->config->request_page_param] = 20;
		$this->assertSame($request->getPage(), 20);

		$_REQUEST[$this->app->config->request_page_param] = -20;
		$this->assertSame($request->getPage(), 20);
	}
}
