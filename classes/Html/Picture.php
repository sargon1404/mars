<?php
/**
* The Picture Class
* @package Mars
*/

namespace Mars\Html;

/**
* The Picture Class
* Renders a picture
*/
class Picture extends \Mars\Html\Tag
{
	/**
	* @var array $images Array listing the images to display in the picture
	*/
	public array $images = [];

	/**
	* {@inheritdoc}
	*/
	protected string $tag = 'picture';

	/**
	* @see \Mars\Html\TagInterface::get()
	* {@inheritdoc}
	*/
	public function get() : string
	{
		$img = new Img($this->attributes);

		$html = "<{$this->tag}>\n";
		$html.= $this->getImages();
		$html.= $img->get();
		$html.= "</{$this->tag}>\n";

		return $html;
	}

	/**
	* Returns the html code of the source images
	* @return string
	*/
	protected function getImages() : string
	{
		$html = '';
		foreach ($this->images as $image) {
			$media_array = [];
			if (isset($image['min'])) {
				$media_array[] = "(min-width:{$image['min']}px)";
			}
			if (isset($image['max'])) {
				$media_array[] = "(max-width:{$image['max']}px)";
			}

			$media = implode(' and ', $media_array);

			$html.= '<source media="' . $media . '" srcset="' . $this->app->escape->html($image['url']) . '">' . "\n";
		}

		return $html;
	}
}
