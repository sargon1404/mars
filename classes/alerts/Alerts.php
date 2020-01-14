<?php
/**
* The Alerts Class
* @package Mars
*/

namespace Mars\Alerts;

/**
* The Alerts Class
* Container for alerts
*
*/
abstract class Alerts
{
	/**
	* @var array $alerts Array with all the generated alerts
	*/
	protected array $alerts = [];

	/**
	* Returns the count of generated alerts
	* @return int
	*/
	public function count() : int
	{
		return count($this->alerts);
	}

	/**
	* Returns the generated alerts
	* @return array The alerts
	*/
	public function get() : array
	{
		return $this->alerts;
	}

	/**
	* Returns the first generated alert
	* @param bool $only_text If true, will return only the text rather than the object
	* @return string The alert
	*/
	public function getFirst(bool $only_text = false)
	{
		if (!$this->alerts) {
			return '';
		}

		if ($only_text) {
			return reset($this->alerts)->getText();
		} else {
			return reset($this->alerts);
		}
	}

	/**
	* Adds an alert to the alerts list.
	* @param mixed $alert The alert text. String or array
	* @param bool $escape_html If true will html escape $alert
	* @return $this
	*/
	public function add($alert, bool $escape_html = true)
	{
		$alerts = [];

		if (is_array($alert)) {
			$alerts = $alert;
		} else {
			$alerts = [$alert];
		}

		foreach ($alerts as $str) {
			$this->alerts[] = new Alert($alert, '', $escape_html, true);
		}

		return $this;
	}

	/**
	* Deletes the currently generated errors
	* @return $this
	*/
	public function clear()
	{
		$this->alerts = [];

		return $this;
	}
}
