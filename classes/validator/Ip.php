<?php
/**
* The Ip Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Ip Validator Class
*/
class Ip extends Base
{
	/**
	* @see \Mars\Validator\Base::validate()
	* {@inheritDocs}
	*/
	public function validate(string $value, $wildcards) : bool
	{
		if (!$wildcards) {
			return filter_var($value, FILTER_VALIDATE_IP);
		}

		//replace colons with dots if it's an IPv6 address
		$value = str_replace(':', '.', strtolower($value);
		$segments = explode('.', $value);
		$segments_count = count($segments);

		if (!$segments_count) {
			return false;
		}
		if ($segments_count != 4 && $segments_count != 8) {
			return false;
		}

		$regexp = '';
		$max_size = 3;
		if ($segments_count == 8) {
			$regexp = '/[a-f0-9]{1,4}/';
			if ($wildcards) {
				$regexp = '/[a-f0-9\*]{1,4}/';
			}
			$max_size = 4;
		} else {
			$regexp = '/[a-f0-9]{1,3}/';
			if ($wildcards) {
				$regexp = '/[a-f0-9\*]{1,3}/';
			}
		}

		foreach ($segments as $segment) {
			if (strlen($segment) > $max_size) {
				return false;
			}
			if (!preg_match($regexp, $segment, $m)) {
				return false;
			}
		}

		return true;
	}

}