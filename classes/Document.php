<?php
/**
* The Document Class
* @package Mars
*/

namespace Mars;

use Mars\Document\Css;
use Mars\Document\Javascript;
use Mars\Document\Meta;
use Mars\Document\Rss;
use Mars\Document\Title;

/**
* The Device Class
* Encapsulates the user's device
*/
class Document
{
	use AppTrait;

	/**
	* @var Css $css The css object
	*/
	public Css $css;

	/**
	* @var Javascript $javascript The javascript object
	*/
	public Javascript $javascript;

	/**
	* @var Meta $meta The meta object
	*/
	public Meta $meta;

	/**
	* @var  Rss $rss The rss object
	*/
	public Rss $rss;

	/**
	* @var Title $title The title object
	*/
	public Title $title;

	/**
	* Builds the device object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->css = new Css($this->app);
		$this->javascript = new Javascript($this->app);
		$this->meta = new Meta($this->app);
		$this->rss = new Rss($this->app);
		$this->title = new Title($this->app);
	}
}
