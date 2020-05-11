<?php
/**
* The Base Validator Class
* @package Mars
*/

namespace Mars\Validator;

use Mars\App;

/**
* The Base Validator Class
*/
abstract class Rule
{
	use \Mars\AppTrait;

	/**
	* @var string $field The name of the field we're validating
	*/
	protected string $field = '';

	/**
	* @var string $table The db table we're validating against
	*/
	protected string $table = '';

	/**
	* @var string $id_field The id field of the db table we're validating against
	*/
	protected string $id_field = '';

	/**
	* Validates the value
	* @param string|array $value The value to validate
	* @param string|array $params Extra params
	* @return bool
	*/
	abstract public function validate($value, $params) : bool;

	/**
	* Builds the object
	* @param App $app The app object
	* @param string $field The name of the field we're validating
	* @param string $table The db table we're validating agains, if any
	* @param string $id_field The id field of the db table we're validating against, if any
	*/
	public function __construct(App $app, string $field = '', string $table = '', string $id_field = '')
	{
		$this->app = $app;
		$this->field = $field;
		$this->table = $table;
		$this->id_field = $id_field;
	}
}
