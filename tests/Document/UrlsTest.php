<?php

use Mars\Document\Css;
use Mars\Document\Javascript;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class UrlsTest extends Base
{
	public function setUp() : void
	{
		parent::setUp();

		$this->app->config->css_version = '123';
	}

	public function testCss()
	{
		$css = new Css($this->app);
		$css->add('https://mydomain/css/style-1.css', 'head', 100);
		$css->add('https://mydomain/css/style-2.css', 'head', 200);
		$css->add('https://mydomain/css/style-3.css', 'first', 300);

		$this->assertSame($css->get(), [
			'https://mydomain/css/style-3.css' => ['location' => 'first', 'priority' => 300, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/css/style-2.css' => ['location' => 'head', 'priority' => 200, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/css/style-1.css' => ['location' => 'head', 'priority' => 100, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
		]);

		$this->assertSame($css->get('head'), [
			'https://mydomain/css/style-2.css' => ['location' => 'head', 'priority' => 200, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/css/style-1.css' => ['location' => 'head', 'priority' => 100, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
		]);

		$this->assertSame($css->get('invalidlocation'), []);
	}

	public function testJavascript()
	{
		$js = new Javascript($this->app);
		$js->add('https://mydomain/js/script-1.css', 'head', 100);
		$js->add('https://mydomain/js/script-2.css', 'head', 200);
		$js->add('https://mydomain/js/script-3.css', 'first', 300);

		$this->assertSame($js->get(), [
			'https://mydomain/js/script-3.css' => ['location' => 'first', 'priority' => 300, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/js/script-2.css' => ['location' => 'head', 'priority' => 200, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/js/script-1.css' => ['location' => 'head', 'priority' => 100, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
		]);

		$this->assertSame($js->get('head'), [
			'https://mydomain/js/script-2.css' => ['location' => 'head', 'priority' => 200, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
			'https://mydomain/js/script-1.css' => ['location' => 'head', 'priority' => 100, 'version' => true, 'async' => false, 'defer' => false, 'is_local' => false],
		]);

		$this->assertSame($js->get('invalidlocation'), []);
	}
}
