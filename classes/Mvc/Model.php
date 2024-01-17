<?php
/**
* The Model Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\Items;

/**
 * The Model Class
 * Implements the Model functionality of the MVC pattern. Extends the Items class.
 */
abstract class Model extends Items
{
	use ModelTrait;
}
