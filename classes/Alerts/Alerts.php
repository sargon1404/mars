<?php
/**
* The Alerts Class
* @package Mars
*/

namespace Mars\Alerts;

use Mars\App;

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
	* @return string The alert
	*/
	public function getFirst()
	{
		if (!$this->alerts) {
			return '';
		}

		return reset($this->alerts);
	}

	/**
	* Adds an alert or multiple alerts to the alerts list.
	* @param string|array|Alerts $alert The alert text
	* @return static
	*/
	public function add(string|array|Alerts $alert) : static
	{
		if ($alert instanceof Alerts) {
			$alerts = $alert->get();
		} else {
			$alerts = (array)$alert;
		}

		foreach ($alerts as $text) {
			$this->alerts[] = $text;
		}

		return $this;
	}

	/**
	* Resets the current alerts then adds the new alerts
	* @param string|array $alert The alert text
	* @return static
	*/
	public function set(string|array $alert) : static
	{
		return $this->reset()->add($alert);
	}

	/**
	* Deletes the currently generated errors
	* @return static
	*/
	public function reset() : static
	{
		$this->alerts = [];

		return $this;
	}
}
