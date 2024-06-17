<?php

namespace ClimbUI;

use \Approach\Render\HTML;
use ClimbUI\Render\Intent;
use ClimbUI\Render\Header;
use ClimbUI\Render\Oyster;
use ClimbUI\Render\Pearl;
use ClimbUI\Render\Visual;

require_once __DIR__ . '/support/lib/vendor/autoload.php';
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
global $body;

$body[] = $stage = new HTML(tag: 'div', classes: ['Stage']);