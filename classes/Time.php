<?php
/**
* The Time Class
* @package Mars
*/

namespace Mars;

use DateTime;
use DateTimeZone;
use DateInterval;

/**
* The Time Class
* Time related functions
*/
class Time
{
	use AppTrait;

	/**
	* @var SQL_DATETIME The SQL datetime format
	*/
	public const SQL_DATETIME = 'Y-m-d H:i:s';

	/**
	* @var SQL_DATE The SQL date format
	*/
	public const SQL_DATE = 'Y-m-d';

	/**
	* @var SQL_TIME The SQL time format
	*/
	public const SQL_TIME = 'H:i:s';

	/**
	* @var string $timezone_id The
	*/
	public string $timezone_id = 'UTC';

	/**
	* @var \DateTimeZone $timezone The timezone applied to the datetime computations
	*/
	public DateTimeZone $timezone;

	/**
	* Builds the time object
	* Sets the default timezone to UTC
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		date_default_timezone_set('UTC');

		$this->app = $app;

		$this->timezone = new DateTimeZone($this->timezone_id);
	}

	/**
	* Returns a DateTime object from a datetime
	* @param int|string|DateTime $datetime The datetime
	* @param bool $is_utc If true, will return the date in the UTC timezone
	* @return \DateTime
	*/
	public function get(int|string|DateTime $datetime = 0, bool $is_utc = true) : DateTime
	{
		if (!$datetime instanceof DateTime) {
			if (!$datetime) {
				$datetime = 'now';
			}

			if (is_numeric($datetime)) {
				$datetime = '@' . $datetime;
			}

			$datetime = new DateTime($datetime);
		}

		if (!$is_utc) {
			$datetime->setTimezone($this->timezone);
		}

		return $datetime;
	}

	/**
	* Returns the timestamp from a date/string|timestamp in the UTC timezone
	* @param int|string|DateTime $datetime The datetime
	* @return int The timestamp
	*/
	public function getTimestamp(int|string|DateTime $datetime) : int
	{
		if (!$datetime) {
			return 0;
		}

		return $this->get($datetime)->getTimestamp();
	}

	/**
	* Returns a SQL datetime in the UTC timezone
	* @param int|string|DateTime $datetime The datetime
	* @return string
	*/
	public function getDatetime(int|string|DateTime $datetime) : string
	{
		if (!$datetime) {
			return '0000-00-00 00:00:00';
		}

		return $this->get($datetime)->format(self::SQL_DATETIME);
	}

	/**
	* Returns a SQL date in the UTC timezone
	* @param int|string|DateTime $datetime The datetime
	* @return string
	*/
	public function getDate(int|string|DateTime $datetime) : string
	{
		if (!$datetime) {
			return '0000-00-00';
		}

		return $this->get($datetime)->format(self::SQL_DATE);
	}

	/**
	* Returns a SQL time in the UTC timezone
	* @param int|string|DateTime $datetime The datetime
	* @return string
	*/
	public function getTime(int|string|DateTime $datetime) : string
	{
		if (!$datetime) {
			return '00:00:00';
		}

		return $this->get($datetime)->format(self::SQL_TIME);
	}

	/**
	* Adds to $datetime a certain number of days/months/weeks/years
	* @param int $units The number of time units to add
	* @param string $type The interval type: days/months/weeks/years
	* @param int|string|DateTime $datetime The datetime. If 0, the current time will be used
	* @return DateTime The new date as a timestamp in the UTC timezone
	*/
	public function add(int $units, string $type, int|string|DateTime $datetime = 0) : DateTime
	{
		$interval = $this->getDateInterval($units, $type);

		return $this->get($datetime)->add($interval);
	}

	/**
	* Subtracts from $datetime a certain number of days/months/weeks/years
	* @param int $units The number of time units to  subtract
	* @param string $type The interval type: days/months/weeks/years
	* @param int|string|DateTime $datetime The datetime. If 0, the current time will be used
	* @return DateTime The new date as a timestamp in the UTC timezone
	*/
	public function sub(int $units, string $type, int|string|DateTime $datetime = 0) : DateTime
	{
		$interval = $this->getDateInterval($units, $type);

		return $this->get($datetime)->sub($interval);
	}

	/**
	* Creates a DateInterval object
	* @param int $units The number of time units
	* @param string $type The interval type: days/months/weeks/years
	* @return DateInterval
	*/
	protected function getDateInterval(int $units, string $type) : DateInterval
	{
		$duration = '';
		$type = strtolower(trim($type));

		switch($type) {
			case 'day':
			case 'days':
				$duration = "P{$units}D";
				break;
			case 'week':
			case 'weeks':
				$duration = "P{$units}W";
				break;
			case 'month':
			case 'months':
				$duration = "P{$units}M";
				break;
			case 'year':
			case 'years':
				$duration = "P{$units}Y";
				break;
			default:
				$duration = 'P0D';
		}

		return new DateInterval($duration);
	}

	/**
	* Returns the number of minutes and seconds from $seconds. Eg: for 90 seconds returns 1 min and 30 sec.
	* @param int $seconds The number of seconds
	* @return array Returns an array with the number of minutes & seconds
	*/
	public function getMinutes(int $seconds) : array
	{
		$time = ['minutes' => 0, 'seconds' => 0];
		if (!$seconds) {
			return $time;
		}

		$time['minutes'] = floor($seconds / 60);
		$time['seconds'] = $seconds % 60;

		return $time;
	}
}
