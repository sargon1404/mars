<?php
namespace Mars\Autoload;

/**
* Autoloader for the app files
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'App\\Extensions')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/' . get_filename($parts, 1, true);

	require($filename);
});
