<?php

require('files.php');

chdir(dirname(__DIR__, 3));

foreach ($files as $file) {
	//echo $file . "\n";
	opcache_compile_file($file);
}
