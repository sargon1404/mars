<?php
namespace Mars\Preload;

chdir(dirname(__DIR__, 3));

require('src/mars/boot-cli.php');

$app->file->listDir('src/mars/classes', $dirs, $files, true, true);

$files = sort_files($files);

$cnt = '<?php' . "\n\n";
$cnt.= '$files = [' . "\n";
foreach ($files as $file) {
	$cnt.= "'" . $file . "'," . "\n";
}
$cnt.= '];' . "\n";

file_put_contents(__DIR__ . '/files.php', $cnt);

$app->cli->print('Preload list generated');

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
