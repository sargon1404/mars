<?php
/**
* The Validation Trait
* @package Mars
*/

namespace Mars;

/**
* The Validation Trait
* Provides validation functionality
*/
trait ValidationTrait
{

	/**
	* @var array $errors  Contains the generated error codes, if any
	*/
	/*protected array $errors = [];*/

	/**
	* @var array $validation_rules Validation rules
	*/
	/*protected array $validation_rules = [];*/

	/**
	* @var array $validation_rules_to_skip Validation rules to skip when validating, if any
	*/
	//protected array $validation_rules_to_skip = [];

	/**
	* @var array $validation_error_strings Validation rules
	*/
	/*protected array $validation_error_strings = [];*/

	/**
	* Returns the generated errors, if any
	* @return array
	*/
	public function getErrors() : array
	{
		return $this->errors;
	}

	/**
	* Returns the first generated error, if any
	* @return mixed
	*/
	public function getFirstError()
	{
		return reset($this->errors);
	}

	/**
	* Sets the generated errors
	* @param array $errors The errors
	*/
	protected function setErrors(array $errors)
	{
		$this->errors = $errors;
	}

	/**
	* Returns the validation rules
	* @return array The rules
	*/
	protected function getValidationRules() : array
	{
		return $this->validation_rules;
	}

	/**
	* Returns the validation rules to skip
	* @return array The rules to skip
	*/
	protected function getValidationRulesToSkip() : array
	{
		return $this->validation_rules_to_skip;
	}

	/**
	* Returns the validation error strings
	* @return array The error strings
	*/
	protected function getValidationErrorStrings() : array
	{
		return $this->validation_error_strings;
	}

	/**
	* The same as skipValidationRules
	* @param string $rule The rule to skip
	* @return $this
	*/
	public function skipValidationRule(string $rule)
	{
		return $this->skipValidationRules([$rule]);
	}

	/**
	* Skips rules from validation
	* @param array $skip_rules Rules which will be skipped at validation
	* @return $this
	*/
	public function skipValidationRules(array $skip_rules)
	{
		foreach ($skip_rules as $rule) {
			if (!in_array($rule, $this->validation_rules_to_skip)) {
				$this->validation_rules_to_skip[] = $rule;
			}
		}

		return $this;
	}

	/**
	* Validates the data
	* @param array|object $data The data to validate
	* @return bool True if the validation passed all tests, false otherwise
	*/
	protected function validate(array|object $data = []) : bool
	{
		$rules = $this->getValidationRules();
		if (!$rules) {
			return true;
		}

		if (!$this->validator->validate($data, $rules, $this->getValidationErrorStrings(), $this->getValidationRulesToSkip())) {
			$this->setErrors($this->validator->getErrors());

			return false;
		}

		return true;
	}
}
