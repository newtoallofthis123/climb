<?php

/*
 * The Server class is the main class that handles the server side of the application.
 * It is responsible for processing the data and returning the response.
 */

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;
use Approach\Service\flow;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use Approach\path;
use Approach\Scope;
use ClimbUI\Imprint\Body\IssueBody;
use ClimbUI\Render\OysterMenu\Oyster;
use ClimbUI\Render\OysterMenu\Pearl;
use ClimbUI\Render\OysterMenu\Visual;
use ClimbUI\Render\TabsForm;
use ClimbUI\Render\TabsInfo;
use Exception;

class Server extends Service
{
    public static array $registrar = [];

    /**
     * @param mixed $action
     * @return array<int,array<string,array<string,string>>>
     * @throws Exception
     */
    public static function Update(mixed $action): array
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
        $res['parent_id'] = $action['Climb']['parent_id'];

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

        $service = new UpdateIssue(
            'newtoallofthis123',
            'test_for_issues',
            labels: ['climb-payload'],
            body: $body->render(),
            title: $title,
            climbId: $action['climb_id'],
        );

        $service->dispatch();

        return [
            'REFRESH' => ['#result' => '<p>' . 'Updated!' . '</p>'],
        ];
    }

    public static function New(mixed $context): array
    {
        $details = [];
        $details['Climb']['parent_id'] = $context['parent_id'];

        $form = new TabsForm($details);

        return [
            'REFRESH' => [
                $context['_response_target'] => $form->render(),
            ],
        ];
    }

    /**
     * @param mixed $climbId
     * @return string
     */
    static function getBtn(mixed $climbId): string
    {
        $newBtn = <<<HTML
                         <button
                            class="control btn btn-primary current-state ms-2 animate__animated animate__slideInDown"
                            id="newButton"
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "New" } }'
                            data-context='{ "_response_target": "#some_content > div", "parent_id": "$climbId"}'
                        >
                            New
                        </button>
            HTML;
        return $newBtn;
    }

    /**
     * @param mixed $context
     * @return array<int,array<string,array>>
     */
    public static function View(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $parentId = $context['parent_id'];
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
        $jsonFile['Climb']['parent_id'] = $parentId;

        $tabsInfo = new TabsInfo($jsonFile);

        $pearls = [];
        $hierarchy = self::getHierarchy($results, $climbId);
        $base = $hierarchy['children'];
        foreach ($base as $issue) {
            $visual1 = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number']);
            $visual = new HTML('div');
            $visual->content = <<<HTML
                    <div
                    class = "control" 
                        data-api="/server.php"
                        data-api-method="POST"
                        data-intent='{ "REFRESH": { "Climb" : "View" } }'
                        data-context='{ "_response_target": "{$context['_response_target']}", "parent_id": "$climbId",  "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
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
                         data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "$climbId", "owner": "$owner", "repo": "$repo" }'>
                        <i class="expand fa fa-angle-left"></i> 
                     </div>
            HTML;

        $breadBtn = new Visual($jsonFile['Climb']['title'], $jsonFile['Climb']['climb_id']);
        $breadRender = <<<HTML
                 <div
                    class = "control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "Hierarchy" } }'
                    data-context='{ "_response_target": "{$context['_response_target']}", "climb_id": "$climbId", "owner": "$owner", "repo": "$repo" }'>
                    {$breadBtn}
                </div>
            HTML;

        // Check it the parent has no children
        if (count($hierarchy['children']) == 0) {
            return [
                'REFRESH' => [
                    '#some_content > div' => $tabsInfo->render(),
                    '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                    '#newButton' => self::getBtn($climbId),
                ],
            ];
        }

        return [
            'REFRESH' => [
                '#some_content > div' => $tabsInfo->render(),
                '.Toolbar > .active > ul' => $oyster->render(),
                '.backBtn > div' => $back,
                '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                '#newButton' => self::getBtn($climbId)
            ],
            'APPEND' => [
                '.breadcrumbs' => '<li>' . $breadRender . '</li>',
            ]
        ];
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
            return [
                'REFRESH' => [$context['_response_target'] => '<p>' . json_encode($result) . '</p>'],
            ];
        }
        $result = json_decode(json_encode($result), true);
        $details = json_decode($result['details'], true);
        $details['Climb']['parent_id'] = $context['parent_id'];
        $details['Climb']['climb_id'] = $context['climb_id'];

        $tabsForm = new TabsForm($details);

        return [
            'REFRESH' => [$context['_response_target'] => $tabsForm->render()],
        ];
    }

    /**
     * @param mixed $results
     * @return array
     */
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

    /*
     * Takes a list of issues, and a parent climbs id and returns the parent and children
     * The children are assigned to the parent through a simple O(N) loop
     * @param mixed $issues
     * @param mixed $parentClimbId
     *
     * @return array
     */
    public static function getHierarchy(mixed $issues, mixed $parentClimbId): array
    {
        $final = ['parent' => [], 'children' => []];
        foreach ($issues as $issue) {
            $issueVars = json_decode(json_encode($issue), true);
            $details = json_decode($issueVars['details'], true);
            $details['number'] = $parentClimbId;
            if ($details['parent_id'] == $parentClimbId) {
                $final['children'][] = $issueVars;
            } else if ($issueVars['number'] == $parentClimbId) {
                $final['parent'] = $issueVars;
            }
        }

        return $final;
    }

    /**
     * @return array|array<int,array<string,array>>
     */
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

        if (in_array('root', $hierarchy['parent']['labels'], true)) {
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
                                    data-context='{ "_response_target": "{$context['_response_target']}", "parent_id": "$climbId", "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
                                    {$visual1}
                                </div>
                HTML;

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        return [
            'REFRESH' => [
                $context['_response_target'] => $oyster->render(),
                '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                '#newButton' => self::getBtn($climbId)
            ],
        ];
    }

    /**
     * @return array<int,array<string,array>>
     */
    public static function makeMenu(mixed $context): array
    {
        $climbId = $context['climbId'];
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
                        data-context='{ "_response_target": "{$context['_response_target']}", "parent_id": "$climbId", "climb_id": "{$issue['number']}", "owner": "$owner", "repo": "$repo" }'>
                        {$visualContent}
                    </div>
                HTML;

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }
        $oyster = new Oyster(pearls: $pearls);

        return [
            'REFRESH' => [
                $context['_response_target'] => $oyster->render(),
                '#newButton' => self::getBtn($climbId)
            ],
        ];
    }

    public function __construct(
        flow $flow = flow::in,
        bool $auto_dispatch = false,
        ?format $format_in = format::json,
        ?format $format_out = format::json,
        ?target $target_in = target::stream,
        ?target $target_out = target::stream,
        $input = [Service::STDIN],
        $output = [Service::STDOUT],
        mixed $metadata = [],
    ) {
        self::$registrar['Climb']['Update'] = static function ($context) {
            return self::Update($context);
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
        self::$registrar['Climb']['New'] = static function ($context) {
            return self::New($context);
        };
        parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
    }

    function processIntents($intent): array
    {
        $result = [];
        if (
            is_array($intent) &&
            !isset($intent['support']) &&
            !isset($intent['command'])
        ) {
            foreach ($intent as $i) {
                $predicated_result = $this->processIntent($i);
                $result = array_merge($result, $predicated_result);
            }
            return $result;
        } else {
            return $this->processIntent($intent);
        }
    }

    /**
     * Process a generic intent
     *
     * @param array<int,mixed> $intent
     */
    public function processIntent(array $intent): array
    {
        $result = [];
        $context = $intent['support'];
        $command = $intent['command'];
        foreach ($command as $predicate => $action) {
            $scope = key($action);
            $call = $action[$scope];

            if (!isset(self::$registrar[$scope][$call])) {
                $result = [
                    'APPEND' => ['#APPROACH_DEBUG_CONSOLE' => '<br /><p>' . 'Unmatched intent! <br />' . var_export($intent, true) . '</p><br />']
                ];
            } else {
                $result = self::$registrar[$scope][$call]($context);
            }
        }
        return $result;
    }

    public function Process(?array $payload = null): void
    {
        $payload = $payload ?? $this->payload;

        foreach ($payload as $index => $intent) {
            $this->payload[$index] = $this->processIntents($intent);
        }
    }
}
