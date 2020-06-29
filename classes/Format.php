<?php
/**
* The Format Class
* @package Mars
*/

namespace Mars;

/**
* The Format Class
* Converts values using a certain format
*/
class Format
{
	use AppTrait;

	/**
	* Calls strtolower on $value
	* @param string|array $value The value
	* @return string The formatted value
	*/
	public function strtolower($value) : string
	{
		if (is_array($value)) {
			return array_map([$this, 'strtolower'], $value);
		}

		return strtolower($value);
	}

	/**
	* Calls strtolower on $value
	* @param string|array $value The value
	* @return string The formatted value
	*/
	public function strtoupper($value) : string
	{
		if (is_array($value)) {
			return array_map([$this, 'strtoupper'], $value);
		}

		return strtoupper($value);
	}

	/**
	* Rounds a float
	* @param float $value The value to round
	* @param int $decimals The number of decimals to round to
	* @return float The rounded value
	*/
	public function round(float $value, int $decimals = 2) : float
	{
		return round($value, $decimals);
	}

	/**
	* If $value evaluates to true, returns true. If not, returns $is_empty
	* @param mixed $value The value to check
	* @param mixed $is_empty The value which will be returned if $value is false
	* @return mixed
	*/
	public function empty($value, $is_empty = '')
	{
		if (!$value) {
			return $is_empty;
		}

		return $value;
	}

	/**
	* Returns $url if not empty, javascript:void(0) otherwise
	* @param string $url The url
	* @return string
	*/
	public function toUrl(string $url) : string
	{
		if ($url) {
			return $url;
		}

		return 'javascript:void(0)';
	}

	/**
	* Format a number with grouped thousands
	* @param float $number The number being formatted
	* @param int $decimals The number of decimal points
	* @param string $dec_point The separator for the decimal point
	* @param string $thousands_sep The thousands separator
	* @return string The formatted number
	*/
	public function number(float $number, int $decimals = 0, string $dec_point = '.', string $thousands_sep = ',') : string
	{
		return number_format($number, $decimals, $dec_point, $thousands_sep);
	}

	/**
	* Formats a filesize. It returns the result in gb, mb or kb depending on the $kb parameter
	* @param int $kb The filesize - in kb - to be converted.
	* @param int $digits The number of digits to return to the result if it's MBs.
	* @param string $gb_str The string to use for gigabytes
	* @param string $mb_str The string to use for megabytes
	* @param string $kb_str The string to use for kilobytes
	* @return string The formatted filesize
	*/
	public function size(int $kb, int $digits = 2, string $gb_str = 'GB', string $mb_str = 'MB', string $kb_str = 'KB') : string
	{
		$gb_limit = 1024 * 768;

		if ($kb > $gb_limit) {
			return round($kb / 1024 / 1024, $digits) . ' ' . $gb_str;
		} else {
			$kb_limit = 768;

			if ($kb > $kb_limit) {
				return round($kb / 1024, $digits) . ' ' . $mb_str;
			} else {
				return round($kb, $digits) . ' ' . $kb_str;
			}
		}
	}

	/**
	* Returns the percentage of $number from $total
	* @param int $number The number
	* @param int $total The total
	* @param int $digits The number of digits to include
	* @return string The percentage
	*/
	public function percentage(float $number, float $total, int $digits = 4) : float
	{
		if (!$number || !$total) {
			return 0;
		}

		$result = ($number * 100) / $total;

		return round($result, $digits);
	}

	/**
	* Formats a timestamp
	* @param int $timestamp The timestamp used.
	* @param string $format The format in which the date will be formatted.identical with the one used with date(). By default 'D M d, Y g:i a' will be used
	* @return string The formatted value
	*/
	public function timestamp(int $timestamp = 0, string $format = 'D M d, Y g:i a') : string
	{
		if (!$timestamp) {
			return '';
		}

		$result = date($format, $timestamp);

		return $result;
	}

