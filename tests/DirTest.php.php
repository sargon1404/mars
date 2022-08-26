<?php

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class DirTest extends Base
{
	protected $dir_cnt = __DIR__ . '/data/dir-test/';
	protected $dir_read = __DIR__ . '/data/dir-test-read/';
	protected $dir_write = __DIR__ . '/data/dir-test-write/';

	public function testContains()
	{
		$dir = $this->app->dir;

		$this->assertSame($dir->contains('/var/www', '/var/www/myfilename.jpg'), true);
		$this->assertSame($dir->contains('/var/www/', '/var/www/myfilename.jpg'), true);
		$this->assertSame($dir->contains('/var/www/', '/var/www/qqq/myfilename.jpg'), true);
		$this->assertSame($dir->contains('/var/www/', '/var/temp/myfilename.jpg'), false);
		$this->assertSame($dir->contains('/var/temp/', '/var/www/myfilename.jpg'), false);
	}

	public function testGetDir()
	{
		$dir = $this->app->dir;

		$this->assertSame($dir->getDirs($this->dir_read), [$this->dir_read . 'a', $this->dir_read . 'b']);
		$this->assertSame($dir->getDirs($this->dir_read, true), [$this->dir_read . 'a', $this->dir_read . 'a/aa', $this->dir_read . 'b']);
		$this->assertSame($dir->getDirs($this->dir_read, true, false), ['a', 'a/aa', 'b']);
		$this->assertSame($dir->getDirs($this->dir_read, true, true, ['b']), [$this->dir_read . 'a', $this->dir_read . 'a/aa']);
	}

	public function testCreate()
	{
		$dir = $this->app->dir;

		$dir->create($this->dir_write . 'test');
		$this->assertSame(is_dir($this->dir_write . 'test'), true);
		rmdir($this->dir_write . 'test');
	}

	public function testCopy()
	{
		$dir = $this->app->dir;

		$source_dir = $this->dir_cnt;
		$dest_dir = $this->dir_write . 'test/';

		$dir->copy($source_dir, $dest_dir);
		$this->assertSame(is_dir($dest_dir . 'a'), true);
		$this->assertSame(is_file($dest_dir . 'file1.txt'), true);
		$this->assertSame(is_file($dest_dir . 'a/file2.txt'), true);

		unlink($dest_dir . 'file1.txt');
		unlink($dest_dir . 'a/file2.txt');

		rmdir($dest_dir . 'a');
		rmdir($dest_dir);
	}

	public function testMove()
	{
		$dir = $this->app->dir;

		$source_dir = $this->dir_write . 'test/';
		$dest_dir = $this->dir_write . 'test1/';

		mkdir($source_dir);

		$dir->move($source_dir, $dest_dir);

		$this->assertSame(is_dir($source_dir), false);
		$this->assertSame(is_dir($dest_dir), true);

		rmdir($dest_dir);
	}

	public function testDelete()
	{
		$dir = $this->app->dir;

		$source_dir = $this->dir_cnt;
		$dest_dir = $this->dir_write . 'test/';

		$dir->copy($source_dir, $dest_dir);
		$dir->delete($dest_dir);

		$this->assertSame(is_dir($dest_dir), false);
	}

	public function testClean()
	{
		$dir = $this->app->dir;

		$source_dir = $this->dir_cnt;
		$dest_dir = $this->dir_write . 'test/';

		$dir->copy($source_dir, $dest_dir);
		$dir->clean($dest_dir);

		$this->assertSame(is_dir($dest_dir), true);
		$this->assertSame(is_dir($dest_dir . 'a'), false);
		$this->assertSame(is_file($dest_dir . 'file1.txt'), false);

		rmdir($dest_dir);
	}
}