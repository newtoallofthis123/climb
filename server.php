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

global $scope;
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
    format_in: format::json,
    format_out: format::json,
    target_in: target::variable,
    target_out: target::stream,
    input: [
        $_POST['json']
    ],
    output: ['php://output'],
);

$output = $service->dispatch();