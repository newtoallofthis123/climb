<?php

namespace ClimbUI;

use Approach\Render\HTML;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;

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

$code = $_GET['code'];
$context = [
  'http' => [
    'method' => 'POST',
    'header' => [
        'User-Agent:curl/8.5.0',
        'Accept: application/json',
    ],
    'content' => json_encode([
      'client_id' => $clientId,
      'client_secret' => $clientSecret,
      'code' => $code
    ])
  ]
];
$service = new Service(
  auto_dispatch: false,
  format_in: format::json,
  target_in: target::stream,
  target_out: target::variable,
  input: ['https://github.com/login/oauth/access_token'],
  metadata: [['context' => $context]]
);

$res = $service->dispatch();
$access_token = json_decode($res[0], true);
$access_token = $access_token['access_token'];

$webpage[] = new HTML(tag: 'p', content: 'Access Code: ' . $code);
$webpage[] = new HTML(tag: 'p', content: 'Access Token: ' . json_encode($res));

echo $webpage->render();