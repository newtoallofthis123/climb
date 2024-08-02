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
use ClimbUI\Imprint\Climb\Editor;
use ClimbUI\Imprint\Climb\Viewer;
use ClimbUI\Imprint\GitHub\Issue as GitHubIssue;
use ClimbUI\Imprint\Settings\Settings;
use ClimbUI\Render\OysterMenu\Oyster;
use ClimbUI\Render\OysterMenu\Pearl;
use ClimbUI\Render\Intent;
use ClimbUI\Render\UIInput;
use ClimbUI\Render\UIList;
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
            if (str_contains($key, 'requirement')) {
                $requirements[] = $value;
            }
        }

        $interests = [];
        $obstructions = [];
        foreach ($surveyForm as $key => $value) {
            if (str_contains($key, 'survey')) {
                $interests[] = $value;
            } else if (str_contains($key, 'obstruction')) {
                $obstructions[] = $value;
            } else{
                $obstructions[] = $value;
            }
        }

        $plans = [];
        $i = 1;
        $curr = [];
        foreach ($action['Plan'] as $key => $value) {
            if (str_ends_with($key, 'quantity')) {
                $curr[] = $value;
            } elseif (str_ends_with($key, 'units')) {
                $curr[] = $value;
            }
            if ($i == 0) {
                $plans[] = $curr;
                $curr = [];
                $i = 1;
            } else {
                $i--;
            }
        }

        $work = $action['Work']['document_progress'];
        $budget_res = $describeForm['budget_res'];

        $d_interests = [];
        $hazards = [];
        foreach ($describeForm as $key => $value) {
            if (str_starts_with($key, 'interestsd')) {
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
        $main[] = $plan = new HTML(tag: 'div');
        $plan->content = 'Plans: ' . implode(', ', $plans);
        $sep[] = $plan->render();
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
        $res['Plan'] = ['plans' => $plans];
        $res['Work'] = ['document_progress' => $work];
        $res['Describe'] = ['budget_res' => $budget_res, 'd_interests' => $d_interests, 'hazards' => $hazards];
        $res['parent_id'] = $action['Climb']['parent_id'];

        return [$res, $div, $sep];
    }

    /**
     * @return array<string,null|string|array|bool>
     */
    public static function getConfig(): array
    {
        $filename = __DIR__ . '/../../config.json';
        if (!file_exists($filename)) {
            $file = fopen($filename, "w");
            if ($file) 
                fclose($file);
        }

        $content = file_get_contents($filename);
        $config = json_decode($content, true);
        $owner = $config['CLIMBSUI_OWNER'];
        $repo = $config['CLIMBSUI_REPO'];
        $key = $config['GITHUB_API_KEY'];

        if($owner == '' || $repo == '' || $key == ''){
            // TODO: Add Redirect Logic
        }

        return ['owner' => $owner, 'repo' => $repo, 'key' => $key];
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
        if ($action['save'] == 'true') {
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

        $labels = [];
        $labels[] = $action['Climb']['labels'];
        $labels = array_merge($labels, explode(',' ,$action['Climb']['other_labels']));
        // check if a parent_id is set
        if (!isset($action['Climb']['parent_id'])) {
            $labels[] = 'root';
        }
        if (!array_key_exists('climb-payload', $labels)) {
            $labels[] = 'climb-payload';
        }

        if ($toSave) {
            $service = new Issue(
                $config['owner'],
                $config['repo'],
                labels: $labels,
                body: $body->render(),
                title: $title,
            );

            $service->dispatch();
        } else {
            $service = new UpdateIssue(
                $config['owner'],
                $config['repo'],
                labels: $labels,
                body: $body->render(),
                title: $title,
                climbId: $action['climb_id'],
            );
            $service->dispatch();
        }

        return [
            'REFRESH' => [$action['_response_target'] => '<div>' . json_encode($labels) . '</div>'],
        ];
    }

    /**
     * @return array<string,array>
     */
    public static function Close(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $url = $context['url'];

        // find all the issues whose parent is the climb id
        $fetcher = new Github(
            url: $url,
        );
        $results = $fetcher->dispatch()[0];

        $hierarchy = self::getHierarchy($results, $climbId);

        $service = new UpdateIssue(
            climbId: $climbId,
            state: 'closed',
            url: $url,
        );

        $service->dispatch();

        foreach ($hierarchy['children'] as $issue) {
            $s = new UpdateIssue(climbId: $issue['number'], state: 'closed', url: $url);
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

        $config = self::getConfig();

        if ($context['parent_id'] != '') {
            $fetcher = new Github(
                $owner,
                $repo,
                $labels
            );
            $results = $fetcher->dispatch()[0];

            $jsonFile = self::getIssue($results, $climbId);
            $jsonFile = json_decode($jsonFile['details'], true);
            // get the requirements and put it into $jsonFile['Climb']['read_only']
            foreach ($jsonFile['Climb']['requirements'] as $key => $value) {
                $jsonFile['Climb']['read_only'][] = $key;
            }

            $jsonFile['Climb']['parent_id'] = $climbId;
            unset($jsonFile['Climb']['title']);
            $details['Climb'] = $jsonFile['Climb'];
        }

        $requirementsForm = new HTML(tag: 'div');

        foreach ($details['Climb']['requirements'] as $key => $requirement) {
            if (isset($details['Climb']['read_only']) && in_array($key, $details['Climb']['read_only'])) {
                $requirementsForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
                $inputGroup[] = new UIInput('requirements' . $key, $requirement, readonly: true);
                $inputGroup[] = new HTML(tag: 'button', classes: ['remove'], content: '<i class="bi bi-x"></i>');
            }
        }

        $update = new HTML(tag: 'div', classes: ['controls']);
        $update[] = new Intent(
            tag: 'button',
            classes: ['control', ' btn', ' btn-sucess'],
            api: '/server.php',
            role: 'autoform',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Update']],
            context: ['_response_target' => '#result', 'climb_id' => '', 'save' => 'true', 'parent_id' => $details['Climb']['parent_id'], 'owner' => $config['owner'], 'repo' => $config['repo']],
            content: 'Save'
        );

        $tokens = [
            'Title' => new UIInput('title', $details['Climb']['title'] ?? ''),
            'Parent' => new UIInput('parent_id', $details['Climb']['parent_id'] ?? ''),
            'Requirements' => $requirementsForm ?? '',
            'Survey' => '',
            'Obstacles' => '',
            'Plan' => '',
            'Progress' => new HTML(tag: 'textarea', content: $details['Work']['document_progress'], attributes: ['name' => 'document_progress']),
            'InterestsD' => '',
            'Hazards' => '',
            'Adapt' => 'TODO',
            'Update' => $update,
        ];

        $form = new Editor(tokens: $tokens);

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
    static function getBtn(mixed $climbId, mixed $owner, mixed $repo, mixed $labels = [])
    {
        $btn = new Intent(
            tag: 'button',
            id: 'newButton',
            classes: ['control', ' btn', ' btn-primary', ' current-state'],
            content: 'New',
            context: ['_response_target' => '#content> div', 'parent_id' => $climbId, 'owner' => $owner, 'repo' => $repo],
            intent: ['REFRESH' => ['Climb' => 'New']],
            api: '/server.php',
            method: 'POST'
        );

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
        $fileDir = str_replace('/../', '', $fileDir);

        $requirements = new HTML(tag: 'div', classes: ['New']);
        $requirements[] = new HTML(tag: 'h4', content: '🎯 Goal: ' . $jsonFile['Climb']['title']);
        $requirements[] = new HTML(tag: 'p', content: 'Tracked with Issue ID: ' . $jsonFile['Climb']['climb_id']);
        $requirements[] = new HTML(tag: 'p', content: 'Parent ID: ' . $jsonFile['Climb']['parent_id']);
        $requirements[] = new HTML(tag: 'h4', content: 'Requirements');
        $requirements[] = new UIList($jsonFile['Climb']['requirements']);

        $edit = new HTML(tag: 'div', classes: ['controls']);
        $edit[] = new Intent(
            tag: 'button',
            api: '/server.php',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Edit']],
            classes: ['control', ' btn', ' btn-primary'],
            content: 'Edit',
            context: ['_response_target' => '#content > div', 'parent_id' => $jsonFile['Climb']['parent_id'], 'climb_id' => $jsonFile['Climb']['climb_id'], 'url' => $jsonFile['Climb']['url']]
        );

        $somePlans = [];
        foreach ($jsonFile['Plan'] as $amount) {
            foreach ($amount as $plan) {
                $somePlans[] = implode(' ', $plan);
            }
        }

        $adapt = new HTML(tag: 'div', classes: ['controls']);
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Adapt',
            classes: ['control', ' btn', ' btn-success', ' current-state', ' ms-2'],
            context: ['_response_target' => '#content > div', 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $parentId],
            intent: ['REFRESH' => ['Climb' => 'Copy']],
            api: '/server.php',
            method: 'POST',
        );
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Branch',
            classes: ['control', ' btn', ' btn-warning', ' current-state', ' ms-2'],
            context: ['_response_target' => '#content > div', 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $parentId],
            intent: ['REFRESH' => ['Climb' => 'New']],
            api: '/server.php',
            method: 'POST',
        );
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Terminate',
            classes: ['control', ' btn', ' btn-danger', ' current-state', ' ms-2'],
            context: ['_response_target' => '#result', 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $parentId],
            intent: ['REFRESH' => ['Climb' => 'Close']],
            api: '/server.php',
            method: 'POST',
        );

        $tokens = [
            'Edit' => $edit,
            'Requirements' => $requirements,
            'Interests' => new UIList($jsonFile['Survey']['interests']),
            'Obstructions' => new UIList($jsonFile['Survey']['obstructions']),
            'Review' => new UIList($somePlans),
            'Progress' => $jsonFile['Work']['document_progress'],
            'InterestsD' => new UIList($jsonFile['Describe']['d_interests']),
            'Hazards' => new UIList($jsonFile['Describe']['hazards']),
            'Adapt' => $adapt->render(),
        ];

        $tabsInfo = new Viewer($tokens);

        $pearls = [];
        $hierarchy = self::getHierarchy($results, $climbId);
        $base = $hierarchy['children'];
        foreach ($base as $issue) {
            $curr_climbid = $issue['number'];
            $target = $context['_response_target'];

            $visual = new Intent(
                tag: 'div',
                classes: ['control', ' visual'],
                context: ['_response_target' => $target, 'climb_id' => $curr_climbid, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $climbId],
                intent: ['REFRESH' => ['Climb' => 'View']],
                api: '/server.php',
                method: 'POST',
            );
            $visual->content .= '<i class="bi bi-chevron-right"></i>';
            $visual->content .= self::getIssue($results, $issue['number'])['title'];

            $pearl = new Pearl($visual);
            $pearls[] = $pearl;
        }

        $oyster = new Oyster(pearls: $pearls);

        $back = new Intent(
            tag: 'div',
            classes: ['control'],
            context: ['_response_target' => $context['_response_target'], 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo],
            intent: ['REFRESH' => ['Climb' => 'Hierarchy']],
            api: '/server.php',
            method: 'POST',
            content: '<i class="bi bi-chevron-left"></i>'
        );

        $breadRender = new Intent(
            tag: 'div',
            classes: ['control', ' visual'],
            context: ['_response_target' => $context['_response_target'], 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $parentId],
            intent: ['REFRESH' => ['Climb' => 'View']],
            api: '/server.php',
            method: 'POST',
            content: '<i class="bi bi-chevron-right"></i>' . $hierarchy['parent']['title']
        );

        $copyRender = new Intent(
            tag: 'button',
            id: 'newTemplate',
            classes: ['control', ' btn', ' btn-success', ' current-state', ' ms-2'],
            context: ['_response_target' => '#content > div', 'climb_id' => $climbId, 'owner' => $owner, 'repo' => $repo, 'parent_id' => $parentId],
            intent: ['REFRESH' => ['Climb' => 'Copy']],
            api: '/server.php',
            method: 'POST',
            content: 'Copy'
        );

        // Check it the parent has no children
        if (count($hierarchy['children']) == 0) {
            return [
                'REFRESH' => [
                    '#content > div' => $tabsInfo->render(),
                    // '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                    '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
                    '#newTemplate' => $copyRender->render(),
                ],
                'APPEND' => [
                    '.breadcrumbs' => '<li>' . $breadRender . '</li>',
                ]
            ];
        }

        return [
            'REFRESH' => [
                '#content > div' => $tabsInfo->render(),
                '.Toolbar > .active > ul' => $oyster->render(),
                '.backBtn > div' => $back->render(),
                // '#menuButtonText > span' => '<span>' . $hierarchy['parent']['title'] . '</span>',
                '#newButton' => self::getBtn($climbId, $owner, $repo, $labels),
                '#newTemplate' => $copyRender->render(),
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

        $isRoot = 'false';
        if(in_array('root', $result['labels'])){
            $isRoot = 'true';
        }

        $config = self::getConfig();

        if ($result == null) {
            return [
                'REFRESH' => [$context['_response_target'] => '<p>' . json_encode($result) . '</p>'],
            ];
        }
        $result = json_decode(json_encode($result), true);
        $details = json_decode($result['details'], true);
        $details['Climb']['parent_id'] = $context['parent_id'];
        $details['Climb']['climb_id'] = $context['climb_id'];

        $requirementsForm = new HTML(tag: 'div');

        foreach ($details['Climb']['requirements'] as $key => $requirement) {
            $requirementsForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('requirements' . $key, $requirement);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $surveyForm = new HTML(tag: 'div');

        foreach ($details['Survey']['interests'] as $key => $interest) {
            $surveyForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('interests' . $key, $interest);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $obstaclesForm = new HTML(tag: 'div');

        foreach ($details['Survey']['obstructions'] as $key => $obstruction) {
            $obstaclesForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('obstacle' . $key, $obstruction);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $reviewForm = new HTML(tag: 'div');
        foreach ($details['Plan'] as $amount) {
            foreach ($amount as $key => $quantity) {
                $reviewForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
                $inputGroup[] = new UIInput('review' . $key . '-quantity', $quantity[0] ?? '');
                $inputGroup[] = new UIInput('review' . $key . '-units', $quantity[1] ?? '');
                $inputGroup[] = new HTML(tag: 'button', classes: ['remove_review', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
            }
        }

        $interestsDForm = new HTML(tag: 'div');
        foreach ($details['Describe']['d_interests'] as $key => $interest) {
            $interestsDForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('interestsd' . $key, $interest);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $hazardsForm = new HTML(tag: 'div');
        foreach ($details['Describe']['hazards'] as $key => $hazard) {
            $hazardsForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('hazards' . $key, $hazard);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $adapt = new HTML(tag: 'div', classes: ['controls']);
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Adapt',
            classes: ['control', ' btn', ' btn-success', ' current-state', ' ms-2'],
            context: ['_response_target' => '#content > div', 'climb_id' => $climbId, 'owner' => $config['owner'], 'repo' => $config['repo'], 'parent_id' => $config['parentId']],
            intent: ['REFRESH' => ['Climb' => 'Copy']],
            api: '/server.php',
            method: 'POST',
        );
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Branch',
            classes: ['control', ' btn', ' btn-warning', ' current-state', ' ms-2'],
            context: ['_response_target' => '#content > div', 'climb_id' => $climbId, 'owner' => $config['owner'], 'repo' => $config['repo'], 'parent_id' => $config['parentId']],
            intent: ['REFRESH' => ['Climb' => 'New']],
            api: '/server.php',
            method: 'POST',
        );
        $adapt[] = new Intent(
            tag: 'button', 
            content: 'Terminate',
            classes: ['control', ' btn', ' btn-danger', ' current-state', ' ms-2'],
            context: ['_response_target' => '#result', 'climb_id' => $climbId, 'owner' => $config['owner'], 'repo' => $config['repo'], 'parent_id' => $config['parentId']],
            intent: ['REFRESH' => ['Climb' => 'Close']],
            api: '/server.php',
            method: 'POST',
        );

        $update = new HTML(tag: 'div', classes: ['controls']);
        $update[] = new Intent(
            tag: 'button',
            classes: ['control', ' btn', ' btn-sucess'],
            api: '/server.php',
            role: 'autoform',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Update']],
            context: ['_response_target' => '#result', 'climb_id' => $details['Climb']['climb_id'], 'parent_id' => $details['Climb']['parent_id'], 'owner' => $config['owner'], 'repo' => $config['repo']],
            content: 'Save'
        );

        $tokens = [
            'Title' => new UIInput('title', $details['Climb']['title']),
            'Parent' => new UIInput('parent_id', $details['Climb']['parent_id'] ?? ''),
            'Requirements' => $requirementsForm,
            'Survey' => $surveyForm,
            'Obstacles' => $obstaclesForm,
            'Plan' => $reviewForm,
            'Progress' => new HTML(tag: 'textarea', content: $details['Work']['document_progress'], attributes: ['name' => 'document_progress']),
            'InterestsD' => $interestsDForm,
            'Hazards' => $hazardsForm,
            'Adapt' => $adapt->render(),
            'Update' => $update,
            'OtherLabels' => new UIInput('other_labels', implode(',',$result['labels'])),
            'IsRoot' => new HTML(tag: 'input', attributes: ['type' => 'checkbox', 'checked' => $isRoot])
        ];

        $tabsForm = new Editor(tokens: $tokens);

        return [
            'REFRESH' => [$context['_response_target'] => $tabsForm->render()],
        ];
    }

    public static function Copy(mixed $context): array
    {
        $climbId = $context['climb_id'];
        $owner = $context['owner'];
        $repo = $context['repo'];
        $labels = ['climb-payload'];

        $config = self::getConfig();

        $fetcher = new Github(
            $owner,
            $repo,
            $labels
        );
        $results = $fetcher->dispatch()[0];
        $issue = self::getIssue($results, $climbId);
        $details = json_decode($issue['details'], true);
        $details['Climb']['parent_id'] = $context['parent_id'];
        $details['Climb']['climb_id'] = $context['climb_id'];

        $requirementsForm = new HTML(tag: 'div');

        foreach ($details['Climb']['requirements'] as $key => $requirement) {
            $requirementsForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('requirements' . $key, placeholder: $requirement);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $surveyForm = new HTML(tag: 'div');

        foreach ($details['Survey']['interests'] as $key => $interest) {
            $surveyForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('interests' . $key, placeholder: $interest);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $obstaclesForm = new HTML(tag: 'div');

        foreach ($details['Survey']['obstructions'] as $key => $obstruction) {
            $obstaclesForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('obstacle' . $key, placeholder: $obstruction);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $reviewForm = new HTML(tag: 'div');
        foreach ($details['Plan'] as $amount) {
            foreach ($amount as $key => $quantity) {
                $reviewForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
                $inputGroup[] = new UIInput('review' . $key . '-quantity', placeholder: $quantity[0] ?? '');
                $inputGroup[] = new UIInput('review' . $key . '-units', placeholder: $quantity[1] ?? '');
                $inputGroup[] = new HTML(tag: 'button', classes: ['remove_review', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
            }
        }

        $interestsDForm = new HTML(tag: 'div');
        foreach ($details['Describe']['d_interests'] as $key => $interest) {
            $interestsDForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('interestsd' . $key, placeholder: $interest);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $hazardsForm = new HTML(tag: 'div');
        foreach ($details['Describe']['hazards'] as $key => $hazard) {
            $hazardsForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-container']);
            $inputGroup[] = new UIInput('hazards' . $key, placeholder: $hazard);
            $inputGroup[] = new HTML(tag: 'button', classes: ['remove', ' control'], attributes: ['data-role' => 'trigger', 'data-action' => 'remove.climb'], content: '<i class="bi bi-x"></i>');
        }

        $update = new HTML(tag: 'div', classes: ['controls']);
        $update[] = new Intent(
            tag: 'button',
            classes: ['control', ' btn', ' btn-sucess'],
            api: '/server.php',
            role: 'autoform',
            method: 'POST',
            intent: ['REFRESH' => ['Climb' => 'Update']],
            context: ['_response_target' => '#result', 'save' => 'true', 'climb_id' => '', 'parent_id' => $details['Climb']['parent_id'], 'owner' => $config['owner'], 'repo' => $config['repo'] ],
            content: 'Save'
        );

        $tokens = [
            'Title' => new UIInput('title', placeholder: $details['Climb']['title'] ?? ''),
            'Parent' => new UIInput('parent_id', $details['Climb']['parent_id'] ?? ''),
            'Requirements' => $requirementsForm,
            'Survey' => $surveyForm,
            'Obstacles' => $obstaclesForm,
            'Plan' => $reviewForm,
            'Progress' => new HTML(tag: 'textarea', content: '', attributes: ['name' => 'document_progress']),
            'InterestsD' => $interestsDForm,
            'Hazards' => $hazardsForm,
            'Adapt' => 'TODO',
            'Update' => $update,
        ];

        $tabsForm = new Editor(tokens: $tokens);
        

        return [
            'REFRESH' => [$context['_response_target'] => $tabsForm->render()],
        ];
    }

    /**
     * Get the base menu of parents
     *
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

    /**
     * @return <missing>|null*/
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

    /**
     * Gets the menu from the provided context
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

            $visual = new Intent(
                tag: 'div',
                classes: ['control', ' visual'],
                api: '/server.php',
                method: 'POST',
                intent: ['REFRESH' => ['Climb' => 'View']],
                attributes: ['data-labels' => implode(',', $issue['labels'])],
                context: ['_response_target' => $target, 'climb_id' => $curr_climbid, 'owner' => $owner, 'repo' => $repo],
            );
            $visual->content .= '<i class="bi bi-magic"></i>';
            $visual->content .= '<label>' . self::getIssue($results, $issue['number'])['title'] . '</label>';
            $visual->content .= '<i class="bi bi-chevron-right"></i>';

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

    /**
     * Makes an oysterMenu based on the provided context
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

            $visual = new Intent(
                tag: 'div',
                classes: ['control', ' visual'],
                api: '/server.php',
                method: 'POST',
                intent: ['REFRESH' => ['Climb' => 'View']],
                attributes: ['data-labels' => implode(',', $issue['labels'])],
                context: ['_response_target' => $target, 'climb_id' => $curr_climbid, 'owner' => $owner, 'repo' => $repo],
            );

            // I wonder if this can be based on color ?
            $icon ='';
            if ( in_array('RED',    $issue['labels']) )  $icon = 'bi-exclamation-circle-fill red';
            if ( in_array('ORANGE', $issue['labels']) )  $icon = 'bi-exclamation-triangle-fill orange';
            if ( in_array('YELLOW', $issue['labels']) )  $icon = 'bi-sun-fill yellow';
            if ( in_array('GREEN',  $issue['labels']) )  $icon = 'bi-bar-chart-steps green';
            if ( in_array('BLUE',   $issue['labels']) )  $icon = 'bi-cup-hot-fill blue';
            if ( in_array('VIOLET', $issue['labels']) )  $icon = 'bi-arrow-through-heart-fill violet';
            
            // or we can have like .yellow, .red, .green ec?

            $visual->content .= '<i class="bi '.$icon.'"></i>';
            $visual->content .= '<label>' . self::getIssue($results, $issue['number'])['title'] . '</label>';
            $visual->content .= '<i class="bi bi-chevron-right"></i>';

            $pearl = new Pearl($visual);

            // is there any way to know the full parent path ??
            // $pearl->attributes['data-pearl'] = root_id.'/'.grandparent_id.'/'.parent_id.'/'.climb_id;

            $pearl->attributes['data-pearl'] = $climbId;
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

    public static function DisplayConfig(mixed $context){
        $filename = __DIR__ . '/../../config.json';
        if (!file_exists($filename)) {
            $file = fopen($filename, "w");
            if ($file) 
                fclose($file);
        }

        $content = file_get_contents($filename);
        $config = json_decode($content, true);

        $settings = new Settings(tokens: [
            'GITHUB_API_KEY' => new UIInput(name: 'GITHUB_API_KEY', value: $config['GITHUB_API_KEY'] ?? getenv('GITHUB_API_KEY') ),
            'CLIMBSUI_OWNER' => new UIInput(name: 'CLIMBSUI_OWNER', value: $config['CLIMBSUI_OWNER'] ?? getenv('CLIMBSUI_OWNER')  ),
            'CLIMBSUI_REPO' => new UIInput(name: 'CLIMBSUI_REPO', value: $config['CLIMBSUI_REPO'] ?? getenv('CLIMBSUI_REPO')),
            'Save' => new Intent(tag: 'button',
                classes: ['control', ' btn', ' btn-sucess'],
                api: '/server.php',
                attributes: ['type' => 'reset'],
                role: 'autoform',
                method: 'POST',
                intent: ['REFRESH' => ['Climb' => 'UpdateSettings']],
                context: ['_response_target' => '#result'],
                content: 'Save'
            )
        ]);

        return [
            'REFRESH' => [
                $context['_response_target'] => $settings->render(),
            ],
        ];
    }

    public static function UpdateSettings(mixed $context){
        $filename = __DIR__ . '/../../config.json';

        if (!file_exists($filename)) {
            $file = fopen($filename, "w");
            if ($file) 
                fclose($file);
        }

        $content = file_get_contents($filename);
        $config = json_decode($content, true);

        $toSave = [];
        $action = $context['Settings'];
        if($action['GITHUB_API_KEY'] != '')
        $toSave['GITHUB_API_KEY'] = $action['GITHUB_API_KEY'];
        else
        $toSave['GITHUB_API_KEY'] = $config['GITHUB_API_KEY'] ?? getenv('GITHUB_API_KEY') ;
        if($action['CLIMBSUI_OWNER'] != '')
        $toSave['CLIMBSUI_OWNER'] = $action['CLIMBSUI_OWNER'];
        else
        $toSave['CLIMBSUI_OWNER'] = $config['CLIMBSUI_OWNER'] ?? getenv('CLIMBSUI_OWNER');
        if($action['CLIMBSUI_REPO'] != '')
        $toSave['CLIMBSUI_REPO'] = $action['CLIMBSUI_REPO'];
        else
        $toSave['CLIMBSUI_REPO'] = $config['CLIMBSUI_REPO'] ?? getenv('CLIMBSUI_REPO');

        file_put_contents($filename, json_encode($toSave));

        return [
            'REFRESH' => [
                $context['_response_target'] => '<div>' . 'Saved!' . '</div>',
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
        self::$registrar['Climb']['Copy'] = static function ($context) {
            return self::Copy($context);
        };
        self::$registrar['Climb']['Close'] = static function ($context) {
            return self::Close($context);
        };
        self::$registrar['Climb']['Settings'] = static function ($context){
          return self::DisplayConfig($context);  
        };
        self::$registrar['Climb']['UpdateSettings'] = static function ($context){
          return self::UpdateSettings($context);  
        };
        parent::__construct($flow, $auto_dispatch, $format_in, $format_out, $target_in, $target_out, $input, $output, $metadata);
    }

    /**
     * @return array<<missing>|array-key,<missing>>|array*/
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
     * @param array $intent
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
