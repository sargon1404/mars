<?php

use Mars\Extensions\Module;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class ModuleTest extends Base
{
	public function testConstruct()
	{
		$module = new Module('foo');

		$this->assertSame($module->path, $this->app->path . '/extensions/modules/foo');
		$this->assertSame($module->url, $this->app->url . '/extensions/modules/foo');
		$this->assertSame($module->url_static, $this->app->url_static . '/extensions/modules/foo');
	}
}
