<?php

use Mars\Extensions\Plugin;

include_once(dirname(__DIR__) . '/Base.php');

/**
* @ignore
*/
final class PluginTest extends Base
{
	public function testConstruct()
	{
		$plugin = new Plugin('foo');

		$this->assertSame($plugin->path, $this->app->path . 'extensions/plugins/foo/');
		$this->assertSame($plugin->url, $this->app->url . 'extensions/plugins/foo/');
		$this->assertSame($plugin->url_static, $this->app->url_static . 'extensions/plugins/foo/');
	}
}
