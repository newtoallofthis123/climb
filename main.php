<?php

namespace AvenuePad;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

// use \AvenuePad\Composition\handler as Composition;
use \Approach\Scope;
use \Approach\path;
use \Approach\nullstate;
use \Approach\Composition\Composition;
use \Approach\deploy;
use Approach\runtime;

$scope = new Scope(
	path: [
		path::project->value    => __DIR__. '/src',
		path::installed->value  => '/srv/climbs.my.home/support/lib/approach',
		path::cache->value		=> '/dev/shm/climbs.my.home/',
		// path::share->value		=> '/usr/share/climbs.my.home/',
		path::route->value		=> '/srv/climbs.my.home/src/Composition',
	],
	deployment: [
		deploy::base->value		=>	'climbs.my.home'
	],
	mode: runtime::development,	// We are in development mode. Similar to debug with more verbosity and dynamics. Debug is for production primarily.
	environment: runtime::staging,	// We are in a staging environment
	project: 'AvenuePad',
);
$scope->ErrorRenderable->content = file_get_contents(path::support->get() . 'placeholders/error.html');

Composition::$types = [
	'Composition'	=> 6,
	'Dynamic'		=> 777,
	'Listing'		=> 100
];

Composition::$type_index = [
	6   => 'Composition',
	777 => 'Dynamic',
	100 => 'Listing',
];

Composition::$routes = [
	'climbs.my.home/'	         => 777,
	'climbs.my.home/listings'	 => 6,
];

$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

Composition::$Active = Composition::Route(url: $url);

if (Composition::$Active instanceof nullstate)
	exit(Composition::$Active->value);

$route   = $scope::GetPath(path::route);
var_dump(Scope::$Active->type);
$compose = $route . '/handler.php';
$scope->ErrorRenderable->content = file_get_contents(path::support->get() . 'placeholders/error.html');

// TODO: check if this is still required after the php 8.2 stable release
if (file_exists($compose)) require_once $compose;


// TODO: Recursively ASCEND to find compose.php if not found, thereby, implementing 
// depth-first dive (for type) and ascend (for populating content) which allows a modular, extensible approach 
// to composition while still allowing for natural inheritance in the common use case.

Composition::$Active->access_mode = Composition::PUBLISH_FULL;
Composition::$Active->publish();
