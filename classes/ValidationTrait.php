<?php
/**
* The Validation Trait
* @package Mars
*/

namespace Mars;

/**
* The Validation Trait
* Provides validation functionality
* Classes using this trait must set these properties:
* protected array $validation_rules = [];
* protected array $validation_rules_to_skip = [];
* protected array $validation_error_strings = [];
*/
trait ValidationTrait
{
	use ErrorsTrait;

	/**
	* @var array $validation_rules Validation rules
	*/
	/*protected array $validation_rules = [];*/

	/**
	* @var array $validation_rules_to_skip Validation rules to skip when validating, if any
	*/
	//protected array $validation_rules_to_skip = [];

	/**
	* @var array $validation_error_strings Custom error strings
	*/
	/*protected array $validation_error_strings = [];*/

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
