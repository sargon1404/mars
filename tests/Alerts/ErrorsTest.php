<?php

use Mars\Alerts\Errors;
use Mars\Alerts\Alert;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class ErrorsTest extends Base
{
	public function testErrors()
	{
		$errors = new Errors($this->app);
		$this->assertTrue($errors->ok());

		$errors->add('1st error');
		$errors->add(['2nd error', '3rd error']);
		$this->assertFalse($errors->ok());
		$this->assertSame($errors->count(), 3);
		$this->assertSame($errors->get(), ['1st error', '2nd error', '3rd error']);
		$this->assertSame($errors->getFirst(), '1st error');

		$errors->reset();
		$this->assertTrue($errors->ok());
		$this->assertSame($errors->count(), 0);
	}
}
