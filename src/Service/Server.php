<?php

/*
 * The Server class is the main class that handles the server side of the application.
 * It is responsible for processing the data and returning the response.
 */

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Imprint\Imprint;
use Approach\Render\HTML;
use Approach\Service\flow;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use Approach\path;
use Approach\Scope;
use ClimbUI\Imprint\GitHub\Issue as GitHubIssue;
use ClimbUI\Render\OysterMenu\Oyster;
use ClimbUI\Render\OysterMenu\Pearl;
use ClimbUI\Render\OysterMenu\Visual;
use ClimbUI\Render\TabsForm;
use ClimbUI\Render\TabsInfo;
use ClimbUI\Service\Issue;
use Exception;

class Server extends Service
{
    public static array $registrar = [];

    /**
     * Compiles the form data from the given action array and returns the structured data.
     *
     * @param mixed $action The array containing the form data for Climb, Survey, Describe sections.
     * @return array An array containing the compiled form data structured for different sections.
     */
    public static function compileForm(mixed $action): array
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

        $sep = [];
        $div = new HTML(tag: 'div', classes: ['p-3']);
        $div[] = new HTML(tag: 'h1', content: 'Form Submitted!');
        $div[] = $main = new HTML(tag: 'main');
        $main[] = $climbRes = new HTML(tag: 'div');
        $climbRes->content = 'Title: ' . $title . '<br>Requirements: ' . implode(', ', $requirements);
        $sep[] = $climbRes->render();
        $main[] = $surveyRes = new HTML(tag: 'div');
        $surveyRes->content = 'Interests: ' . implode(', ', $interests) . '<br>Obstructions: ' . implode(', ', $obstructions);
        $sep[] = $surveyRes->render();
        $main[] = $timeRes = new HTML(tag: 'div');
        $timeRes->content = 'Time Intent: ' . $time_intent . '<br>Energy Intent: ' . $energy_intent . '<br>Resources Intent: ' . $resources_intent;
        $sep[] = $timeRes->render();
        $main[] = $workRes = new HTML(tag: 'div');
        $workRes->content = 'Work: ' . $work . '<br>Budget: ' . $budget_res;
        $sep[] = $workRes->render();
        $main[] = $describeRes = new HTML(tag: 'div', content: $describeForm);
        $describeRes->content = 'Interests: ' . implode(', ', $d_interests) . '<br>Hazards: ' . implode(', ', $hazards);
        $sep[] = $describeRes->render();

        // add json
        $res = [];
        $res['Climb'] = ['title' => $title, 'requirements' => $requirements];
        $res['Survey'] = ['interests' => $interests, 'obstructions' => $obstructions];
        $res['Time'] = ['time_intent' => $time_intent, 'energy_req' => $energy_intent, 'resources' => $resources_intent];
        $res['Work'] = ['document_progress' => $work];
        $res['Describe'] = ['budget_res' => $budget_res, 'd_interests' => $d_interests, 'hazards' => $hazards];
        $res['parent_id'] = $action['Climb']['parent_id'];

