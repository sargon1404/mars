<?php
/**
* The Pagination Class
* @package Mars
*/

namespace Mars\Ui;

/**
* The Pagination Class
* Generates pagination links
*/
class Pagination
{
	use \Mars\AppTrait;

	/**
	* @var int $max_links The max number of pagination links to show
	*/
	public int $max_links = 10;

	/**
	* @var string $page_param The name of the query param where the page number will be appended
	*/
	public string $page_param = 'page';

	/**
	* @var string $seo_page_param The string found in $base_url which will be replaced by the page number, if $is_seo_url is true
	*/
	public string $seo_page_param = '{PAGE_NO}';

	/**
	* Builds the pagination template. The number of pages is computed as $total_items / $items_per_page.
	* @param string $base_url The generic base_url where the number of the page will be appended
	* @param int $current_page The current page
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items per page
	* @param string $is_seo_url If true will try to replace $seo_page_param from $base_url with the page number rather than append the page number as a param
	* @return string The html code
	*/
	public function get(string $base_url, int $current_page, int $total_items, int $items_per_page = 30, bool $is_seo_url = false) : string
	{
		if (!$total_items) {
			return '';
		}

		$pages_count = $this->getPagesCount($total_items, $items_per_page);
		if ($pages_count == 1) {
			return '';
		}

		$url_extra = [];
		$current_page = $this->getCurrentPage($current_page, $pages_count);
		$replace_seo_page = $this->canReplaceSeoParam($base_url, $is_seo_url);
		$data = $this->getLimits($current_page, $pages_count);

		$start = $data['start'];
		$end = $data['end'];

		$pages = $this->getPages($base_url, $start, $end, $current_page, $replace_seo_page, $url_extra);
		$previous = $this->getPreviousLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$next = $this->getNextLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$first = $this->getFirstLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$last = $this->getLastLink($base_url, $current_page, $pages_count, $replace_seo_page, $url_extra);
		$jump = $this->getJumpToLink($base_url, $current_page, $pages_count, $is_seo_url, $url_extra);

		$vars = [
			'current_page' => $current_page,
			'total_items' => $total_items,
			'items_per_page' => $items_per_page,
			'start' => $start,
			'end' => $end,
			'previous' => $previous,
			'next' => $next,
			'first' => $first,
			'last' => $last,
			'jump' => $jump,
			'pages_count' => $pages_count,
			'pages' => $pages
		];

		return $this->getTemplate($vars);
	}

	/**
	* Returns the number of pages
	* @param int $total_items The total numbers of items
	* @param int $items_per_page The number of items per page
	* @return int The number of pages
	*/
	protected function getPagesCount(int $total_items, int $items_per_page) : int
	{
		$pages_count = ceil($total_items / $items_per_page);

		return $pages_count;
	}

	/**
	* Returns the current page
	* @param int $current_page The current page
	* @param int $pages_count The no. of pages
	* @return int
	*/
	protected function getCurrentPage(int $current_page, int $pages_count) : int
	{
		if ($current_page < 0 || $current_page > $pages_count) {
			$current_page = 1;
		}

		return $current_page;
	}

	/**
	* Returns true if the base url contains the page seo param
	* @param string $base_url The url
	* @param bool $is_seo_url True if the page param must be replaced
	* @return bool
	*/
	protected function canReplaceSeoParam(string $base_url, bool $is_seo_url) : bool
	{
		$replace_seo_page = false;

		if ($is_seo_url) {
			if (str_contains($base_url, $this->seo_page_param)) {
				$replace_seo_page = true;
			}
		}

		return $replace_seo_page;
	}

	/**
	* Builds the url, by appending the page param
	* @param string $base_url The url
	* @param int $page The page number
	* @param bool $replace_seo_page If true, will replace in the url the seo page param
	* @param array url_extra Extra url params
	* @return string The url
	*/
	protected function getUrl(string $base_url, int $page, bool $replace_seo_page = false, array $url_extra = []) : string
	{
		$url = '';
		if (!$replace_seo_page) { //build the url, by appending the page as a query string
			$url = $this->app->uri->build($base_url, [$this->page_param => $page]);
		} else { //replace the seo page param with the page number
			$url = str_replace($this->seo_page_param, $page, $base_url);
		}

		return $url;
	}

