<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

final class TimeTest extends Base
{
	public function testGetTimestamp()
	{
		$time = $this->app->time;

		$this->assertEquals($time->getTimestamp(0), 0);
		$this->assertEquals($time->getTimestamp(''), 0);
		$this->assertEquals($time->getTimestamp('1642505072'), 1642505072);
		$this->assertEquals($time->getTimestamp('2022-01-18 13:25:39'), 1642512339);
	}

	public function testGetDatetime()
	{
		$time = $this->app->time;

		$this->assertEquals($time->getDatetime('0'), '0000-00-00 00:00:00');
		$this->assertEquals($time->getDatetime(''), '0000-00-00 00:00:00');
		$this->assertEquals($time->getDatetime('2022-01-18 11:24:32'), '2022-01-18 11:24:32');
		$this->assertEquals($time->getDatetime('1642505072'), '2022-01-18 11:24:32');
	}

	public function testGetDate()
	{
		$time = $this->app->time;

		$this->assertEquals($time->getDate('0'), '0000-00-00');
		$this->assertEquals($time->getDate(''), '0000-00-00');
		$this->assertEquals($time->getDate('2022-01-18 11:24:32'), '2022-01-18');
		$this->assertEquals($time->getDate('1642505072'), '2022-01-18');
	}

	public function testGetTime()
	{
		$time = $this->app->time;

		$this->assertEquals($time->getTime('0'), '00:00:00');
		$this->assertEquals($time->getTime(''), '00:00:00');
		$this->assertEquals($time->getTime('2022-01-18 11:24:32'), '11:24:32');
		$this->assertEquals($time->getTime('1642505072'), '11:24:32');
	}

	public function testAdd()
	{
		$time = $this->app->time;

		$this->assertEquals($time->add(1, 'dayxx', '2022-01-18 11:24:32'), $time->get('2022-01-18 11:24:32'));
		$this->assertEquals($time->add(1, 'day', '2022-01-18 11:24:32'), $time->get('2022-01-19 11:24:32'));
		$this->assertNotEquals($time->add(1, 'day', '2022-01-18 11:24:32'), $time->get('2022-01-10 11:24:32'));
		$this->assertEquals($time->add(10, 'days', '2022-01-18 11:24:32'), $time->get('2022-01-28 11:24:32'));
		$this->assertEquals($time->add(1, 'month', '2022-01-18 11:24:32'), $time->get('2022-02-18 11:24:32'));
		$this->assertEquals($time->add(10, 'months', '2022-01-18 11:24:32'), $time->get('2022-11-18 11:24:32'));
		$this->assertEquals($time->add(1, 'year', '2022-01-18 11:24:32'), $time->get('2023-01-18 11:24:32'));
		$this->assertEquals($time->add(10, 'years', '2022-01-18 11:24:32'), $time->get('2032-01-18 11:24:32'));
	}

	public function testSub()
	{
		$time = $this->app->time;

		$this->assertEquals($time->sub(1, 'dayxx', '2022-01-18 11:24:32'), $time->get('2022-01-18 11:24:32'));
		$this->assertEquals($time->sub(1, 'day', '2022-01-18 11:24:32'), $time->get('2022-01-17 11:24:32'));
		$this->assertNotEquals($time->sub(1, 'day', '2022-01-18 11:24:32'), $time->get('2022-01-10 11:24:32'));
		$this->assertEquals($time->sub(10, 'days', '2022-01-18 11:24:32'), $time->get('2022-01-08 11:24:32'));
		$this->assertEquals($time->sub(1, 'month', '2022-01-18 11:24:32'), $time->get('2021-12-18 11:24:32'));
		$this->assertEquals($time->sub(10, 'months', '2022-01-18 11:24:32'), $time->get('2021-03-18 11:24:32'));
		$this->assertEquals($time->sub(1, 'year', '2022-01-18 11:24:32'), $time->get('2021-01-18 11:24:32'));
		$this->assertEquals($time->sub(10, 'years', '2022-01-18 11:24:32'), $time->get('2012-01-18 11:24:32'));
	}

	public function testGetMinutes()
	{
		$time = $this->app->time;

		$this->assertEquals($time->getMinutes(60), ['minutes' => 1, 'seconds' => 0]);
		$this->assertEquals($time->getMinutes(70), ['minutes' => 1, 'seconds' => 10]);
		$this->assertEquals($time->getMinutes(133), ['minutes' => 2, 'seconds' => 13]);
	}
}