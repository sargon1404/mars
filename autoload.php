<?php
namespace Mars\Autoload;

/**
* Autoloader for the mars files
*/
\spl_autoload_register(function ($name) {
	if (!str_contains($name, 'Mars\\')) {
		return;
	}

	$parts = explode('\\', $name);

	$filename = __DIR__ . '/classes/' . get_filename($parts);

	require($filename);
});

/**
* Returns the autoload filename from the namespace parts
* @param array $parts The namespace parts
* @param int $base_parts The number of base parts in the namespace
* @return string The filename
*/
function get_filename(array $parts, int $base_parts = 1) : string
{
	$parts_count = count($parts);

	$path = '';
	$name = $parts[$parts_count - 1];

	//determine the dir and name of the class
	if ($parts_count > $base_parts + 1) {
		$path_parts = array_slice($parts, $base_parts, $parts_count - ($base_parts + 1));

		$path = get_path($path_parts);
	}

	return $path . $name . '.php';
}

/**
* Converts the parts of a namespace to a path. Converts a namespace part like MyNamespace to folder my-namespace
* @param array $parts The namespace parts
* @return string The dir
*/
function get_path(array $parts) : string
{
	$path_parts = [];

	foreach ($parts as &$part) {
		$dir = '';

		$len = strlen($part);

		for ($i = 0; $i < $len; $i++) {
			$char = $part[$i];
			$ord = ord($char);

			if ($i && $ord >= 65 && $ord <= 90) {
				if ($i) {
					$dir.= '-';
				}
			}

			$dir.= $char;
		}

		$path_parts[] = $dir;
	}

	$path = implode('/', $path_parts) . '/';

	return strtolower($path);
}
