<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class RegistryTest extends Base
{
	public function testRegistry()
	{
		$registry = $this->app->registry;

		$this->assertNull($registry->get('my_val'));

		$registry->set('my_val', 'test123');
		$this->assertSame($registry->get('my_val'), 'test123');
	}
}
