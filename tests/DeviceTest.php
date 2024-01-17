<?php

use Mars\Device\MobileDetect;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class DeviceTest extends Base
{
	public function testDevice()
	{
		$detect = new MobileDetect($this->app);

		$this->assertSame($detect->get(''), 'desktop');
		$this->assertSame($detect->get('Mozilla/5.0 (iPad; CPU OS 15_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/103.0.5060.63 Mobile/15E148 Safari/604.1'), 'tablet');
		$this->assertSame($detect->get('Mozilla/5.0 (iPad; CPU OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148'), 'tablet');

		$this->assertSame($detect->get('Opera/9.80 (Android 4.1.2; Linux; Opera Mobi/ADR-1305251841) Presto/2.11.355 Version/12.10'), 'smartphone');
		$this->assertSame($detect->get('Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'), 'smartphone');
	}
}