	/**
	* Returns the contents of the pagination template
	* @param array $vars Vars to be passed to the template
	* @return string
	*/
	protected function getTemplate(array $vars) : string
	{
		return $this->app->theme->getTemplate('pagination', ['pagination' => $vars]);
	}

	/**
	* Determines the pages interval which should be displayed/are visible
	* @param int $current_page The current page
	* @param int $pages_count The no. of pages
	* @return array The start & end pages
	*/
	protected function getLimits(int $current_page, int $pages_count) : array
	{
		$max_links = $this->max_links;

		$start = 1;
		$end = 1;

		if ($max_links && $max_links < $pages_count) {
			$exlinks = floor($max_links / 2);
			$start = $current_page - $exlinks;
			$end = $current_page + $exlinks;

			if (!($max_links % 2)) {
				$start++;
			}
			if ($start <= 0) {
				$start = 1;
				$end = $max_links;
			} elseif ($end > $pages_count) {
				$end = $pages_count;
				$start = $end - $max_links + 1;
			}
		} else {
			$start = 1;
			$end = $pages_count;
		}

		return ['start' => $start, 'end' => $end];
	}

	/**
	* Returns the paginator's pages
	* @param string $base_url The url
	* @param int $start The start page
	* @param int $end The end page
	* @param int $current_page The current page
	* @param bool $replace_seo_page If true, will replace in the url the seo page param
	* @param array url_extra Extra url params
	* @return array The pages
	*/
	protected function getPages(string $base_url, int $start, int $end, int $current_page, bool $replace_seo_page = false, array $url_extra = []) : array
	{
		$pages = [];

		for ($i = $start; $i <= $end; $i++) {
			$class = '';
			if ($i == $current_page) {
				$class = 'current';
			}

			$url = $this->getUrl($base_url, $i, $replace_seo_page, $url_extra);

			$pages[] = ['url' => App::e($url), 'page' => $i, 'class' => $class];
		}

		return $pages;
	}

	/**
	* Returns the data for the first link
	* @internal
	*/
	protected function getFirstLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		$max_links = $this->max_links;

		if (!$max_links || $max_links >= $pages_count) {
			return ['show' => false, 'url' => '', 'page' => ''];
		}

		$i = 1;
		$url = $this->getUrl($base_url, $i, $replace_seo_page, $url_extra);

		return ['show' => true, 'url' => $url, 'page' => $i];
	}

	/**
	* Returns the data for the last link
	* @internal
	*/
	protected function getLastLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		$max_links = $this->max_links;

		if (!$max_links || $current_page == $pages_count || $max_links >= $pages_count) {
			return ['show' => false, 'url' => '', 'page' => ''];
		}

		$i = $pages_count;
		$url = $this->getUrl($base_url, $i, $replace_seo_page, $url_extra);

		return ['show' => true, 'url' => $url, 'page' => $i];
	}

	/**
	* Returns the data for the previous link
	* @internal
	*/
	protected function getPreviousLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		if ($current_page <= 1) {
			return ['show' => false, 'url' => '', 'page' => ''];
		}

		$i = $current_page - 1;
		$url = $this->getUrl($base_url, $i, $replace_seo_page, $url_extra);

		return ['show' => true, 'url' => $url, 'page' => $i];
	}

	/**
	* Returns the data for the next link
	* @internal
	*/
	protected function getNextLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		if ($current_page >= $pages_count) {
			return ['show' => false, 'url' => '', 'page' => ''];
		}

		$i = $current_page + 1;
		$url = $this->getUrl($base_url, $i, $replace_seo_page, $url_extra);

		return ['show' => true, 'url' => $url, 'page' => $i];
	}

	/**
	* Returns the data for jump to link
	* @internal
	*/
	protected function getJumpToLink(string $base_url, int $current_page, int $pages_count, bool $replace_seo_page, array $url_extra = []) : array
	{
		return ['show' => false, 'form' => ''];
	}
}
