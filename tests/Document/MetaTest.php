<?php

use Mars\Document\Meta;

include_once(dirname(__DIR__) . '/Base.php');

/**
* @ignore
*/
final class MetaTest extends Base
{
	public function testMeta()
	{
		$meta = new Meta($this->app);
		$meta->add('author', 'John Doe');
		$meta->add('keywords', 'k1, k2');

		$this->expectOutputString(
			'<meta name="author" content="John Doe">' . "\n" .
			'<meta name="keywords" content="k1, k2">' . "\n"
		);
		$meta->output();
	}
}
