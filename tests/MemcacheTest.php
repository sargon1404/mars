<?php

use Mars\App;
use Mars\Memcache;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class MemcacheTest extends Base
{
	protected $driver = '';

	protected $host = '';

	protected $port = '';

	public function setUp() : void
	{
		parent::setUp();

		$this->app->config->memcache_enable = true;
		$this->driver = $this->app->config->memcache_driver;
		$this->host = $this->app->config->memcache_host;
		$this->port = $this->app->config->memcache_port;
	}

	public function tearDown() : void
	{
		$this->app->config->memcache_driver = $this->driver;
		$this->app->config->memcache_host = $this->host;
		$this->app->config->memcache_port = $this->port;
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
		$this->app->config->memcache_driver = 'memcache';

		$memcache = new Memcache($this->app);
		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testMemcache()
	{
		$this->app->config->memcache_driver = 'memcache';

		$memcache = new Memcache($this->app);
		$this->runAssertions($memcache);
	}

	public function testMemcachedConnection()
	{
		$this->app->config->memcache_driver = 'memcached';

		$memcache = new Memcache($this->app);
		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testMemcached()
	{
		$this->app->config->memcache_driver = 'memcached';

		$memcache = new Memcache($this->app);
		$this->runAssertions($memcache);
	}

	public function testRedisConnection()
	{
		$this->app->config->memcache_driver = 'redis';
		$this->app->config->memcache_port = '6379';

		$memcache = new Memcache($this->app);
		$this->assertTrue($memcache->add($this->getKey(), '12345'));
	}

	public function testRedis()
	{
		$this->app->config->memcache_driver = 'redis';
		$this->app->config->memcache_port = '6379';

		$memcache = new Memcache($this->app);

		$this->runAssertions($memcache);
	}

	/*public function testInvalidMemcacheConnection()
	{
		$this->app->config->memcache_driver = 'memcache';
		$this->app->config->memcache_port = '11312';

		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app);

		$invalid_memcache->add('test_key', '12345');
	}*/

	/*public function testInvalidMemcachedConnection()
	{
		$this->app->config->memcache_driver = 'memcached'
		$this->app->config->memcache_port = '11312';

		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app, 'memcached', '127.0.0.1', '11312');

		$invalid_memcache->add('test_key', '12345');
	}*/

	public function testInvalidRedisConnection()
	{
		$this->app->config->memcache_driver = 'redis';
		$this->app->config->memcache_port = '11312';

		$this->expectNotice();
		$this->expectWarning();
		$this->expectException(\Exception::class);

		$invalid_memcache = new Memcache($this->app);

		$invalid_memcache->add('test_key', '12345');
	}
}
