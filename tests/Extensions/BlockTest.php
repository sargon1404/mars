<?php

use Mars\Extensions\Block;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class BlockTest extends Base
{
	public function testConstruct()
	{
		$block = new Block('foo/bar');

		$this->assertSame($block->path, $this->app->path . '/extensions/modules/foo/blocks/bar');
		$this->assertSame($block->url, $this->app->url . '/extensions/modules/foo/blocks/bar');
		$this->assertSame($block->url_static, $this->app->url_static . '/extensions/modules/foo/blocks/bar');
	}
}
