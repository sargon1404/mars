<?php
namespace Mars\Preload;

/**
* Sorts the files. List the interfaces and traits first
* @param array $files The files to sort
* @return array $files The sorted files
*/
function sort_files(array $files) : array
{
	usort($files, function ($f1, $f2) {
		if (strpos($f1, 'Interface') !== false) {
			return -1;
		} elseif (strpos($f2, 'Interface') !== false) {
			return 1;
		}
		
		if (strpos($f1, 'Trait') !== false) {
			return -1;
		} elseif (strpos($f2, 'Trait') !== false) {
			return 1;
		}
		
		return 0;
	});
	
	return $files;
}
