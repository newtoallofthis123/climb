<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use Approach\Render\HTML;

$TabBar = new HTML(tag: 'div', classes: ['TabBar']);
$tabs = [
    '1' => 'Requirements',
    '2' => 'Survey',
    '3' => 'Review',
    '4' => 'Work',
    '5' => 'Describe',
    '6' => 'Adapt',
];

$rightArrow = new HTML(tag: 'i', classes: ['bi', 'bi-arrow-right']);

foreach ($tabs as $index => $tab) $TabBar[] = new HTML(
    tag: 'div',
    classes: ['tab-button'],
    attributes: [
        'id' => 'tabBtn' . $index,
    ],
    content: $tab . $rightArrow
);

// for some reason the id doesn't seem to work for the life of me
// Hence, at least for now, I'm manually putting it in

$firstNode = new HTML(tag: 'div', classes: ['New']);
$firstNode[] = $TabBar;

$firstNode = new HTML(tag: 'div', classes: ['New']);
$firstNode[] = $TabBar;
