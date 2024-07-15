<?php

namespace ClimbUI;

use Approach\Render\HTML;

// disable errors
error_reporting(0);

global $webpage;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';

$clientId = getenv('GITHUB_APP_CLIENT_ID');
$clientSecret = getenv('GITHUB_APP_CLIENT_SECRET');

if($clientId == "" || $clientSecret == "") {
  echo "Please set the GITHUB_APP_CLIENT_ID and GITHUB_APP_CLIENT_SECRET environment variables";
  exit(1);
}

$url = "https://github.com/login/oauth/authorize?client_id=" . $clientId;

$webpage[] = new HTML(tag: 'a', attributes: ['href' => $url], content: 'Login with GitHub');

echo $webpage->render();