<?php
/**
* The System's Language Class
* @package Mars
*/

namespace Mars\System;

use Mars\App;

/**
 * The System's Language Class
 */
class Language extends \Mars\Extensions\Language
{
	/**
	 * Builds the language
	 * @param App $app The app object
	 */
	public function __construct(App $app)
	{
		if (!$app->config->language) {
			return;
		}

		parent::__construct($app->config->language, $app);

		$this->loadFile('errors');
	}
}
