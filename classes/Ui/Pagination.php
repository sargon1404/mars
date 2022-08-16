<?php
/**
* The Pagination Class
* @package Mars
*/

namespace Mars\Ui;

use Mars\App;

/**
* The Pagination Class
* Generates pagination links
*/
class Pagination
{
	use \Mars\AppTrait;

	/**
	* @var string $base_url The generic base_url where the number of the page will be appended
	*/
	protected string $base_url = '';

	/**
	* @var int $current_paget The current page
	*/
	protected int $current_page = 0;

	/**
	* @var int $total_pages The total number of pages
	*/
	protected int $total_pages = 0;

	/**
	* @var int $total_items The total number of items
	*/
	protected int $total_items = 0;

	/**
	* @var int $pagination_items_per_page The number of items that should be displayed on each page
	*/
	protected int $items_per_page = 0;

	/**
	* @var int $max_links The max number of pagination links to show
	*/
	protected int $max_links = 0;

	/**
	* @var bool $use_seo_param If true, will use the seo param in the base url rather than append the page as a query param
	*/
	protected bool $use_seo_param = false;

	/**
	* @var string $seo_param The string found in $base_url which will be replaced by the page number
	*/
	protected string $seo_param = '{PAGE_NO}';

	/**
	* Builds the pagination object
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param int $items_per_page The number of items per page
	* @param int $total_items The total numbers of items
	* @param int $max_links The max number of links to show
	* @param App $app The app object
	*/
	public function __construct(string $base_url, int $items_per_page, int $total_items, int $max_links, App $app)
	{
		$this->app = $app;

		$this->base_url = $base_url;
		$this->items_per_page = $items_per_page;
		$this->total_items = $total_items;
		$this->max_links = $max_links;
		$this->total_pages = $this->getTotalPages();
		$this->current_page = $this->getCurrentPage();
		$this->use_seo_param = $this->canUseSeoParam();
	}

	/**
	* Builds the pagination template. The number of pages is computed as $total_items / $items_per_page.
	* @return string The html code
	*/
	public function get() : string
	{
		if (!$this->total_items || $this->items_per_page > $this->total_items) {
			return '';
		}

		$links = $this->getLinks();

		$links = $this->app->plugins->filter('ui_pagination_get', $links, $this);

		return $this->getHtml($links);
	}

	/**
	* Returns the pagination html code
	* @return string
	*/
	protected function getHtml(array $links) : string
	{
		$html = '<div class="pagination">' . "\n";
		foreach ($links as $link) {
			if (!$link['show']) {
				continue;
			}

			$html.= $this->app->html->a($link['url'], $link['title'], ['class' => $link['class']]) . "\n";
		}

		$html.= '</div' . "\n";

		return $html;
	}

	/**
	* Returns the pagination links
	* @param int $start The start page
	* @param int $end The end page
	* @return array
	*/
	public function getLinks() : array
	{
		[$start, $end] = $this->getLimits();

		$links = [];
		$links['first'] = $this->getFirstLink();
		$links['previous'] = $this->getPreviousLink();

		for ($i = $start; $i <= $end; $i++) {
			$class = ($i == $this->current_page) ? 'pagination-current' : '';

			$links['page-' . $i] = $this->getLink($i, $i, true, $class);
		}

		$links['next'] = $this->getNextLink();
		$links['last'] = $this->getLastLink();

		return $links;
	}

	/**
	* Returns the total number of pages
	* @return int
	*/
	protected function getTotalPages() : int
	{
		return ceil($this->total_items / $this->items_per_page);
	}

	/**
	* Returns the current page
	* @return int
	*/
	protected function getCurrentPage() : int
	{
		$current_page = $this->app->request->getPage();
		if ($current_page <= 0 || $current_page > $this->total_pages) {
			return 1;
		}

		return $current_page;
	}

	/**
	* Returns true if the base url contains the page seo param
	* @return bool
	*/
	protected function canUseSeoParam() : bool
	{
		return str_contains($this->base_url, $this->seo_param);
	}

	/**
	* Determines the pages interval which should be displayed/are visible
	* @return array The start & end pages
	*/
	protected function getLimits() : array
	{
		$start = 1;
		$end = 1;

		if ($this->max_links && $this->max_links < $this->total_pages) {
			$exlinks = floor($this->max_links / 2);
			$start = $this->current_page - $exlinks;
			$end = $this->current_page + $exlinks;

			if (!($this->max_links % 2)) {
				$start++;
			}
			if ($start <= 0) {
				$start = 1;
				$end = $this->max_links;
			} elseif ($end > $this->total_pages) {
				$end = $this->total_pages;
				$start = $end - $this->max_links + 1;
			}
		} else {
			$start = 1;
			$end = $this->total_pages;
		}

		return [$start, $end];
	}

	/**
	* Builds the url, by appending the page param
	* @param int $page The page number
	* @return string The url
	*/
	protected function getUrl(int $page) : string
	{
		if ($this->use_seo_param) {
			//replace the seo page param with the page number
			return str_replace($this->seo_param, $page, $this->base_url);
		} else {
			//build the url, by appending the page as a query string
			return $this->app->uri->build($this->base_url, [$this->app->config->request_page_param => $page]);
		}
	}

	/**
	* Returns the link array
	* @param int $page The page number
	* @param string $title The link's title
	* @param bool $show If false, the link shouldn't be visible
	* @param string $class The class of the link, if any
	* @return array
	*/
	protected function getLink(int $page, string $title, bool $show = true, string $class = '') : array
	{
		return ['show' => $show, 'url' => $this->getUrl($page), 'title' => $title, 'page' => $page, 'class' => $class];
	}

	/**
	* Returns the data for the first link
	* @return array
	*/
	protected function getFirstLink() : array
	{
		$show = false;
		if ($this->current_page > 1) {
			$show = true;
		}

		return $this->getLink(1, App::__('pagination_first'), $show, 'pagination-first');
	}

	/**
	* Returns the data for the last link
	* @return array
	*/
	protected function getLastLink() : array
	{
		$show = false;
		if ($this->current_page != $this->total_pages && $this->total_pages > $this->max_links) {
			$show = true;
		}

		return $this->getLink($this->total_pages, App::__('pagination_last'), $show, 'pagination-last');
	}

	/**
	* Returns the data for the previous link
	* @internal
	*/
	protected function getPreviousLink() : array
	{
		$show = false;
		if ($this->current_page > 1) {
			$show = true;
		}

		return $this->getLink($this->current_page - 1, App::__('pagination_previous'), $show, 'pagination-previous');
	}

	/**
	* Returns the data for the next link
	* @internal
	*/
	protected function getNextLink() : array
	{
		$show = false;
		if ($this->current_page != $this->total_pages) {
			$show = true;
		}

		return $this->getLink($this->current_page + 1, App::__('pagination_next'), $show, 'pagination-next');
	}
}
