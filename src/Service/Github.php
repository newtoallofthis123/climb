<?php

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;

class Github extends Service
{
    public function __construct(
        $owner,
        $repo,
        $labels,
    ) {
        $url = 'https://api.github.com/repos/' . $owner . '/' . $repo . '/issues?labels=' . implode(',', $labels);
        $context = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent:curl/8.5.0',
                    'Accept: */*',
                ],
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

    // TODO: Make it to array
    // FIXME: Make it actually work

    /*
     * @param string $body
     */
    public function getIssueMeta($body): string
    {
        return '{"meta": "data"}';
    }

    /**
     * @param mixed $issue
     * @return array<string,mixed>
     */
    public function processIssue($issue): array
    {
        $res = [];
        $res['id'] = $issue['id'];
        $res['title'] = $issue['title'];
        $res['url'] = $issue['repository_url'];
        $res['user_login'] = $issue['user']['login'];
        $res['user_avatar'] = $issue['user']['avatar_url'];
        $res['is_admin'] = $issue['author_association'] === 'OWNER' || $issue['author_association'] === 'MEMBER';
        $res['metadata'] = $this->getIssueMeta($res['body']);
        // TODO: Add all of the required fields
        // TODO: Add the label fields

        return $res;
    }

    public function Process(?array $payload = null): void
    {
        $payload = $this->payload ?? $payload;
        $res = [];
        foreach ($payload as &$p) {
            foreach ($p as $issue) {
                $res[] = $this->processIssue($issue);
            }
            $p = $res;
        }
        $this->payload = $payload;
    }
}
