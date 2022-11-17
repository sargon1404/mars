<?php

chdir(dirname(__DIR__, 3));

//load the traits & interfaces
$files = require('src/mars/preload/generated/traits-interfaces.php');
foreach ($files as $file) {
	opcache_compile_file($file);
}

//load the classes
$files = require('src/mars/preload/generated/classes.php');

foreach ($files as $file) {
	opcache_compile_file($file);
}
