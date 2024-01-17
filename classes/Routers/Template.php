<?php
/**
* The Block Router Class
* @package Mars
*/

namespace Mars\Routers;

use Mars\App;

/**
 * The Block Router Class
 * Routes to a block
 */
class Template implements HandlerInterface
{
	use \Mars\AppTrait;
	
	/**
	 * @var string $name The template's name
	 */
	protected string $name = '';
	
	/**
	 * @var string $title The title tag of the page
	 */
	protected string $title = '';
	
	/**
	 * @var array $meta Meta data of the page
	 */
	protected array $meta = [];
	
	/**
	 * Builds the Template object
	 * @param string $name The template's name
	 * @param string $title The title tag of the page
	 * @param array $meta Meta data of the page
	 * @param App $app The app object
	 */
	public function __construct(string $name, string $title, array $meta, App $app)
	{
		$this->name = $name;
		$this->title = $title;
		$this->meta = $meta;
		$this->app = $app;
	}
	
	public function output()
	{
		$this->app->document->title->set($this->title);
		
		if ($this->meta) {
			foreach ($this->meta as $name => $val) {
				$this->app->document->meta->set($name, $val);
			}
		}
			
		echo $this->app->theme->getTemplate($this->name);
	}
}
