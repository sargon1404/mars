<?php
/**
* The Block Trait
* @package Mars
*/

namespace Mars\Extensions;

/**
* The Block Trait
* Trait implementing the Block functionality
*/
trait BlockTrait
{
	use \Mars\Extensions\Abilities\MvcTrait;
	use \Mars\Extensions\Abilities\LanguagesTrait;

	/**
	* @internal
	*/
	protected static string $type = 'block';

	/**
	* @internal
	*/
	protected static string $base_dir = 'blocks';

	/**
	* @internal
	*/
	protected static string $namespace = "\\App\\Extensions\\Blocks\\";
}
