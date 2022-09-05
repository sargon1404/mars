<?php
/**
* The Image Driver Interface
* @package Mars
*/

namespace Mars\Images;

use Mars\App;
use GdImage;

/**
* The Image Driver Interface
*/
interface DriverInterface
{
	/**
	* Builds the image object
	* @param string $filename The image's filename
	* @param App $app The app object
	*/
	public function __construct(string $filename, App $app = null);

	/**
	* Determines if the image is valid
	* @return bool
	*/
	public function isValid() : bool;

	/**
	* Returns the size (width/height) of the image
	* @return array
	* @throws Exception
	*/
	public function getSize() : array;

	/**
	* Returns the width of the image
	* @return int
	* @throws Exception
	*/
	public function getWidth(): int;

	/**
	* Returns the height of the image
	* @return int
	* @throws Exception
	*/
	public function getHeight(): int;

	/**
	* Returns the radio between width and height
	* @return float
	* @throws Exception
	*/
	public function getRatio() : float;

	/**
	* Opens the file as a GdImage
	* @return GdImage
	* @throws Exception
	*/
	public function open() : GdImage;

	/**
	* Creates a GdImage object
	* @param int $width The image's width
	* @param int $height The image's height
	* @param GdImage The source to create the image from
	* @return GdImage
	*/
	public function create(int $width, int $height, GdImage $source) : GdImage;

	/**
	* Saves a GdImage object
	* @return GdImage
	* @throws Exception
	*/
	public function save(GdImage $img);

	/**
	* Optimizes the image
	* @return bool Returns true, if the image was optimized
	*/
	public function optimize() : bool;
}
