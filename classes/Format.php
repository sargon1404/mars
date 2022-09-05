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
	* @var string $datetime_format The datetime format
	*/
	protected string $datetime_format = 'D M d, Y h:i:s';

	/**
	* @var string $date_format The date format
	*/
	protected string $date_format = 'D M d, Y';

	/**
	* @var string $time_format The time format
	*/
	protected string $time_format = 'h:i:s';

	/**
	* @var Handlers $handlers The handlers object
	*/
	public readonly Handlers $handlers;

	/**
	* @var array $supported_handlers The list of supported_handlers
	*/
	protected array $supported_handlers = [
		'lower' => ['lower'],
		'upper' => ['upper'],
		'round' => ['round'],
		'number' => ['number'],
		'datetime' => ['datetime'],
		'date' => ['date'],
		'time' => ['time'],
		'percentage' => '\Mars\Formats\Percentage',
		'filesize' => '\Mars\Formats\Filesize',
		'time_interval' => '\Mars\Formats\TimeInterval',
	];

	/**
	* Builds the text object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->handlers = new Handlers($this->supported_handlers);
	}

	/**
	* Converts a value to lowercase
	* @param string|array $value The value
	* @return string|array The formatted value
	*/
	public function lower(string|array $value) : string|array
	{
		return $this->handlers->map($value, function ($value) {
			return strtolower($value);
		});
	}

	/**
	* Converts a value to lowercase
	* @param string|array $value The value
	* @return string|array The formatted value
	*/
	public function upper(string|array $value) : string|array
	{
		return $this->handlers->map($value, function ($value) {
			return strtoupper($value);
		});
	}

	/**
	* Rounds a float
	* @param float|array $value The value to round
	* @param int $decimals The number of decimals to round to
	* @return float The rounded value
	*/
	public function round(float|array $value, int $decimals = 2) : float|array
	{
		return $this->handlers->map($value, function ($value) use ($decimals) {
			return round($value, $decimals);
		});
	}

	/**
	* Format a number with grouped thousands
	* @param float|array $number The number being formatted
	* @param int $decimals The number of decimal points
	* @param string $decimal_separator The separator for the decimal point
	* @param string $thousands_separator The thousands separator
	* @return string The formatted number
	*/
	public function number(float|array $number, int $decimals = 2, string $decimal_separator = '.', string $thousands_separator = ',') : string|array
	{
		return $this->handlers->map($number, function ($number) use ($decimals, $decimal_separator, $thousands_separator) {
			return number_format($number, $decimals, $decimal_separator, $thousands_separator);
		});
	}

	/**
	* Returns the percentage of $number from $total
	* @param float|array $number The number
	* @param float $total The total
	* @param int $decimals The number of decimal points
	* @return string The percentage
	*/
	public function percentage(float|array $number, float $total, int $decimals = 4) : float|array
	{
		return $this->handlers->getMultiValue($number, 'percentage', $total, $decimals);
	}

	/**
	* Formats a filesize. It returns the result in gb, mb or kb depending on the $kb parameter
	* @param int $bytes The filesize - in bytes - to be converted.
	* @param int $digits The number of digits to return to the result if it's MBs.
	* @return string The formatted filesize
	*/
	public function filesize(int|array $bytes, int $digits = 2) : string|array
	{
		return $this->handlers->getMultiValue($bytes, 'filesize', $digits);
	}

	/**
	* Formats a datetime
	* @param int|string|DateTime $datetime The datetime
	* @param string $format The format in which the datetime will be formatted
	* @return string The formatted value
	*/
	public function datetime(int|string|DateTime|array $datetime = 0, string $format = '') : string|array
	{
		$format = $format ?: $this->datetime_format;

		return $this->handlers->map($datetime, function ($datetime) use ($format) {
			return $this->app->time->get($datetime)->format($format);
		});
	}

	/**
	* Formats a date
	* @param int|string|DateTime $datetime The datetime
	* @param string $format The format in which the date will be formatted
	* @return string The formatted value
	*/
	public function date(int|string|DateTime|array $datetime = 0, string $format = '') : string|array
	{
		$format = $format ?: $this->date_format;

		return $this->datetime($datetime, $format);
	}

	/**
	* Formats time
	* @param int|string|DateTime $datetime The datetime
	* @param string $format The format in which the time will be formatted
	* @return string The formatted value
	*/
	public function time(int|string|DateTime|array $datetime = 0, string $format = '') : string|array
	{
		$format = $format ?: $this->time_format;

		return $this->datetime($datetime, $format);
	}

	/**
	* Formats a time interval. It returns the number of weeks,days,hours,minutes,seconds it contains. Eg: 90 = 1 minute,30 seconds
	* @param int $seconds The number of seconds
	* @param string $separator1 The separator between the numeric value and the word. Eg: separator = : the result will be 2:weeks etc..
	* @param string $separator2 The separator from the end of a value. Eg:separator = , result= 2weeks,3days..
	* @return string The formatted value
	*/
	public function timeInterval(int|array $seconds, string $separator1 = ' ', string $separator2 = ', ') : string|array
	{
		return $this->handlers->getMultiValue($seconds, 'time_interval', $separator1, $separator2);
	}
}
