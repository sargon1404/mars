<?php
namespace Mars;

try {
	require('src/mars/autoload.php');
	require('src/mars/autoload-app.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('bootSystem');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
