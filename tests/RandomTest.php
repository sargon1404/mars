<?php

use Mars\Serializer;

include_once(__DIR__ . '/Base.php');

final class RandomTest extends Base
{
	public function testGetString()
	{
		$random = $this->app->random;

		$string = $random->getString(20);
		$this->assertSame(strlen($string), 20);

		$string = $random->getString(32);
		$this->assertSame(strlen($string), 32);
	}

	public function testInt()
	{
		$random = $this->app->random;

		$random_int = $random->getInt(10, 100);
		$this->assertGreaterThanOrEqual(10, $random_int);
		$this->assertLessThanOrEqual(100, $random_int);
	}
}