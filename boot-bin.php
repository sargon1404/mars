<?php
namespace Mars\Bin;

try {
	require('src/mars/autoload.php');
	require('src/mars/autoload-app.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('boot_bin');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
