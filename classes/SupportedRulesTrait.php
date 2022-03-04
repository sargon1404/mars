<?php
/**
* The Supported Rules Trait
* @package Mars
*/

namespace Mars;

/**
* The SupportedRulesTrait
* Trait implementing supported rules functionality. Classes using this trait must set the $driver and $driver_namespace properties
*/
trait SupportedRulesTrait
{
	/**
	* @var array $supported_rules The list of supported rules in the name => class format
	*/
	//protected array $supported_rules = [];

	/**
	* @var array $rules Array storing the rule objects
	*/
	protected array $rules = [];

	/**
	* @var string $rule_method The method called on a rule object
	*/
	protected string $rule_method = 'get';

	/**
	* Maps a value [scalar|array] to a callback
	*/
	public function map($value, callable $callback)
	{
		if (is_array($value)) {
			return array_map($callback, $value);
		}

		return $callback($value);
	}

	/**
	* Returns a value
	* @param mixed $value The value
	* @param mixed $rule The name of the rule
	* @param mixed $args The arguments to pass to the rule, if any
	*/
	public function value($value, string $rule, ...$args) : mixed
	{
		return $this->map($value, function($value) use ($rule, $args) {
			$args = array_merge([$value], $args);

			return $this->getValue($rule, ...$args);
		});
	}

	/**
	* Adds a supported validation rule
	* @param string $name The name of the rule
	* @param string $class The class which will handle it
	* @return static
	*/
	public function addSupportedRule(string $name, string $class) : static
	{
		$this->supported_rules[$name] = $class;

		return $this;
	}

	/**
	* Removes a supported validation rule
	* @param string $name The name of the rule
	* @return static
	*/
	public function removeSupportedRule(string $name) : static
	{
		unset($this->supported_rules[$name]);

		return $this;
	}

	/**
	* @param string $rule The rule to get the value for
	* @param mixed $args The arguments to pass to the rule
	* @return mixed
	*/
	protected function getValue(string $rule, ...$args)
	{
		$func = $this->getRuleHandler($rule);

		return call_user_func_array($func, $args);
	}

	/**
	* Returns the callable handling a rule
	* @param string $rule The rule to get the value for
	* @return callable The rule handler
	*/
	protected function getRuleHandler(string $rule)
	{
		if (isset($this->rules[$rule])) {
			return [$this->rules[$rule], $this->rule_method];
		}
		if (!isset($this->supported_rules[$rule])) {
			throw new \Exception("Unknow rule '{$rule}'");
		}

		if (is_string($this->supported_rules[$rule])) {
			$class = $this->supported_rules[$rule];

			$this->rules[$rule] = new $class($this->app);

			return [$this->rules[$rule], $this->rule_method];

		} elseif (is_array($this->supported_rules[$rule])) {
			return [$this, reset($this->supported_rules[$rule])];
		} else {
			return $this->supported_rules[$rule];
		}
	}
}
