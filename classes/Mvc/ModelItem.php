<?php
/**
* The Model Item Class
* @package Mars
*/

namespace Mars\Mvc;

use Mars\Item;

/**
* The Model Item Class
* Implements the Model functionality of the MVC pattern. Extends the Item class.
*/
abstract class ModelItem extends Item
{
	use ModelTrait;
}
