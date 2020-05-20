<?php
namespace Mars\Cli;

try {
	require('src/mars/functions.php');
	require('src/mars/autoload.php');
	require('src/mars/autoload-app.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('boot_cli');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
