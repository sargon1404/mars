<?php
/**
* The Img Class
* @package Mars
*/

namespace Mars\Html;

/**
* The Img Class
* Renders an image
*/
class Img extends \Mars\Html\Tag
{
	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'img';

	/**
	* {@inheritdoc}
	*/
	protected string $newline = '';
}
