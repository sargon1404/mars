<?php
/**
* The Errors Trait
* @package Mars
*/

namespace Mars;

/**
* The Errors Trait
* Trait implementing errors functionality.
*/
trait ErrorsTrait
{
	/**
	* @var array $errors  The generated errors, if any
	*/
	protected array $errors = [];

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
	* Returns true if no errors have been generated
	* @return bool
	*/
	public function ok() : bool
	{
		if ($this->errors) {
			return false;
		}

		return true;
	}
}
