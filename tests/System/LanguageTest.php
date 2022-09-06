<?php

use Mars\System\Language;

include_once(dirname(__DIR__) . '/Base.php');

/**
* @ignore
*/
final class LanguageTest extends Base
{
	public function testConstruct()
	{
		$language = new Language($this->app);

		$this->assertSame($language->path, $this->app->path . 'extensions/languages/' . $this->app->config->lang . '/');
		$this->assertSame($language->url, $this->app->url . 'extensions/languages/' . $this->app->config->lang . '/');
		$this->assertSame($language->url_static, $this->app->url_static . 'extensions/languages/' . $this->app->config->lang . '/');
	}

	public function testLoadFilename()
	{
		$filename = dirname(__DIR__) . '/data/strings.php';

		$language = new Language($this->app);
		$language->strings = [];
		$language->loadFilename($filename);

		$this->assertSame($language->strings, ['str1' => 'String 1', 'str2' => 'String 2']);
	}
}
