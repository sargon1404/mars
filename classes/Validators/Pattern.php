<?php
/**
* The Pattern Validator Class
* @package Mars
*/

namespace Mars\Validators;

/**
* The Pattern Validator Class
*/
class Pattern extends Rule
{
	/**
	* {@inheritdoc}
	*/
	protected string $error_string = 'validate_pattern_error';

	/**
	* @see \Mars\Validator\Rule::isValid()
	* {@inheritdoc}
	*/
	public function isValid(string $value, ...$params) : bool
	{
		if (empty($params[0])) {
			throw new \Exception("The Validator Pattern rule must have the pattern specified. Eg: pattern:/[a-Z0-9]*/");
		}

		if (count($params) == 1) {
			$pattern = $params[0];
		} else {
			$pattern = implode(':', $params);
		}

		return preg_match($pattern, $value, $m);
	}
}
