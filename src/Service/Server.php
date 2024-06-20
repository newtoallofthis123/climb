<?php

/*
 * The Server class is the main class that handles the server side of the application.
 * It is responsible for processing the data and returning the response.
 */

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\path;
use Approach\Render\HTML;
use Approach\Scope;
use Approach\Service\flow;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use ClimbUI\Imprint\Body\IssueBody;
use ClimbUI\Render\OysterMenu\Oyster;
use ClimbUI\Render\OysterMenu\Pearl;
use ClimbUI\Render\OysterMenu\Visual;
use Exception;
use ClimbUI\Render\TabsInfo;
use ClimbUI\Render\TabsForm;

class Server extends Service
{
    public static array $registrar = [];

    /**
     * @param mixed $action
     * @return array<int,array<string,array<string,string>>>
     * @throws Exception
     */
    public static function Save(mixed $action): array
    {
        $title = $action['Climb']['title'];

        $climbForm = $action['Climb'];
        $surveyForm = $action['Survey'];
        $describeForm = $action['Describe'];

        $requirements = [];
        foreach ($climbForm as $key => $value) {
            if (str_starts_with($key, 'requirement')) {
                $requirements[] = $value;
            }
        }


        $interests = [];
        $obstructions = [];
        foreach ($surveyForm as $key => $value) {
            if (str_starts_with($key, 'interest')) {
                $interests[] = $value;
            }
            if (str_starts_with($key, 'obstruction')) {
                $obstructions[] = $value;
            }
        }

        $time_intent = $action['Time']['time_intent'];
        $energy_intent = $action['Time']['energy_req'];
        $resources_intent = $action['Time']['resources'];

        $work = $action['Work']['document_progress'];
        $budget_res = $describeForm['budget_res'];

        $d_interests = [];
        $hazards = [];
        foreach ($describeForm as $key => $value) {
            if (str_starts_with($key, 'd_interest')) {
                $d_interests[] = $value;
            }
            if (str_starts_with($key, 'hazard')) {
                $hazards[] = $value;
            }
        }

        $div = new HTML(tag: 'div', classes: ['p-3']);
        $div[] = new HTML(tag: 'h1', content: 'Form Submitted!');
        $div[] = $main = new HTML(tag: 'main');
        $main[] = $climbRes = new HTML(tag: 'div');
        $climbRes->content = 'Title: ' . $title . '<br>Requirements: ' . implode(', ', $requirements);
        $main[] = $surveyRes = new HTML(tag: 'div');
        $surveyRes->content = 'Interests: ' . implode(', ', $interests) . '<br>Obstructions: ' . implode(', ', $obstructions);
        $main[] = $timeRes = new HTML(tag: 'div');
        $timeRes->content = 'Time Intent: ' . $time_intent . '<br>Energy Intent: ' . $energy_intent . '<br>Resources Intent: ' . $resources_intent;
        $main[] = $workRes = new HTML(tag: 'div');
        $workRes->content = 'Work: ' . $work . '<br>Budget: ' . $budget_res;
        $main[] = $describeRes = new HTML(tag: 'div', content: $describeForm);
        $describeRes->content = 'Interests: ' . implode(', ', $d_interests) . '<br>Hazards: ' . implode(', ', $hazards);

        // add json
        $res = [];
        $res['Climb'] = ['title' => $title, 'requirements' => $requirements];
        $res['Survey'] = ['interests' => $interests, 'obstructions' => $obstructions];

        $res['Work'] = ['document_progress' => $work];
        $res['Describe'] = ['budget_res' => $budget_res, 'd_interests' => $d_interests, 'hazards' => $hazards];
        $res['parent_id'] = $action['climb_id'];

        $path_to_project = __DIR__ . '/';
        $path_to_approach = __DIR__ . '/support/lib/approach/';
        $path_to_support = __DIR__ . '//support//';
        $scope = new Scope(
            path: [
                path::project->value => $path_to_project,
                path::installed->value => $path_to_approach,
                path::support->value => $path_to_support,
            ],
        );

        $fileDir = $scope->GetPath(path::pattern);
        // FIXME: Fix the need for removing the ..
        $fileDir = str_replace('/../', '', $fileDir);

        //        $imp = new Imprint(
        //            imprint: 'body.xml',
        //            imprint_dir: $fileDir,
        //        );

        //        $prepSuccess = $imp->Prepare();

        //          $imp->Mint('IssueBody');

        $body = new IssueBody(tokens: [
            'Body' => $div->render(),
            'Metadata' => json_encode($res),
        ]);

        $service = new Issue(
            'newtoallofthis123',
            'test_for_issues',
            labels: ['climb-payload'],
            body: $body->render(),
            title: $title
        );

        $service->dispatch();

        return [[
            'REFRESH' => ['#result' => '<p>' . 'Saved!' . '</p>'],
        ]];
    }

