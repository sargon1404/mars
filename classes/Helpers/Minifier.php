<?php
/**
* The Minifier Class
* @package Mars
*/

namespace Mars\Helpers;

use Mars\App;
use Mars\Handlers;

/**
* The Asset Minifier Class
* Minifies assets content
*/
class Minifier
{
	use \Mars\AppTrait;

	/**
	* @var Handlers $handlers The screens handlers
	*/
	public readonly Handlers $handlers;

	/**
	* @var array $minifiers_list The list of supported minifiers
	*/
	protected array $minifiers_list = [
		'html' => '\Mars\Minifiers\Html',
		'css' => '\Mars\Minifiers\Css',
		'js' => '\Mars\Minifiers\Javascript'
	];

	/**
	* Constructs the screens object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app ?? $this->getApp();
		$this->handlers = new Handlers($this->minifiers_list, $this->app);
	}

	/**
	* Minifies html code
	* @param string $code The code to minify
	* @return string The minified code
	*/
	public function minifyHtml(string $code) : string
	{
		$handler = $this->handlers->get('html');

		return $handler->minify($code);
	}

	/**
	* Minifies css code
	* @param string $code The code to minify
	* @return string The minified code
	*/
	public function minifyCss(string $code) : string
	{
		$handler = $this->handlers->get('css');

		return $handler->minify($code);
	}

	/**
	* Minifies css code
	* @param string $code The code to minify
	* @return string The minified code
	*/
	public function minifyJs(string $code) : string
	{
		$handler = $this->handlers->get('js');

		return $handler->minify($code);
	}
}
