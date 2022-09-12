<?php

use Mars\Elements;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class ElementsTest extends Base
{
	public function testHeaders()
	{
		$elements = new Elements;

		$this->assertSame($elements->get(), []);
		$this->assertNull($elements->get('X-Test-Header'));

		$elements->add('X-Test-Header', 'test123');
		$elements->add('X-Test-Header2', 'test345');
		$this->assertSame($elements->get('X-Test-Header'), 'test123');
		$this->assertSame($elements->get(), ['X-Test-Header' => 'test123', 'X-Test-Header2' => 'test345']);

		$elements->remove('X-Test-Header');
		$this->assertNull($elements->get('X-Test-Header'));
	}
}
