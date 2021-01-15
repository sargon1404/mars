<?php
/**
* The Time Class
* @package Mars
*/

namespace Mars;

/**
* The Time Class
* Time related functions
*/
class Time
{
	use AppTrait;

	/**
	* Returns the current time as a timestamp
	* @return int
	*/
	public function get() : int
	{
		return time();
	}

	/**
	* Returns the timestamp from a date/string|timestamp
	* @param int|string $date Either a timestamp (int) or a date/datetime, if string
	* @return int The timestamp
	*/
	public function getTimestamp(int|string $date) : int
	{
		if (!$date) {
			return 0;
		}

		if (is_numeric($date) && (int)$date == $date) {
			return $date;
		} elseif (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $date, $m)) {
			return mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
		} elseif (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date, $m)) {
			return mktime(0, 0, 0, $m[2], $m[3], $m[1]);
		} else {
			return strtotime($date);
		}
	}

	/**
	* Converts a timestamp to a sql datetime representation
	* @param int|string $timestamp The timestamp
	* @param bool $date If true, will return the date part
	* @param bool $time If true, will return the time part
	* @return string The sql datetime
	*/
	public function getSql(int|string $timestamp, bool $date = true, bool $time = true) : string
	{
		$timestamp = $this->getTimestamp($timestamp);

		$date_part = '';
		$time_part = '';

		if ($timestamp) {
			$ct = getdate($timestamp);

			$date_part = $ct['year'] . '-' . App::padInt($ct['mon']) . '-' . App::padInt($ct['mday']);
			$time_part = App::padInt($ct['hours']) . ':' . App::padInt($ct['minutes']) . ':' . App::padInt($ct['seconds']);
		}

		if ($date_part && $time_part) {
			if ($date && $time) {
				return $date_part . ' ' . $time_part;
			} elseif ($date) {
				return $date_part;
			} elseif ($time) {
				return $time_part;
			}
		}

		return '0000-00-00 00:00:00';
	}
	
	/**
	* Converts a timestamp to an ISO 8601 datetime representation
	* @param int|string $timestamp The timestamp
	* @param bool $date If true, will return the date part
	* @param bool $time If true, will return the time part
	* @return string The datetime
	*/
	public function getISO(int|string $timestamp, bool $date = true, bool $time = true) : string
	{
		return $this->getSql($timestamp, $date, $time);
	}

	/**
	* Adjusts $timestamp from UTC to user's timezone
	* @param int $timestamp The timestamp
	* @param int $timezone_offset The timezone offset - in hours - between user's timezone and UTC
	* @return int The adjusted timestamp,from UTC to user's timezone
	*/
	public function adjust(int $timestamp, int $timezone_offset = 0) : int
	{
		if (!$timestamp || !$timezone_offset) {
			return $timestamp;
		}

		return $timestamp + ($timezone_offset * 3600);
	}

	/**
	* Adjusts $timestamp from the user's timezone to UTC
	* @param int $timestamp The timestamp
	* @param int $timezone_offset The timezone offset - in hours - between user's timezone and UTC
	* @return int The adjusted timestamp,from user's timezone to UTC
	*/
	public function adjustUtc(int $timestamp, int $timezone_offset = 0) : int
	{
		if (!$timestamp || !$timezone_offset) {
			return $timestamp;
		}

		return $timestamp - ($timezone_offset * 3600);
	}

	/**
	* Returns the timezone offset -in hours- between $timezone and utc
	* @param string $timezone The timezone
	* @return int The offset
	*/
	public function getTimezoneOffset(string $timezone) : int
	{
		$dtz = new \DateTimeZone($timezone);
		$dt = new \DateTime('now', $dtz);

		return $dt->getOffset() / 3600;
	}

	/**
	* Adds 1 day to $timestamp.
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp + 1 day
	*/
	public function addDay(int $timestamp = 0) : int
	{
		return $this->addDays(1, $timestamp);
	}

	/**
	* Adds to $timestamp a certain number of days.
	* @param int $days The number of days to add
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp + $days days
	*/
	public function addDays(int $days, int $timestamp = 0) : int
	{
		if (!$timestamp) {
			$timestamp = time();
		}

		$do = new \DateTime();
		$do->setTimestamp($timestamp);
		$di = new \DateInterval("P{$days}D");

		$res = $do->add($di);

		return $res->getTimestamp();
	}

	/**
	* Adds 1 month to $timestamp
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp + 1 month
	*/
	public function addMonth(int $timestamp = 0) : int
	{
		return $this->addMonths(1, $timestamp);
	}

	/**
	* Adds to $timestamp a certain number of months
	* @param int $months The number of months to add
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp + $months months
	*/
	public function addMonths(int $months, int $timestamp = 0) : int
	{
		if (!$timestamp) {
			$timestamp = time();
		}

		$do = new \DateTime();
		$do->setTimestamp($timestamp);
		$di = new \DateInterval("P{$months}M");

		$res = $do->add($di);

		return $res->getTimestamp();
	}

	/**
	* Subtracts 1 day from $timestamp.
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp - 1 day
	*/
	public function subDay(int $timestamp = 0) : int
	{
		return $this->subDays(1, $timestamp);
	}

	/**
	* Subtracts from $timestamp a certain number of days.
	* @param int $days The number of days to subtract
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp - $days days
	*/
	public function subDays(int $days, int $timestamp = 0) : int
	{
		if (!$timestamp) {
			$timestamp = time();
		}

		$do = new \DateTime();
		$do->setTimestamp($timestamp);
		$di = new \DateInterval("P{$days}D");

		$res = $do->sub($di);

		return $res->getTimestamp();
	}

	/**
	* Subtracts 1 month to $timestamp
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp - 1 month
	*/
	public function subMonth(int $timestamp = 0) : int
	{
		return $this->subMonths(1, $timestamp);
	}

	/**
	* Subtracts from $timestamp a certain number of months
	* @param int $months The number of months to sub
	* @param int $timestamp The timestamp. If $timestamp is 0, the current timestamp will be used
	* @return int timestamp - $months months
	*/
	public function subMonths(int $months, int $timestamp = 0) : int
	{
		if (!$timestamp) {
			$timestamp = time();
		}

		$do = new \DateTime();
		$do->setTimestamp($timestamp);
		$di = new \DateInterval("P{$months}M");

		$res = $do->sub($di);

		return $res->getTimestamp();
	}

	/**
	* Returns the number of minutes and seconds from $seconds. Eg: for 90 seconds returns 1 min and 30 sec.
	* @param int $seconds The number of seconds
	* @return array Returns an array with the number of minutes & seconds
	*/
	public function getMinutes(int $seconds) : array
	{
		$time = [];
		$time['minutes'] = 0;
		$time['seconds'] = 0;
		if (!$seconds) {
			return $time;
		}

		$time['minutes'] = floor($seconds / 60);
		$time['seconds'] = $seconds % 60;

		return $time;
	}
}
