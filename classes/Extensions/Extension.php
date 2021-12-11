<?php
/**
* The Extension Class
* @package Mars
*/

namespace Mars\Extensions;

/**
* The Extension Class
* Base class for all extensions
* The difference between Extension and Extension Basic is the former extends Itme while the later extends Entity
*/
abstract class Extension extends \Mars\Item
{
	use Body;
}
