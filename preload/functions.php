<?php
namespace Mars\Preload;

/**
* Returns the classes
* @param array $files The files list
* @return array The classes
*/
function get_classes(array $files) : array
{
	$classes = [];
	foreach ($files as $file) {
		if (str_contains($file, 'Interface') || str_contains($file, 'Trait')) {
			continue;
		}

		$classes[] = $file;
	}

	return $classes;
}

/**
* Sorts the classes
* @param array $files The files list
* @return array The sorted classes
*/
function sort_classes(array $files) : array
{
	natsort($files);

	$classes = [];
	foreach ($files as $file) {
		$sort = 500;

		$cnt = file_get_contents($file);
		if (preg_match('/class.*extends/isU', $cnt)) {
			$sort = 100;
		}


		$classes[$file] = $sort;
	}

	asort($classes);
	$classes = array_reverse($classes);

	return array_keys($classes);
}

/**
* Returns the traits and interfaces
* @param array $files The files list
* @return array The traits and interfaces
*/
function get_traits_and_interfaces(array $files) : array
{
	$traits_interfaces = [];
	foreach ($files as $file) {
		if (str_contains($file, 'Interface') || str_contains($file, 'Trait')) {
			$traits_interfaces[] = $file;
		}
	}

	return $traits_interfaces;
}

/**
* Writes $files as a file
* @param string $filename The name of the files
* @param array $files The files to write
*/
function write_file(string $filename, array $files)
{
	natsort($files);

	$cnt = '<?php' . "\n\n";
	$cnt.= 'return [' . "\n";
	foreach ($files as $file) {
		$cnt.= "\t'" . $file . "'," . "\n";
	}
	$cnt.= '];' . "\n";

	file_put_contents($filename, $cnt);
}