	/**
	* Alias of timestamp
	* @param int $timestamp The timestamp used.
	* @param string $format The format in which the date will be formatted.identical with the one used with date(). By default 'D M d, Y g:i a' will be used
	* @return string The formatted value
	*/
	public function datetime(int $timestamp = 0, string $format = 'D M d, Y g:i a') : string
	{
		return $this->timestamp($timestamp, $format);
	}

	/**
	* Formats a date from a timestamp
	* @param int $timestamp The timestamp used.
	* @return string The time
	*/
	public function date(int $timestamp = 0) : string
	{
		return $this->timestamp($timestamp, 'D M d, Y');
	}

	/**
	* Formats the time from a timestamp
	* @param int $timestamp The timestamp used.
	* @param bool $replace_strings If true,will replace the english strings with the current language's strings
	* @return string The time
	*/
	public function time(int $timestamp = 0, bool $replace_strings = true) : string
	{
		return $this->timestamp($timestamp, 'g:i a');
	}

	/**
	* Formats a time interval. It returns the number of weeks,days,hours,minutes,seconds it contains. Eg: 90 = 1 minute,30 seconds
	* @param int $seconds The number of seconds
	* @param array $myinterval It copies here the numeric values. Output value
	* @param string $separator1 The separator between the numeric value and the word. Eg: separator = : the result will be 2:weeks etc..
	* @param string $separator2 The separator from the end of a value. Eg:separator = , result= 2weeks,3days..
	* @return string The formatted value
	*/
	public function timeInterval(int $seconds, ?array &$myinterval = [], string $separator1 = ' ', string $separator2 = ', ') : string
	{
		if (!$seconds || $seconds < 0) {
			return '0 ' . App::__('second');
		}

		$interval = ['seconds' => 0, 'minutes' => 0, 'hours' => 0, 'days' => 0, 'weeks' => 0];

		if ($seconds < 60) {
			$interval['seconds'] = $seconds;
		} else {
			///compute the minutes
			$interval['minutes'] = floor($seconds / 60);
			$interval['seconds'] = $seconds % 60;
			if ($interval['minutes'] > 60) {
				///compute the hours
				$interval['hours'] = floor($interval['minutes'] / 60);
				$interval['minutes'] = $interval['minutes'] % 60;
				if ($interval['hours'] > 24) {
					///compute the days
					$interval['days'] = floor($interval['hours'] / 24);
					$interval['hours'] = $interval['hours'] % 24;
					if ($interval['days'] > 7) {
						///compute the weeks
						$interval['weeks'] = floor($interval['days'] / 7);
						$interval['days'] = $interval['days'] % 7;
					}
				}
			}
		}

		$result = [];
		$myinterval = $interval;

		if ($interval['weeks']) {
			if ($interval['weeks'] == 1) {
				$result[] = $interval['weeks'] . $separator1 . App::__('week');
			} else {
				$result[] = $interval['weeks'] . $separator1 . App::__('weeks');
			}
		}
		if ($interval['days']) {
			if ($interval['days'] == 1) {
				$result[] = $interval['days'] . $separator1 . App::__('day');
			} else {
				$result[] = $interval['days'] . $separator1 . App::__('days');
			}
		}
		if ($interval['hours']) {
			if ($interval['hours'] == 1) {
				$result[] = $interval['hours'] . $separator1 . App::__('hour');
			} else {
				$result[] = $interval['hours'] . $separator1 . App::__('hours');
			}
		}
		if ($interval['minutes']) {
			if ($interval['minutes'] == 1) {
				$result[] = $interval['minutes'] . $separator1 . App::__('minute');
			} else {
				$result[] = $interval['minutes'] . $separator1 . App::__('minutes');
			}
		}
		if ($interval['seconds']) {
			if ($interval['seconds'] == 1) {
				$result[] = $interval['seconds'] . $separator1 . App::__('second');
			} else {
				$result[] = $interval['seconds'] . $separator1 . App::__('seconds');
			}
		}

		$result = implode($separator2, $result);

		return $result;
	}
}
