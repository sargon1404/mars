<?php
namespace Mars\Preload;

chdir(dirname(__DIR__, 3));

require('src/mars/preload/functions.php');
require('src/mars/boot.php');


$files = $app->dir->getFiles('src/mars/classes', true);
$traits_and_interfaces = get_traits_and_interfaces($files);
write_file(__DIR__ . '/generated/traits-interfaces.php', $traits_and_interfaces);

$classes = get_classes($files);
$classes = sort_classes($classes);
write_file(__DIR__ . '/generated/classes.php', $classes);

$app->bin->print('Preload list generated');
