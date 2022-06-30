<?php


include_once(__DIR__ . '/Base.php');

final class LogTest extends Base
{
	public function testMessage()
	{
		$log = $this->app->log;

		$filename = $this->app->log->getFilename('messages');
		$log->message('some message');
		$this->assertFileExists($filename);

		$filename = $this->app->log->getFilename('errors');
		$log->error('some message');
		$this->assertFileExists($filename);
	}
}
