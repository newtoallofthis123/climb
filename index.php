<?php

namespace ClimbUI;

global $webpage;
require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/content.php';

use Approach\Render\HTML;
use mysqli;

$serverName = "localhost";
$username = "noob";
$password = "NoobScience";

// Create connection
$conn = new mysqli($serverName, $username, $password, "t3_test");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo $webpage->render();