    /**
     * @param mixed $context
     * @return array<int,array<string,array>>
     */
    public static function View(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $owner = $context['owner'];
        $repo = $context['repo'];
        $labels = ['climb-payload'];

        $fetcher = new Github(
            $owner,
            $repo,
            $labels
        );
        $results = $fetcher->dispatch()[0];

        $jsonFile = self::getIssue($results, $climbId);
        $jsonFile = json_decode($jsonFile['details'], true);

        $jsonFile['Climb']['climb_id'] = $climbId;
        $jsonFile['Climb']['url'] = $fetcher->url;

        $tabsInfo = new TabsInfo($jsonFile);

        $pearls = [];
        $hierarchy = self::getHierarchy($results, $climbId);
        foreach ($hierarchy['children'] as $issue) {
            $visual1 = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number']);
            $visual = new HTML('div');
            $visual->content = <<<HTML
                <div
                class = "control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "View" } }'
                    data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
                    {$visual1}
                </div>
HTML;

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        $back = <<<HTML
                <div
                class = "control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "Hierarchy" } }'
                    data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "{$climbId}", "owner": "$owner", "repo": "$repo" }'>
                   <i class="expand fa fa-angle-left"></i> 
                </div>
       HTML;

        return [[
            'REFRESH' => [
                '#some_content > div' => $tabsInfo->render(),
                '.Toolbar > .active > ul' => $oyster->render(),
                '.backBtn > div' => $back,
            ],
        ]];
    }

    /**
     * @param mixed $context
     * @return array<int,array<string,array>>
     */
    public static function Edit(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $url = $context['url'];
        $fetcher = new Github(url: $url);
        $results = $fetcher->dispatch()[0];
        $result = null;
        foreach ($results as $issue) {
            if ($issue['number'] == $climbId) {
                $result = $issue;
                break;
            }
        }
        if ($result == null) {
            return [[
                'REFRESH' => [$context['_response_target'] => '<p>' . json_encode($result) . '</p>'],
            ]];
        }
        $result = json_decode(json_encode($result), true);
        $details = json_decode($result['details'], true);

        $tabsForm = new TabsForm($details);

        return [[
            'REFRESH' => [$context['_response_target'] => '<div>' . $tabsForm . '</div>'],
        ]];
    }

    public static function getBaseMenu(mixed $results): array
    {
        $parents = [];
        // The setup loop, prepare the parents and details
        foreach ($results as $issue) {
            $issueVars = json_decode(json_encode($issue), true);
            // append the parent to the parent's array
            if (in_array('root', $issueVars['labels'], true)) {
                $parents[] = $issueVars;
            }
        }
        return $parents;
    }

    public static function getIssue(mixed $results, mixed $id)
    {
        foreach ($results as $issue) {
            if ($issue['number'] == $id) {
                return $issue;
            }
        }
        return null;
    }

    public static function getHierarchy(mixed $results, mixed $climbId): array
    {
        $final = ['parent' => [], 'children' => []];
        foreach ($results as $issue) {
            $issueVars = json_decode(json_encode($issue), true);
            $details = json_decode($issueVars['details'], true);
            $details['number'] = $climbId;
            if ($details['parent_id'] == $climbId) {
                $final['children'][] = $issueVars;
            } else if ($issueVars['number'] == $climbId) {
                $final['parent'] = $issueVars;
            }
        }

        return $final;
    }

    public static function getMenu(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $owner = $context['owner'];
        $repo = $context['repo'];
        $labels = ['climb-payload'];

        $fetcher = new Github(
            $owner,
            $repo,
            $labels
        );
        $results = $fetcher->dispatch()[0];

        $pearls = [];
        $hierarchy = self::getHierarchy($results, $climbId);

        if(in_array('root', $hierarchy['parent']['labels'], true)){
            return self::makeMenu($context);
        }

        foreach ($hierarchy['children'] as $issue) {
            $visual1 = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number']);
            $visual = new HTML('div');
            $visual->content = <<<HTML
                <div
                class = "control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "View" } }'
                    data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
                    {$visual1}
                </div>
HTML;

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        return [[
            'REFRESH' => [$context['_response_target'] => $oyster->render()],
        ]];
    }

    public static function makeMenu(mixed $context): array
    {
        $owner = $context['owner'];
        $repo = $context['repo'];
        $labels = ['climb-payload'];

        $fetcher = new Github(
            $owner,
            $repo,
            $labels
        );
        $results = $fetcher->dispatch()[0];

        $pearls = [];
        $base = self::getBaseMenu($results);
        foreach ($base as $issue) {
            $visualContent = new Visual($issue['title'], $issue['number']);
            $visual = new HTML('div');
            $visual->content = <<<HTML
                <div
                class = "control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "View" } }'
                    data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
                    {$visualContent}
                </div>
HTML;

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        return [[
            'REFRESH' => [
                $context['_response_target'] => $oyster->render(),
            ],
        ]];
    }

    public function __construct(
        flow    $flow = flow::in,
        bool    $auto_dispatch = false,
        ?format $format_in = format::json,
        ?format $format_out = format::json,
        ?target $target_in = target::stream,
        ?target $target_out = target::stream,
                $input = [Service::STDIN],
                $output = [Service::STDOUT],
        mixed   $metadata = [],
    )
    {
        self::$registrar['Climb']['Save'] = static function ($context) {
            return self::Save($context);
        };
        self::$registrar['Climb']['Edit'] = static function ($context) {
            return self::Edit($context);
        };
        self::$registrar['Climb']['View'] = static function ($context) {
            return self::View($context);
        };
        self::$registrar['Climb']['Menu'] = static function ($context) {
            return self::makeMenu($context);
        };
        self::$registrar['Climb']['Hierarchy'] = static function ($context) {
            return self::getMenu($context);
        };
        parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
    }

    public function Process(?array $payload = null): void
    {
        $payload = $payload ?? $this->payload;

        $action = $payload[0]['support'];

        foreach ($payload[0] as $intent) {
            foreach ($intent as $instruction) {
                foreach ($instruction as $command => $context) {
                    if ($command === 'Climb') {
                        $this->payload = self::$registrar[$command][$context]($action);
                    }
                }
            }
        }
    }
}
