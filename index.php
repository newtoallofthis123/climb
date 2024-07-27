<?php

/*
 * Entry point for the ClimbUI application
 */

namespace ClimbUI;

use Approach\Render\HTML;

// disable errors
error_reporting(0);

global $body, $webpage;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

$owner = null;
$repo = null;
if(getenv('CLIMBSUI_OWNER') != "" && getenv('CLIMBSUI_REPO') != "") {
    $owner = getenv('CLIMBSUI_OWNER');
    $repo = getenv('CLIMBSUI_REPO');
} else{
    echo "Please set the CLIMBSUI_OWNER and CLIMBSUI_REPO environment variables";
    echo $_ENV['CLIMBSUI_REPO'];
    exit;
}

$webpage = new HTML(tag: 'html');
$webpage->before = '<!DOCTYPE html>' . PHP_EOL;

$head = new HTML(tag: 'head');
$head[] = $pageTitle = new HTML(tag: 'title', content: 'ClimbUI');
$head[] = new HTML(tag: 'meta', attributes: [
    'charset' => 'utf-8',
], selfContained: true);
$head[] = new HTML(tag: 'meta', attributes: [
    'http-equiv' => 'X-UA-Compatible',
    'content' => 'IE=edge',
], selfContained: true);
$head[] = new HTML(tag: 'meta', attributes: [
    'name' => 'viewport',
    'content' => 'width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0',
], selfContained: true);
$head[] = new HTML(tag: 'meta', attributes: [
    'name' => 'author',
    'content' => 'Ishan Joshi',
], selfContained: true);

// We will be using Bootstrap for the layout
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'href' => '//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
], selfContained: true);

// Rest are some custom styles and scripts
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/static/css/layout.css',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/static/css/style.css',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/static/css/reset.css',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'href' => '/static/css/menu.css',
], selfContained: true);

$head[] = $pageTitle;

$head[] = new HTML(tag: 'link', attributes: [
    'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
    'rel' => 'stylesheet',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'href' => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
    'rel' => 'stylesheet',
], selfContained: true);

// JQuery baby!!
$head[] = new HTML(tag: 'script', attributes: [
    'src' => '//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js',
]);

// The actual approach library
$head[] = new HTML(tag: 'script', attributes: [
    'type' => 'text/javascript',
    'src' => '/static/js/approach/approach.interface.js',
]);
$head[] = new HTML(tag: 'script', attributes: [
    'type' => 'text/javascript',
    'src' => '/static/js/approach/approach.utility.js',
]);


$head[] = new HTML(tag: 'script', attributes: [
    'src' => '/static/js/main.js',
    'type' => 'module',
]);

$body = new HTML(tag: 'body');

$body->content = <<<HTML
    <div class="Stage">
        <div id="main" class="Screen">
            <div class="Oyster Interface InterfaceContent controls">
                <section class="header controls">
                    <button class="backBtn">
                        <div>
                            <i class="expand fa fa-angle-left"></i>
                        </div>
                    </button>
                    <button
                        class="controls btn btn-warning current-state ms-2"
                        id="menuButton"
                    >
                        <span id="menuButtonText"><span></span></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="breadcrumbs" style="display: none"></ul>
                    <span id="newButton">
                         <button
                            class="control btn btn-primary current-state ms-2"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "New" } }'
                            data-context='{ "_response_target": "#content > div", "parent_id": "", "repo": "$repo", "owner": "$owner" }'
                        >
                            New
                        </button>
                    </span>
                    <span id="newTemplate">
                    </span>
                </section>
                <ul class="Toolbar controls">
                    <li>
                        <div class="visual" style="padding: 5px 12px">
                            <button
                            class="control btn"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "Menu" } }'
                            data-context='{ "_response_target": ".Toolbar > .active > ul", "owner": "$owner", "repo": "$repo"}'
                            >
                                View
                            </button>
                        </div>
                        <ul></ul>
                    </li>
                </ul>
            </div>
            <div class="Viewport">
                <div id="content">
                    <div class="ClimbsUI"></div>
                </div>
                <div id="result"></div>
            </div>
        </div>
    </div>
    HTML;

$webpage[] = $head;
$webpage[] = $body;

echo $webpage->render();
