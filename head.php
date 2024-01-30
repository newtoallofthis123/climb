<?php

namespace ClimbUI;

// This the head file Render
// I can now include this wherever I need head stuff

global $head, $pageTitle;
require_once __DIR__ . '/support/lib/vendor/autoload.php';

use \Approach\Render\HTML;
use \Approach\Render\Node;

// Can also be done like this
//$head->content .= file_get_contents(__DIR__ . '/support/static/head.htm'); //optional

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
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'href' => '//cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'stylesheet',
    'href' => '//cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
], selfContained: true);
$head[] = new HTML(tag: 'script', attributes: [
    'src' => '//cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
]);
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
$head[] = new HTML(tag: 'link', attributes: [
    'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
    'rel' => 'stylesheet',
], selfContained: true);
$head[] = new HTML(tag: 'link', attributes: [
    'rel' => 'shortcut icon',
    'href' => 'https://noobscience.vercel.app/favicon.ico',
    'type' => 'image/x-icon',
], selfContained: true);
$head[] = $pageTitle;

$head[] = new HTML(tag: 'script', attributes: [
    'src' => '//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js',
]);
$head[] = new HTML(tag: 'script', attributes: [
    'type' => 'text/javascript',
    'src' => '/static/js/approach/approach.interface.js',
]);
$head[] = new HTML(tag: 'link', attributes: [
    'src' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
]);
$head[] = new HTML(tag: 'script', attributes: [
    'src' => '/static/js/menu.js',
]);
$head[] = new HTML(tag: 'script', attributes: [
    'src' => '/static/js/tabs.js',
]);

$DocReady = new Node();
$DocReady[] = 'onReadyHandle(document);';

$head[] = new HTML(tag: 'script', attributes: [
    'content' => <<<JS
        // reattaches interfaces after HTML has been changed, should plausibly be on a DOM mutation event instead
        Interface.prototype.RefreshComplete = onReadyHandle;

        $(document).ready(function(){
            
        });
JS
]);
