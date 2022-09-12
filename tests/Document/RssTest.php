<?php

use Mars\Document\Rss;

include_once(dirname(__DIR__) . '/Base.php');

/**
* @ignore
*/
final class RssTest extends Base
{
	public function testRss()
	{
		$rss = new Rss($this->app);
		$rss->load('https://www.mydomain.com/rss/feed1.xml', 'My First Rss feed');
		$rss->load('https://www.mydomain.com/rss/feed2.xml', 'My Second Rss feed');

		$this->expectOutputString(
			'<link rel="alternate" type="application/rss+xml" title="My First Rss feed" href="https://www.mydomain.com/rss/feed1.xml">' . "\n" .
			'<link rel="alternate" type="application/rss+xml" title="My Second Rss feed" href="https://www.mydomain.com/rss/feed2.xml">' . "\n"
		);
		$rss->output();
	}
}
