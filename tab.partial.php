<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

use Approach\Render\HTML;

$TabBar = new HTML(tag: 'div', classes: ['TabBar']);
$tabs = [
    '0' => 'Requirements',
    '1' => 'Survey',
    '2' => 'Review',
    '3' => 'Work',
    '4' => 'Describe',
    '5' => 'Adapt',
];

foreach ($tabs as $index => $tab) $TabBar[] = new HTML(
    tag: 'div',
    classes: ['tab-button', ' tab-button-' . $index],
    attributes: [
        'id' => 'tabBtn'.$index,
    ],
    content: $tab . ' <i class="bi bi-arrow-right"></i>'
);

// for some reason the id doesn't seem to work for the life of me
// Hence, at least for now, I'm manually putting it in

$i = 0;
foreach ($TabBar as $tab) {
    $tab->id = $i;
    $i++;
}

$firstNode = new HTML(tag: 'div', classes: ['New']);
$firstNode[] = $TabBar;

$firstNode = new HTML(tag: 'div', classes: ['New']);
$firstNode[] = $TabBar;