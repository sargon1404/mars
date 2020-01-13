<?php
/**
* The Php Session Class
* @package Mars
*/

namespace Mars\Session;

use Mars\App;

/**
* The Php Session Class
* Session driver which uses the default php implementation
*/
class Php implements DriverInterface
{
	use \Mars\AppTrait;
}
