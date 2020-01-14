<?php
namespace Mars\Preload;

chdir(dirname(__DIR__, 3));

require('src/mars/preload/functions.php');
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
