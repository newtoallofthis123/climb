<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\Node;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use Exception;
use Stringable;

class Issue extends Service
{
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
        return $config['GITHUB_API_KEY'];
    }

    /**
     * @throws Exception
     */
    public function __construct(
        Stringable|string $owner,
        Stringable|string $repo,
        array $labels,
        $body = null,
        Stringable|Node|string $title = null,
    ) {
        $url = 'https://api.github.com/repos/' . $owner . '/' . $repo . '/issues';

        $context = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'User-Agent:curl/8.5.0',
                    'Accept: */*',
                    'Authorization: Bearer ' . self::getApiKey(),
                    'X-GitHub-Api-Version: 2022-11-28',
                    'Content-Type: application/vnd.github+json'
                ],
                'content' => json_encode([
                    'title' => $title,
                    'body' => $body,
                    'labels' => $labels
                ])
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