        return [$res, $div, $sep];
    }

    public static function getConfig(): array{
        $owner = null;
        $repo = null;
        
        if(getenv('CLIMBSUI_OWNER') != "" && getenv('CLIMBSUI_REPO') != "") {
            $owner = getenv('CLIMBSUI_OWNER');
            $repo = getenv('CLIMBSUI_REPO');
        } else{
            echo "Please set the CLIMBSUI_OWNER and CLIMBSUI_REPO environment variables";
            echo $_ENV['CLIMBSUI_REPO'];
            exit;
        }

        return ['owner' => $owner, 'repo' => $repo];
    }

    /**
     * @param mixed $action
     * @return array<int,array<string,array<string,string>>>
     * @throws Exception
     */
    public static function Update(mixed $action): array
    {
        $title = $action['Climb']['title'];
        $compiled = self::compileForm($action);
        $res = $compiled[0];
        $div = $compiled[1];
        $sep = $compiled[2];
        $toSave = false;
        if($action['save'] == 'true'){
            $toSave = true;
        }

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

        // NOTE: Activate only to mint new imprint 
        //
        // $fileDir = $scope->getPath(path::pattern);
        // $fileDir = str_replace('/../', '', $fileDir);
        //
        // $imp = new Imprint(
        //     imprint: 'GitHub.xml',
        //     imprint_dir: $fileDir,
        // );

        // $success = $imp->Prepare();

        // $imp->Mint('Issue');

        $body = new GitHubIssue(tokens: [
            'Requirements' => $sep[0],
            'Survey' => $sep[1],
            'Review' => $sep[2],
            'Work' => $sep[3],
            'Describe' => $sep[4],
            'Adapt' => 'TODO',
            'Metadata' => json_encode($res),
        ]);

        $config = self::getConfig();

        if($toSave){
            $service = new Issue(
                $config['owner'],
                $config['repo'],
                labels: ['climb-payload'],
                body: $body->render(),
                title: $title,
            );

            $service->dispatch();
        } else{
            $service = new UpdateIssue(
                $config['owner'],
                $config['repo'],
                body: $body->render(),
                title: $title,
                climbId: $action['climb_id'],
            );
            $service->dispatch();
        }

        return [
            'REFRESH' => [$action['_response_target'] => '<div>Updated to GitHub</div>'],  
        ];
    }

    public static function Close(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $url = $context['url'];
        
        // find all of the issues whose parent is the climb id
        $fetcher = new Github(
        url: $url,
        );
        $results = $fetcher->dispatch()[0];

        $hierarchy = self::getHierarchy($results, $climbId);

        $service = new UpdateIssue(
            url: $url,
            climbId: $climbId,
            state: 'closed',
        );

        $service->dispatch();

        foreach($hierarchy['children'] as $issue){
            $s = new UpdateIssue(url: $url, climbId: $issue['number'], state: 'closed');
            $s->dispatch();
        }

        return [
            'REFRESH' => [$context['_response_target'] => '<p>' . 'Issue ' . $climbId . ' closed.' . '</p>'],
        ];
    }

    /**
     * Function to create a new climb based on the provided context.
     *
     * @param mixed $context The context containing parent_id, repo, owner, and other climb details
     * @return array Returns an array with a 'REFRESH' key containing the refreshed form data
     */
    public static function New(mixed $context): array
    {
        $details = [];
        $details['Climb']['parent_id'] = $context['parent_id'];
        $details['save'] = 'true';

        $climbId = $context['parent_id'];
        $repo = $context['repo'];
        $owner = $context['owner'];
        $labels = ['climb-payload'];

        if($context['parent_id'] != ''){
            $fetcher = new Github(
                $owner,
                $repo,
                $labels
            );
            $results = $fetcher->dispatch()[0];

            $jsonFile = self::getIssue($results, $climbId);
            $jsonFile = json_decode($jsonFile['details'], true);
            // get the requirements and put it into $jsonFile['Climb']['read_only']
            foreach ($jsonFile['Climb']['requirements'] as $key => $value){
                $jsonFile['Climb']['read_only'][] = $key;
            }

            $jsonFile['Climb']['parent_id'] = $climbId;
            unset($jsonFile['Climb']['title']);
            $details['Climb'] = $jsonFile['Climb'];
        }

        $form = new TabsForm($details);

        return [
            'REFRESH' => [
                $context['_response_target'] => $form->render(),
            ],
        ];
    }

    /**
     * @param mixed $climbId
     * @param mixed $owner
     * @param mixed $repo
     * @param mixed $labels
     * @return string
     */
    static function getBtn(mixed $climbId, mixed $owner, mixed $repo, mixed $labels = []): string
    {
        $btn = new Intent(tag: 'button',
            classes: ['control', ' btn', ' btn-primary', ' current-state', ' animate__animated', ' animate__slideInDown'],
            api: '/server.php',
            method: 'POST',
            id: 'newButton',
            intent: ['REFRESH' => ['Climb' => 'New']],
            context: ['_response_target' => '#content> div', 'parent_id' => $climbId, 'owner' => $owner, 'repo' => $repo],
            content: 'New');

        return $btn->render();
    }

    /**
     * Renders the view based on the provided context, including fetching data and generating necessary components.
     *
     * @param mixed $context The context containing climb_id, parent_id, owner, and repo.
     * @return array The refreshed view content and additional components based on the context.
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
            $curr_climbid = $issue['number'];
            $target = $context['_response_target'];

            $visual = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number'], classes: ['control']);
            $visual->attributes['data-api'] = '/server.php';
            $visual->attributes['data-api-method'] = 'POST';
            $visual->attributes['data-intent'] = '{ &quot;REFRESH&quot;: { &quot;Climb&quot; : &quot;View&quot; } }';
            $visual->attributes['data-context'] = '{ &quot;_response_target&quot;: &quot;' . $target . '&quot;, &quot;climb_id&quot;: &quot;' . $curr_climbid . '&quot;, &quot;owner&quot;: &quot;' . $owner . '&quot;, &quot;repo&quot;: &quot;' . $repo . '&quot; }';

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        $back = new Intent(
            tag: 'div',
            classes: ['control'],
            api: '/server.php',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Hierarchy']],
            context: ['_response_target' => $context['_response_target'], 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo],
        );

        $breadRender = new Intent(
            tag: 'div',
            classes: ['control'],
            api: '/server.php',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Hierarchy']],
            context: ['_response_target' => $context['_response_target'], 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo],
        );

        // Check it the parent has no children
        if (count($hierarchy['children']) == 0) {
            return [
                'REFRESH' => [
                    '#content > div' => $tabsInfo->render(),
                    '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                    '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
                ],
            ];
        }

        return [
            'REFRESH' => [
                '#content > div' => $tabsInfo->render(),
                '.Toolbar > .active > ul' => $oyster->render(),
                '.backBtn > div' => $back,
                '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
            ],
            'APPEND' => [
                '.breadcrumbs' => '<li>' . $breadRender . '</li>',
            ]
        ];
    }

    /**
     * Edits a climb based on the provided context data.
     *
     * @param mixed $context The context data for the climb.
     * @return array An array containing the refreshed content or appended breadcrumbs.
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

    /** 
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

    /** Gets the menu from the provided context
     * 
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
            $curr_climbid = $issue['number'];
            $target = $context['_response_target'];

            $visual = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number'], classes: ['control']);
            $visual->attributes['data-api'] = '/server.php';
            $visual->attributes['data-api-method'] = 'POST';
            $visual->attributes['data-intent'] = '{ &quot;REFRESH&quot;: { &quot;Climb&quot; : &quot;View&quot; } }';
            $visual->attributes['data-context'] = '{ &quot;_response_target&quot;: &quot;' . $target . '&quot;, &quot;climb_id&quot;: &quot;' . $curr_climbid . '&quot;, &quot;owner&quot;: &quot;' . $owner . '&quot;, &quot;repo&quot;: &quot;' . $repo . '&quot; }';

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        return [
            'REFRESH' => [
                $context['_response_target'] => $oyster->render(),
                '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
            ],
        ];
    }

    /** Makes an oysterMenu based on the provided context 
     * 
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
            $curr_climbid = $issue['number'];
            $target = $context['_response_target'];

            $visual = new Visual(self::getIssue($results, $issue['number'])['title'], $issue['number'], classes: ['control']);
            $visual->attributes['data-api'] = '/server.php';
            $visual->attributes['data-api-method'] = 'POST';
            $visual->attributes['data-intent'] = '{ &quot;REFRESH&quot;: { &quot;Climb&quot; : &quot;View&quot; } }';
            $visual->attributes['data-context'] = '{ &quot;_response_target&quot;: &quot;' . $target . '&quot;, &quot;climb_id&quot;: &quot;' . $curr_climbid . '&quot;, &quot;owner&quot;: &quot;' . $owner . '&quot;, &quot;repo&quot;: &quot;' . $repo . '&quot; }';

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }
        $oyster = new Oyster(pearls: $pearls);

        return [
            'REFRESH' => [
                $context['_response_target'] => $oyster->render(),
                '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
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
        self::$registrar['Climb']['Close'] = static function ($context) {
            return self::Close($context);
        };
        parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
    }

    /**
     * @return array<<missing>|array-key,<missing>>|array
     */
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
     * @return <missing>|array<string,array>
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
