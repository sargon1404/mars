<?php
/**
* The Time Interval Format Class
* @package Mars
*/

namespace Mars\Formats;

use Mars\App;

/**
* The Time Interval Format Class
*/
class TimeInterval
{
	/**
	* @see \Mars\Format::timeInterval()
	*/
	public function format(int|array $seconds, string $separator1 = ' ', string $separator2 = ', ') : string|array
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

		return implode($separator2, $result);
	}
}
