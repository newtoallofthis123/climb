<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\Node;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use Exception;
use Stringable;

class UpdateIssue extends Service
{
    /**
     * @return array<string,null|string|array|bool>
     */
    public static function getApiKey()
    {
        $filename = __DIR__ . '/../../config.json';
        if (!file_exists($filename)) {
            $file = fopen($filename, "w");
            if ($file) 
                fclose($file);
        }

        $content = file_get_contents($filename);
        $config = json_decode($content, true);
        $key = $config['GITHUB_API_KEY'];
        return $key;
    }

    /**
     * @throws Exception
     */
    public function __construct(
        Stringable|string $owner = null,
        Stringable|string $repo = null,
        array $labels = null,
        $body = null,
        Stringable|Node|string $title = null,
        Stringable|Node|string $climbId = null,
        string $state = "open",
        string $url = null, 
    ) {
        if($url == null){
            $url = 'https://api.github.com/repos/' . $owner . '/' . $repo . '/issues/' . (string) $climbId;
        } else{
            $url = substr($url, 0, strpos($url, '?'));
            $url = $url . '/' . (string) $climbId;
        }

        $params = [
            'state' => $state
        ];
        if($title != null){
            $params['title'] = $title;
        } 
        if($body != null){
            $params['body'] = $body;
        }
        if($labels != null){
            $params['labels'] = $labels;
        }
        $context = [];

        $context = [
            'http' => [
                'method' => 'PATCH',
                'header' => [
                    'User-Agent:curl/8.5.0',
                    'Accept: */*',
                    'Authorization: Bearer ' . self::getApiKey(),
                    'X-GitHub-Api-Version: 2022-11-28',
                    'Content-Type: application/vnd.github+json'
                ],
                'content' => json_encode($params) 
            ]
        ];

        parent::__construct(
            auto_dispatch: false,
            format_in: format::json,
            target_in: target::stream,
            target_out: target::variable,
            input: [$url],
            metadata: [['context' => $context]]
        );
    }
}

