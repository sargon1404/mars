<?php
namespace Mars\Bin;

try {
	require(__DIR__ . '/autoload.php');
	require(__DIR__ . '/autoload-extensions.php');
	require(__DIR__  .'/autoload-app.php');

	$app = App::instantiate();
	$app->boot();

	$app->plugins->run('boot_bin');
} catch (\Exception $e) {
	$app->fatalError($e->getMessage());
}
