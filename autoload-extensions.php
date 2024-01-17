<?php
namespace Mars\Autoload;

use Mars\App;

/**
 * Autoloader for the app files
 */
\spl_autoload_register(function ($name) {
	if (!str_starts_with($name, 'Modules')) {
		return;
	}

	$app = App::get();
	$parts = explode('\\', $name);

	$filename = $app->extensions_path . '/' . App::EXTENSIONS_DIRS['modules'] . '/' . get_filename($parts, 1, true);
	;

	require($filename);
});
