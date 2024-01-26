<?php

namespace ClimbUI;

global $body, $pageTitle, $firstNode;
require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/tab.partial.php';

use Approach\Render\HTML;

$pageTitle = new HTML(tag: 'title', content: 'Climb UI');
$body[] = $firstNode;
