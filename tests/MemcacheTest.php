<?php

use Mars\App;
use Mars\Memcache;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class MemcacheTest extends Base
{
	public function setUp() : void
	{
		parent::setUp();

		$this->app->config->memcache_enable = true;
	}

	protected function getKey() : string
	{
		return 'test-key-' . time() . mt_rand(0, 99999);
	}

	protected function runAssertions($memcache)
	{
		$key = $this->getKey();

		$this->assertTrue($memcache->add($key, '12345'));
		$this->assertTrue($memcache->exists($key));
		$this->assertFalse($memcache->exists($this->getKey()));
		$this->assertEquals($memcache->get($key), '12345');

		$this->assertTrue($memcache->set($key, 'abcdef'));
		$this->assertEquals($memcache->get($key), 'abcdef');
		$this->assertTrue($memcache->delete($key));
		$this->assertFalse($memcache->exists($key));
		$this->assertFalse($memcache->get($key));
	}

	public function testMemcacheConnection()
	{
		$memcache = new Memcache($this->app, 'memcache', '127.0.0.1', '11211');

		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testMemcache()
	{
		$memcache = new Memcache($this->app, 'memcache', '127.0.0.1', '11211');

		$this->runAssertions($memcache);
	}

	public function testMemcachedConnection()
	{
		$memcache = new Memcache($this->app, 'memcached', '127.0.0.1', '11211');

		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testMemcached()
	{
		$memcache = new Memcache($this->app, 'memcached', '127.0.0.1', '11211');

		$this->runAssertions($memcache);
	}

	public function testRedisConnection()
	{
		$memcache = new Memcache($this->app, 'redis', '127.0.0.1', '6379');

		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testRedis()
	{
		$memcache = new Memcache($this->app, 'redis', '127.0.0.1', '6379');

		$this->runAssertions($memcache);
	}

	/*public function testInvalidMemcacheConnection()
	{
		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app, 'memcache', '127.0.0.1', '11212');

		$invalid_memcache->add('test_key', '12345');
	}*/

	/*public function testInvalidMemcachedConnection()
	{
		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app, 'memcached', '127.0.0.1', '11312');

		$invalid_memcache->add('test_key', '12345');
	}*/

	public function testInvalidRedisConnection()
	{
		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app, 'redis', '127.0.0.1', '11312');

		$invalid_memcache->add('test_key', '12345');
	}
}
