<?php
/**
* The User Interface (UI) Class
* @package Mars
*/

namespace Mars;

/**
* The User Interface (UI) Class
*/
class Ui
{
	use AppTrait;

	/**
	* Builds pagination. The number of pages is computed as $total_items / $items_per_page.
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items that should be displayed on each page
	* @param string $page_param The name of the 'get' param into which the number of the page will be stored
	* @param string $is_seo_url If true will try to replace $seo_page_param from $base_url with the page number rather than append the page number as a param
	* @param string $seo_page_param The string found in $base_url which will be replaced by the page number if $is_seo_url is true
	* @param bool $max_links The max number of pagination links to show
	* @return string The html code of the pagination
	*/
	public function buildPagination(string $base_url, int $total_items, int $items_per_page = 30, string $page_param = 'page', bool $is_seo_url = false, string $seo_page_param = '{PAGE_NO}', int $max_links = 10) : string
	{
		$pag = $this->getPaginationObj();
		$pag->page_param = $page_param;
		$pag->seo_page_param = $seo_page_param;
		$pag->max_links = $max_links;

		$current_page = $this->app->request->get($page_param, 'i');

		return $pag->get($base_url, $current_page, $total_items, $items_per_page, $is_seo_url);
	}

	/**
	* @internal
	*/
	protected function getPaginationObj()
	{
		return new \Ui\Pagination;
	}
}
