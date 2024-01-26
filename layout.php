<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use Approach\Render\HTML;

$webpage = new HTML(tag: 'html');
$webpage->before = '<!DOCTYPE html>' . PHP_EOL;

$head = new HTML(tag: 'head');
$body = new HTML(tag: 'body');
$pageTitle = new HTML(tag: 'title', content: 'ClimbUI');

$webpage[] = $head;
$webpage[] = $body;


// :before <tag attr classes>prefix content [child nodes] suffix</tag> :after
