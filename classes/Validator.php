<?php
/**
* The Validator Class
* @package Mars
*/

namespace Mars;

/**
* The Validator Class
* Validates values
*/
class Validator
{
	use AppTrait;

	/**
	* @var array $errors The list of validation errors, if any
	*/
	protected array $errors = [];

	/**
	* @var array $supported_rules The list of suported rules
	*/
	protected array $supported_rules = [
		'required' => '\Mars\Validator\Required',
		'unique' => '\Mars\Validator\Unique',
		'min' => '\Mars\Validator\Min',
		'pattern' => '\Mars\Validator\Pattern',
		'email' => '\Mars\Validator\Email',
		'url' => '\Mars\Validator\Url',
		'ip' => '\Mars\Validator\Ip',
	];

	/**
	* Adds a supported validation rule
	* @param string $name The name of the rule
	* @param string $class The class which will handle it
	* @return $this
	*/
	public function addSupportedRule(string $name, string $class)
	{
		$this->supported_rules[$name] = $class;

		return $this;
	}

	/**
	* Deletes a supported validation rule
	* @param string $name The name of the rule
	* @return $this
	*/
	public function deleteSupportedRule(string $name)
	{
		unset($this->supported_rules[$name]);

		return $this;
	}

	/**
	* Returns the validation errors, if any
	* @return array The errors
	*/
	public function getErrors() : array
	{
		return $this->errors;
	}

	/**
	* Validates the rules
	* @param array|object $data The data to validate
	* @param array $rules The rules to validate
   * @param string $table The database table where we'll be looking for 'unique' rules
   * @param string $id_field The id field, which must be 0 in order to process unique' rules
	* @param array $ignore_array Array with the fields for which we'll skip validation, if any
	* @return bool True if the validation passed all tests, false otherwise
	*/
	public function validate($data, array $rules, string $table = '', string $id_field = '', array $ignore_array = []) : bool
	{
		$ok = true;
		$this->errors = [];

		foreach ($rules as $field => $rules_array) {
			foreach ($rules_array as $name => $rule) {
				if (in_array($field, $ignore_array)) {
					continue;
				}

				$value = (string)App::getProperty($field, $data);
				$error = $rule;
				$params  = '';
				if (is_array($rule)) {
					[$error, $params] = $rule;
				}

				if (isset($this->supported_rules[$name])) {
					$class = $this->supported_rules[$name];
					$validator = new $class($this->app, $field, $table, $id_field);

					if (!$validator->validate($value, $params)) {
						$ok = false;
						$this->errors[] = $error;
						break;
					}
				} else {
					//is rule a callable function/method?
					if (is_array($rule) && is_callable($rule[1])) {
						$method = $rule[1];
						if (!$method($value)) {
							$ok = false;
							$this->errors[] = $error;
							break;
						}
					} else {
						throw new \Exception("Unknown validator: {$name}");
					}
				}
			}
		}
		var_dump($ok, $this->errors);
		die;
		return $ok;
	}



	/**
	* Validates $value based on $type
	* @param string $value The value to validate
	* @param string Validation's type. Eg: url/email/ip/file
	* @return bool It returns true if $value passes validation, false otherwise. If will also return false, if $type is unknown
	*/
	public function check(string $value, string $type) : bool
	{
		switch ($type) {
			case 'url':
				return $this->isUrl($value);
			case 'email':
				return $this->isEmail($value);
			case 'ip':
				return $this->isIp($value);
			case 'file':
				return $this->isFile($value);
		}

		return false;
	}

	/**
	* Validates a datetime
	* @param int $year The year
	* @param int $month The month
	* @param int $day The day
	* @param int $hour The hour
	* @param int $minute The minute
	* @param int $second The second
	* @return bool Returns true if the params are valid
	*/
	public function isDatetime(int $year, int $month, int $day, int $hour, int $minute, int $second) : bool
	{
		if (!$this->isDate($year, $month, $day)) {
			return false;
		}
		if (!$this->isTime($hour, $minute, $second)) {
			return false;
		}

		return true;
	}

	/**
	* Validates a date
	* @param int $year The year
	* @param int $month The month
	* @param int $day The day
	* @return bool Returns true if the params are valid
	*/
	public function isDate(int $year, int $month, int $day) : bool
	{
		return checkdate($month, $day, $year);
	}

	/**
	* Validates a time
	* @param int $hour The hour
	* @param int $minute The minute
	* @param int $second The second
	* @return bool Returns true if the params are valid
	*/
	public function isTime(int $hour, int $minute, int $second) : bool
	{
		if ($hour < 0 || $hour > 23) {
			return false;
		}
		if ($minute < 0 || $minute > 59) {
			return false;
		}
		if ($second < 0 || $second > 59) {
			return false;
		}

		return true;
	}

	/**
	* Checks if $url is a valid url
	* @param string $url The url to validate
	* @return bool Returns true if the url is valid
	*/
	public function isUrl(string $url) : bool
	{
		$l_url = strtolower($url);

		if (strpos($l_url, 'ssh://') === 0) {
			return false;
		} elseif (strpos($l_url, 'ftp://') === 0) {
			return false;
		} elseif (strpos($l_url, 'mailto:') === 0) {
			return false;
		}

		return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}

	/**
	* Checks if $email is a valid email address
	* @param string $email The email to validate
	* @return bool Returns true if the email is valid
	*/
	public function isEmail(string $email) : bool
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	* Checks if $ip is a valid IP address
	* @param string $ip The IP to validate
	* @param bool $wildcards If true, the IP can contain wildcards
	* @return bool Returns true if the IP is valid
	*/
	public function isIp(string $ip, bool $wildcards = false) : bool
	{
		if (!$wildcards) {
			return filter_var($ip, FILTER_VALIDATE_IP);
		}

		//replace colons with dots if it's an IPv6 address
		$ip = str_replace(':', '.', strtolower($ip));
		$segments = explode('.', $ip);
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
