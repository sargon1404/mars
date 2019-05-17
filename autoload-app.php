<?php
namespace mars\autoload;

/**
* Autoloader for the app files
*/
\spl_autoload_register(function ($name) {
	if (strpos($name, 'App\\') !== 0) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = dirname(__DIR__, 2) . '/' . get_filename($parts);

	require($filename);
});
