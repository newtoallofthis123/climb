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
     * @throws Exception
     */
    public function __construct(
        Stringable|string $owner,
        Stringable|string $repo,
        array $labels = null,
        $body = null,
        Stringable|Node|string $title = null,
        Stringable|Node|string $climbId = null,
    ) {
        $url = 'https://api.github.com/repos/' . $owner . '/' . $repo . '/issues/' . (string) $climbId;

        $apiKey = getenv('GITHUB_API_KEY');
        if ($apiKey === false) {
            throw new Exception('GITHUB_API_KEY not set');
        }

        $context = [];

        $context = [
            'http' => [
                'method' => 'PATCH',
                'header' => [
                    'User-Agent:curl/8.5.0',
                    'Accept: */*',
                    'Authorization: Bearer ' . $apiKey,
                    'X-GitHub-Api-Version: 2022-11-28',
                    'Content-Type: application/vnd.github+json'
                ],
                'content' => json_encode([
                    'title' => $title,
                    'body' => $body,
                    // 'labels' => $labels
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

