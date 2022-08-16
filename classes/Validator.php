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
	use HandlersTrait;
	use ErrorsTrait;

	/**
	* @var array $supported_handlers The list of supported_handlers
	*/
	protected array $supported_handlers = [
		'req' => '\Mars\Validators\Required',
		'required' => '\Mars\Validators\Required',
		'unique' => '\Mars\Validators\Unique',
		'min' => '\Mars\Validators\Min',
		'max' => '\Mars\Validators\Max',
		'interval' => '\Mars\Validators\Interval',
		'min_chars' => '\Mars\Validators\MinChars',
		'max_chars' => '\Mars\Validators\MaxChars',
		'chars' => '\Mars\Validators\Chars',
		'pattern' => '\Mars\Validators\Pattern',
		'email' => '\Mars\Validators\Email',
		'url' => '\Mars\Validators\Url',
		'ip' => '\Mars\Validators\Ip',
		'time' => '\Mars\Validators\Time',
		'date' => '\Mars\Validators\Date',
		'datetime' => '\Mars\Validators\Datetime',
	];

	/**
	* Builds the object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->handler_method = 'validate';
	}

	/**
	* Checks a value agains a validator
	* @param mixed $value The value to validate
	* @param string $rule The rule to validate the value against
	* @param string $field The name of the field
	* @param mixed $params Extra params to pass to the validator
	* @return bool Returns true if the value is valid
	*/
	public function isValid(mixed $value, string $rule, string $field = '', ...$params) : bool
	{
		return $this->getValue($rule, $value, $field, ...$params);
	}

	/**
	* Validates the rules
	* @param array|object $data The data to validate
	* @param array $rules The rules to validate, in the format ['field' => validation_type]. Eg: 'my_id' => 'required|min:3|unique:my_table:my_id'
	* @param array $error_strings Custom error strings, if any
	* @param array $skip_array Array with the fields for which we'll skip validation, if any
	* @return bool True if the validation passed all tests, false otherwise
	*/
	public function validate(array|object $data, array $rules, array $error_strings = [], array $skip_array = []) : bool
	{
		$ok = true;
		$this->errors = [];

		foreach ($rules as $field => $field_rules) {
			if (in_array($field, $skip_array)) {
				continue;
			}

			$value = App::getProperty($data, $field);

			$error_field = $field_rules['field'] ?? $field;
			$rules_list = $field_rules['rules'] ?? $field_rules;

			$rules_array = explode('|', $rules_list);
			foreach ($rules_array as $rule) {
				$parts = explode(':', trim($rule));
				$rule = reset($parts);
				$params = array_slice($parts, 1);

				if (!$this->isValid($value, $rule, $error_field, ...$params)) {
					$this->addError($rule, $field, $error_strings);
					$ok = false;
				}
			}
		}

		return $ok;
	}

	/**
	* Adds an error for a field & rule
	* @param string $rule The validation rule name
	*/
	protected function addError(string $rule, string $field, array $error_strings)
	{
		//do we have in the $error_strings array a custom error for this rule & $field?
		if ($error_strings && isset($error_strings[$field][$rule])) {
			$this->errors[] = $error_strings[$field][$rule];

			return;
		}

		//use the handler's error
		$this->errors[] = $this->handlers[$rule]->getError();
	}

	/**
	* Validates a datetime
	* @param string $value The value to validate
	* @param string $format The date's format
	* @return bool Returns true if the datetime is valid
	*/
	public function isDatetime(string $value, string $format = '') : bool
	{
		return $this->isValid($value, 'datetime', '', $format);
	}

	/**
	* Validates a date
	* @param string $value The value to validate
	* @param string $format The date's format
	* @return bool Returns true if the date is valid
	*/
	public function isDate(string $value, string $format = '') : bool
	{
		return $this->isValid($value, 'date', '', $format);
	}

	/**
	* Validates a time value
	* @param string $value The value to validate
	* @param string $format The date's format
	* @return bool Returns true if the time value is valid
	*/
	public function isTime(string $value, string $format = '') : bool
	{
		return $this->isValid($value, 'time', '', $format);
	}

	/**
	* Checks if $url is a valid url
	* @param string $value The value to validate
	* @return bool Returns true if the url is valid
	*/
	public function isUrl(string $value) : bool
	{
		return $this->isValid($value, 'url');
	}

	/**
	* Checks if $email is a valid email address
	* @param string $email The email to validate
	* @return bool Returns true if the email is valid
	*/
	public function isEmail(string $email) : bool
	{
		return $this->isValid($email, 'email');
	}

	/**
	* Checks if $ip is a valid IP address
	* @param string $ip The IP to validate
	* @param bool $wildcards If true, the IP can contain wildcards
	* @return bool Returns true if the IP is valid
	*/
	public function isIp(string $ip, bool $wildcards = false) : bool
	{
		return $this->isValid($ip, 'ip', '', $wildcards);
	}
}
