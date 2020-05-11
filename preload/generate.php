<?php
namespace Mars\Preload;

chdir(dirname(__DIR__, 3));

require('src/mars/preload/functions.php');
require('src/mars/boot-cli.php');

$app->file->listDir('src/mars/classes', $dirs, $files, true, true);

$traits_and_interfaces = get_traits_and_interfaces($files);
write_file(__DIR__ . '/traits-interfaces.php', $traits_and_interfaces);

$classes = get_classes($files);
$classes = sort_classes($classes);
write_file(__DIR__ . '/classes.php', $classes);

$app->cli->print('Preload list generated');
