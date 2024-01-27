<?php

namespace ClimbUI;

ob_flush();

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use \ClimbUI\Service\Server;
use \Approach\path;
use \Approach\Scope;
use \Approach\Service\target;
use \Approach\Service\format;
use Approach\Service\Service;

$path_to_project = __DIR__ . '/';
$path_to_approach = __DIR__ . '/support/lib/approach/';
$path_to_support = __DIR__ . '//support//';

// echo PHP_EOL . PHP_EOL . 'PATH TO PROJECT: ' . $path_to_project . PHP_EOL . PHP_EOL;
// echo PHP_EOL . PHP_EOL . 'PATH TO APPROACH: ' . $path_to_approach . PHP_EOL . PHP_EOL;

// print_r('starting');
$scope = new Scope(
	path: [
		path::project->value        =>  $path_to_project,
		path::installed->value      =>  $path_to_approach,
		path::support->value        =>  $path_to_support,
	],
);

// The php://input stream is needed apache2
$service = new Server(
	auto_dispatch: false,
	target_in: target::stream,
	target_out: target::stream,
	format_out: format::json,
	format_in: format::json,
	input: ['php://input'],
	output: ['php://output'],
);

// $service->disconnectAll();
// print_r('step 2');
// Perform a command that causes some standard output
$output = $service->dispatch();

// $server = new Server();
// $response = $server->dispatch();
// print_r('step 3');
print_r($output);
