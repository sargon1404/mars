<?php
/**
* The Base Validator Class
* @package Mars
*/

namespace Mars\Validators;

use Mars\App;

/**
 * The Base Validator Class
 */
abstract class Rule
{
	use \Mars\AppTrait;

	/**
	 * @var string $error_string The error string
	 */
	protected string $error_string = '';

	/**
	 * @var string $error The generated error, if any
	 */
	protected string $error = '';

	/**
	 * Validates a value
	 * @param mixed $value The value to validate
	 * @param string $field The name of the field
	 * @param mixed $params Params to be passed to the validator, if any
	 * @return bool True if the validation passed
	 */
	public function validate(mixed $value, string $field, ...$params) : bool
	{
		$this->error = '';

		if ($this->isValid($value, ...$params)) {
			return true;
		}

		$this->error = $this->getErrorString($field, $params);

		return false;
	}

	/**
	 * Returns the validation error string
	 * @param string $field The name of the field
	 * @param mixed $params Params to be passed to the validator, if any
	 * @return string
	 */
	protected function getErrorString(string $field, ...$params) : string
	{
		return App::__($this->error_string, ['{FIELD}' => App::__($field)]);
	}

	/**
	 * Returns the generated error, if any
	 * @return string
	 */
	public function getError() : string
	{
		return $this->error;
	}
}
