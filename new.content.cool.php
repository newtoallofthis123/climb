<?php

namespace ClimbUI;

global $body, $pageTitle, $firstNode;
require_once __DIR__ . '/support/lib/vendor/autoload.php';
require_once __DIR__ . '/components/tabs/requirements.php';
require_once __DIR__ . '/components/tabs/survey.php';
require_once __DIR__ . '/components/tabs/review.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/components/tabs/adapt.php';
require_once __DIR__ . '/tab.partial.php';

use Approach\Render\HTML;
use Approach\Render\XML;
use Approach\Render\Node;
use Approach\Render\Container;

$pageTitle->content = 'New Stuff';
$body[] = $firstNode;
$body[] = new HTML(tag: 'div', content: $requirementsBody, attributes: ['id' => 'tab']);
$body[] = new HTML(tag: 'div', content: $surveyBody, attributes: ['id' => 'tab']);
$body[] = new HTML(tag: 'div', content: $reviewBody, attributes: ['id' => 'tab']);

// ob_flush(); // should only be before or after something that is going to output like echo or print_r