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
* @param bool $convert_path If not null, will convert the specified path index part of the path. Eg: MyNamespace to my-namespace
* @return string The filename
*/
function get_filename(array $parts, int $base_parts = 1, ?int $convert_path = null) : string
{
	$parts_count = count($parts);

	$path = '';
	$name = $parts[$parts_count - 1];

	//determine the dir and name of the class
	if ($parts_count > $base_parts + 1) {
		$path_parts = array_slice($parts, $base_parts, $parts_count - ($base_parts + 1));

		if ($convert_path !== null) {
			for ($i = 0; $i < $convert_path; $i++) {
				if (isset($path_parts[$i])) {
					$path_parts[$i] = convert_part($path_parts[$i]);
				}
			}
		}

		$path = implode('/', $path_parts) . '/';
	}

	return $path . $name . '.php';
}

/**
* Converts the path. Converts a namespace part like MyNamespace to folder my-namespace
* @param array $parts The namespace parts
* @return string The dir
*/
function convert_part(string $part) : string
{
	$new_part = '';
	$len = strlen($part);

	for ($i = 0; $i < $len; $i++) {
		$char = $part[$i];
		$ord = ord($char);

		if ($i && $ord >= 65 && $ord <= 90) {
			if ($i) {
				$new_part.= '-';
			}
		}

		$new_part.= $char;
	}

	return strtolower($new_part);
}
