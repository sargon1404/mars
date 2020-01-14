<?php

chdir(dirname(__DIR__, 3));

require('src/mars/preload/files.php');

foreach ($files as $file) {
	opcache_compile_file($file);
}
