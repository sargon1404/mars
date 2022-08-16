<?php

use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
class MyError
{
	use \Mars\ErrorsTrait;

	public function __construct()
	{
		$this->errors[] = 'First Error';
		$this->errors[] = 'Second Error';
	}
}

/**
* @ignore
*/
final class ErrorsTest extends Base
{
	public function testErrors()
	{
		$error = new MyError;

		$this->assertSame($error->getFirstError(), 'First Error');
		$this->assertSame($error->getErrors(), ['First Error', 'Second Error']);
	}
}
