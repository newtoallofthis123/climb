<?php

namespace global;
require_once __DIR__ . '/support/lib/vendor/autoload.php';

error_reporting(0);
use Exception;
use \Approach\Imprint\Imprint;
use \Approach\Service\format;
use \Approach\Service\target;
use \Approach\path;
use \Approach\Scope;

$path_to_project = __DIR__ . '/src';
$path_to_approach = __DIR__ . '/support/lib/approach/';
$path_to_support = __DIR__ . '//support//';

$scope = new Scope(
    project: 'ClimbUI',
    path: [
        path::project->value => $path_to_project,
        path::installed->value => $path_to_approach,
        path::support->value => $path_to_support,
    ],
);
$fileDir = $scope->GetPath(path::pattern);
$fileDir = str_replace('/../', '', $fileDir);


$imp = new Imprint(
    imprint: 'Climb.xml',
    imprint_dir: __DIR__.'/support/pattern/',
);

$success = $imp->Prepare();

echo '<!DOCTYPE html>' . PHP_EOL.'<html>'.PHP_EOL. '<head><title>Quick Mint</title>' . PHP_EOL.'<body><pre>';

echo 'Minting Viewer..'.PHP_EOL;
$imp->Mint('Viewer');

echo 'Minting Editor..'.PHP_EOL;
$imp->Mint('Editor');

echo 'Done'.PHP_EOL. '</pre></body>' . PHP_EOL.'</html>';