<?php

use Mars\Image;

include_once(__DIR__ . '/Base.php');

/**
* @ignore
*/
final class ImageTest extends Base
{
	protected $images_dir = __DIR__ . '/data/images/';

	public function testUnsupported()
	{
		$this->expectException(\Exception::class);

		$image = new Image($this->images_dir . 'invalid-image.txt');
	}

	public function testJpg()
	{
		$image = new Image($this->images_dir . 'invalid-image.jpg');
		$this->assertFalse($image->isValid());

		$image = new Image($this->images_dir . 'image.jpg');
		$this->assertTrue($image->isValid());
		$this->assertSame($image->getSize(), [1280, 853]);
		$this->assertSame($image->getWidth(), 1280);
		$this->assertSame($image->getHeight(), 853);

		//resize - both width and height
		$resized_image = $image->resize($this->images_dir . 'image-resized.jpg', 400, 200);
		$this->assertTrue(is_file($resized_image->getFilename()));
		$this->assertTrue($resized_image->isValid());
		$this->assertSame($resized_image->getSize(), [400, 200]);
		unlink($resized_image->getFilename());

		//resize - by width
		$resized_image = $image->resize($this->images_dir . 'image-resized.jpg', 400);
		$this->assertSame($resized_image->getFilename(), $this->images_dir . 'image-resized.jpg');
		$this->assertTrue(is_file($resized_image->getFilename()));
		$this->assertTrue($resized_image->isValid());
		$this->assertSame($resized_image->getSize(), [400, (int) (400 / $image->getRatio())]);
		unlink($resized_image->getFilename());

		//resize - by height
		$resized_image = $image->resize($this->images_dir . 'image-resized.jpg', 0, 400);
		$this->assertSame($resized_image->getFilename(), $this->images_dir . 'image-resized.jpg');
		$this->assertTrue(is_file($resized_image->getFilename()));
		$this->assertTrue($resized_image->isValid());
		$this->assertSame($resized_image->getSize(), [(int) (400 * $image->getRatio()), 400]);
		unlink($resized_image->getFilename());


		//crop
		$cropped_image = $image->crop($this->images_dir . 'image-cropped.jpg', 400, 200);
		$this->assertSame($cropped_image->getFilename(), $this->images_dir . 'image-cropped.jpg');
		$this->assertTrue(is_file($cropped_image->getFilename()));
		$this->assertTrue($cropped_image->isValid());
		$this->assertSame($cropped_image->getSize(), [400, 200]);
		unlink($cropped_image->getFilename());

		$cropped_image = $image->crop($this->images_dir . 'image-cropped.jpg', 200, 400);
		$this->assertSame($cropped_image->getFilename(), $this->images_dir . 'image-cropped.jpg');
		$this->assertTrue(is_file($cropped_image->getFilename()));
		$this->assertTrue($cropped_image->isValid());
		$this->assertSame($cropped_image->getSize(), [200, 400]);
		unlink($cropped_image->getFilename());


		//cut
		$cropped_image = $image->cut($this->images_dir . 'image-cut.jpg', 400, 200);
		$this->assertSame($cropped_image->getFilename(), $this->images_dir . 'image-cut.jpg');
		$this->assertTrue(is_file($cropped_image->getFilename()));
		$this->assertTrue($cropped_image->isValid());
		$this->assertSame($cropped_image->getSize(), [400, 200]);
		unlink($cropped_image->getFilename());

		$cropped_image = $image->cut($this->images_dir . 'image-cut.jpg', 200, 400);
		$this->assertSame($cropped_image->getFilename(), $this->images_dir . 'image-cut.jpg');
		$this->assertTrue(is_file($cropped_image->getFilename()));
		$this->assertTrue($cropped_image->isValid());
		$this->assertSame($cropped_image->getSize(), [200, 400]);
		unlink($cropped_image->getFilename());


		//convert
		$png_image = $image->convert($this->images_dir . 'image-convert.png');
		$this->assertSame($png_image->getFilename(), $this->images_dir . 'image-convert.png');
		$this->assertTrue(is_file($png_image->getFilename()));
		$this->assertTrue($png_image->isValid());
		unlink($png_image->getFilename());

		$gif_image = $image->convert($this->images_dir . 'image-convert.gif');
		$this->assertSame($gif_image->getFilename(), $this->images_dir . 'image-convert.gif');
		$this->assertTrue(is_file($gif_image->getFilename()));
		$this->assertTrue($gif_image->isValid());
		unlink($gif_image->getFilename());


		$webp_image = $image->convert($this->images_dir . 'image-convert.webp');
		$this->assertSame($webp_image->getFilename(), $this->images_dir . 'image-convert.webp');
		$this->assertTrue(is_file($webp_image->getFilename()));
		$this->assertTrue($webp_image->isValid());
		unlink($webp_image->getFilename());
	}

	public function testJpeg()
	{
		$image = new Image($this->images_dir . 'invalid-image.jpeg');
		$this->assertFalse($image->isValid());

		$image = new Image($this->images_dir . 'image.jpeg');
		$this->assertTrue($image->isValid());
	}
}
