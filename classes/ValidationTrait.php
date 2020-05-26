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
	* @var array $skip_validation_rules Validation rules to skip when validating, if any
	*/
	protected array $skip_validation_rules = [];

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
	* The same as skipValidationRules
	* @param string $rule The rule to skip
	* @return $this
	*/
	public function skipValidationRule(string $rule)
	{
		return $this->skipValidationRules($rule);
	}

	/**
	* Skips rules from validation
	* @param array|string $skip_rules Rules which will be skipped at validation
	* @return $this
	*/
	public function skipValidationRules($skip_rules)
	{
		$skip_rules = App::getArray($skip_rules);
		foreach ($skip_rules as $rule) {
			if (!in_array($rule, $this->skip_validation_rules)) {
				$this->skip_validation_rules[] = $rule;
			}
		}

		return $this;
	}

	/**
	* Validates the data
	* @param array|object $data The data to validate
	* @return bool True if the validation passed all tests, false otherwise
	*/
	protected function validate($data = []) : bool
	{
		$rules = $this->getValidationRules();
		if (!$rules) {
			return true;
		}

		if (!$this->validator->validate($data, $rules, $this->getTable(), $this->getIdName(), $this->skip_validation_rules)) {
			$this->setErrors($this->validator->getErrors());

			return false;
		}

		return true;
	}
}
