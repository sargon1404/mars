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
	use SupportedRulesTrait;

	/**
	* @var array $errors The list of validation errors, if any
	*/
	protected array $errors = [];

	/**
	* @var array $supported_rules The list of suported rules
	*/
	protected array $supported_rules = [
		'required' => '\Mars\Validators\Required',
		'unique' => '\Mars\Validators\Unique',
		'min' => '\Mars\Validators\Min',
		'max' => '\Mars\Validators\Max',
		'min_chars' => '\Mars\Validators\MinChars',
		'max_chars' => '\Mars\Validators\MaxChars',
		'pattern' => '\Mars\Validators\Pattern',
		'email' => '\Mars\Validators\Email',
		'url' => '\Mars\Validators\Url',
		'ip' => '\Mars\Validators\Ip',
		'time' => '\Mars\Validators\Time',
		'date' => '\Mars\Validators\Date',
		'datetime' => '\Mars\Validators\Datetime',
	];

	

	/**
	* Returns the validation errors, if any
	* @return array The errors
	*/
	public function getErrors() : array
	{
		return $this->errors;
	}

	/**
	* Checks a value agains a validator
	* @param string|array $value The value to validate
	* @param string $rule The rule to validate the value against
	* @param string|array $params Extra params to pass to the validator
	* @return bool Returns true if the value is valid
	*/
	public function check(string|array $value, string $rule, string|array $params = '') : bool
	{
		if (!isset($this->supported_rules[$rule])) {
			throw new \Exception("Unknown validator: {$rule}");
		}

		$class = $this->supported_rules[$rule];
		$validator = new $class($this->app);

		return $validator->validate($value, $params);
	}

	/**
	* Validates the rules
	* @param array|object $data The data to validate
	* @param array $rules The rules to validate, in the format ['field' => [error => validation_type]]
   * @param string $table The database table where we'll be looking for 'unique' rules
   * @param string $id_field The id field, which must be 0 in order to process unique' rules
	* @param array $ignore_array Array with the fields for which we'll skip validation, if any
	* @return bool True if the validation passed all tests, false otherwise
	*/
	public function validate(array|object $data, array $rules, string $table = '', string $id_field = '', array $ignore_array = []) : bool
	{
		$ok = true;
		$this->errors = [];

		foreach ($rules as $field => $rules_array) {
			foreach ($rules_array as $error => $rule) {
				if (in_array($field, $ignore_array)) {
					continue;
				}

				$value = (string)App::getProperty($field, $data);

				if (is_callable($rule)) {
					//the rule is a custom callable function rather than a supported rule
					$function = $rule;
					if (!$function($value)) {
						$ok = false;
						$this->errors[] = $error;
						break;
					}
				} else {
					$name = $rule;
					$params  = '';
					if (is_array($rule)) {
						[$name, $params] = $rule;
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
						throw new \Exception("Unknown validator: {$name}");
					}
				}
			}
		}

		return $ok;
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
		return $this->check([$year, $month, $day, $hour, $minute, $second], 'datetime');
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
		return $this->check([$year, $month, $day], 'date');
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
		return $this->check([$hour, $minute, $second], 'time');
	}

	/**
	* Checks if $url is a valid url
	* @param string $url The url to validate
	* @return bool Returns true if the url is valid
	*/
	public function isUrl(string $url) : bool
	{
		return $this->check($url, 'url');
	}

	/**
	* Checks if $email is a valid email address
	* @param string $email The email to validate
	* @return bool Returns true if the email is valid
	*/
	public function isEmail(string $email) : bool
	{
		return $this->check($email, 'email');
	}

	/**
	* Checks if $ip is a valid IP address
	* @param string $ip The IP to validate
	* @param bool $wildcards If true, the IP can contain wildcards
	* @return bool Returns true if the IP is valid
	*/
	public function isIp(string $ip, bool $wildcards = false) : bool
	{
		return $this->check($ip, 'ip', $wildcards);
	}
}
