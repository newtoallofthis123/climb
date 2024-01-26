<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/new.content.cool.php';

global $webpage; 

// echo $node; prints the render implicitly
// print_r($node->render()) prints the render directly
// print_r($node) prints the actual object, does not auto convert to string

// Composition::$Active->publish(); handles this usually as Composition::$Active->DOM is your document instead of 1swebpage1

echo $webpage;