<?php

use Mars\File;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class FileTest extends Base
{
	protected $dir_write = __DIR__ . '/dir-test-write/';

	public function testCheckForInvalidChars()
	{
		$file = $this->app->file;

		$this->expectException(\Exception::class);
		$file->checkForInvalidChars('../somefile.txt');

		$this->expectException(\Exception::class);
		$file->checkForInvalidChars('./../somefile.txt');

		$file->checkForInvalidChars('somefile.txt');
	}

	public function testCheckFilename()
	{
		$file = $this->app->file;

		$this->expectException(\Exception::class);
		$file->checkFilename('../somefile.txt');

		$this->expectException(\Exception::class);
		$file->checkFilename('./../somefile.txt');

		$this->app->config->open_basedir = true;
		$file = new File($this->app);

		$this->expectException(\Exception::class);
		$file->checkFilename('/etc/php/somefile.txt');

		$this->app->config->open_basedir = '/etc/php';
		$file = new File($this->app);
		$file->checkFilename('/etc/php/somefile.txt');

		$this->app->config->open_basedir = '';
		$file = new File($this->app);
		$file->checkFilename('/etc/php/somefile.txt');
	}

	public function testGetPath()
	{
		$file = $this->app->file;

		$this->assertSame($file->getPath('/var/www/html/somefile.txt'), '/var/www/html/');
		$this->assertSame($file->getPath('.'), '');
	}

	public function testRel()
	{
		$file = $this->app->file;

		$this->assertSame($file->getRel($this->app->path . 'somefile.txt'), 'somefile.txt');
		$this->assertSame($file->getRel($this->app->path . 'dir1/dir2/somefile.txt'), 'dir1/dir2/somefile.txt');
		$this->assertSame($file->getRel('/etc/php/somefile.txt'), '/etc/php/somefile.txt');
		$this->assertSame($file->getRel('somefile.txt'), 'somefile.txt');
	}

	public function testGetFile()
	{
		$file = $this->app->file;

		$this->assertSame($file->getFile('/etc/php/somefile.txt'), 'somefile');
		$this->assertSame($file->getFile('/etc/php/../somefile.txt'), 'somefile');
		$this->assertSame($file->getFile('somefile.txt'), 'somefile');
		$this->assertSame($file->getFile('somefile'), 'somefile');
	}

	public function testGetFilename()
	{
		$file = $this->app->file;

		$this->assertSame($file->getFilename('/etc/php/somefile.txt'), 'somefile.txt');
		$this->assertSame($file->getFilename('/etc/php/../somefile.txt'), 'somefile.txt');
		$this->assertSame($file->getFilename('somefile.txt'), 'somefile.txt');
		$this->assertSame($file->getFilename('somefile'), 'somefile');
	}

	public function testAppendToFilename()
	{
		$file = $this->app->file;

		$this->assertSame($file->appendToFilename('/etc/php/somefile.txt', '_1234'), '/etc/php/somefile_1234.txt');
		$this->assertSame($file->appendToFilename('/etc/php/somefile', '_1234'), '/etc/php/somefile_1234');
		$this->assertSame($file->appendToFilename('somefile.txt', '_1234'), 'somefile_1234.txt');
	}

	public function testGetExtension()
	{
		$file = $this->app->file;

		$this->assertSame($file->getExtension('/etc/php/somefile.txt'), 'txt');
		$this->assertSame($file->getExtension('somefile.txt'), 'txt');
		$this->assertSame($file->getExtension('somefile'), '');
		$this->assertSame($file->getExtension('somefile.jpg.txt'), 'txt');

		$this->assertSame($file->getExtension('somefile.txt', true), '.txt');
		$this->assertSame($file->getExtension('somefile', true), '');
	}

	public function testAddExtension()
	{
		$file = $this->app->file;

		$this->assertSame($file->addExtension('/etc/php/somefile', 'txt'), '/etc/php/somefile.txt');
		$this->assertSame($file->addExtension('somefile', 'txt'), 'somefile.txt');
		$this->assertSame($file->addExtension('', 'txt'), '.txt');
		$this->assertSame($file->addExtension('somefile.jpg', 'txt'), 'somefile.jpg.txt');
	}

	public function testBuildPath()
	{
		$file = $this->app->file;

		$this->assertSame($file->buildPath([]), '');
		$this->assertSame($file->buildPath(['var', 'www', 'html']), '/var/www/html');
		$this->assertSame($file->buildPath(['var', 'www', 'html'], true), '/var/www/html/');
		$this->assertSame($file->buildPath(['var', 'www', '', 'html', 'somefile.txt']), '/var/www/html/somefile.txt');
	}

	public function testGetSubdir()
	{
		$file = $this->app->file;

		$this->assertSame($file->getSubdir('somefolder'), 'some/');
		$this->assertSame($file->getSubdir('somefolder', false, 6), 'somefo/');
	}

	public function testIsImage()
	{
		$file = $this->app->file;

		$this->assertSame($file->isImage('somefile'), false);
		$this->assertSame($file->isImage('somefile.jpg'), true);
		$this->assertSame($file->isImage('/var/www/html/somefile.jpg'), true);
	}

	public function testRead()
	{
		$file = $this->app->file;
		$filename = $this->dir_write . 'myfile.txt';

		$this->expectException(\Exception::class);
		$file->read('/etc/php/somefile.txt');

		file_put_contents($filename, 'abc');
		$this->assertSame($file->read($filename), 'abc');
		unlink($filename);
	}

	public function testWrite()
	{
		$file = $this->app->file;
		$filename = $this->dir_write . 'myfile.txt';

		$this->expectException(\Exception::class);
		$file->write('/etc/php/somefile.txt', 'abc');

		$file->write($filename, 'abc');

		$this->assertSame(file_get_contents($filename), 'abc');

		unlink($filename);
	}

	public function testDelete()
	{
		$file = $this->app->file;
		$filename = $this->dir_write . 'myfile.txt';

		file_put_contents($filename, 'abc');
		$file->delete($filename);

		$this->assertSame(is_file($filename), false);
	}

	public function testCopy()
	{
		$file = $this->app->file;
		$filename = $this->dir_write . 'myfile.txt';
		$filename_dest = $this->dir_write . 'myfile2.txt';

		file_put_contents($filename, 'abc');
		$file->copy($filename, $filename_dest);

		$this->assertSame(is_file($filename), true);
		$this->assertSame(is_file($filename_dest), true);
		$this->assertSame(file_get_contents($filename_dest), 'abc');

		unlink($filename);
		unlink($filename_dest);
	}

	public function testMove()
	{
		$file = $this->app->file;
		$filename = $this->dir_write . 'myfile.txt';
		$filename_dest = $this->dir_write . 'myfile2.txt';

		file_put_contents($filename, 'abc');
		$file->move($filename, $filename_dest);

		$this->assertSame(is_file($filename), false);
		$this->assertSame(is_file($filename_dest), true);
		$this->assertSame(file_get_contents($filename_dest), 'abc');

		unlink($filename_dest);
	}
}