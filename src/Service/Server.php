<?php

/*
 * The Server class is the main class that handles the server side of the application.
 * It is responsible for processing the data and returning the response.
 */

namespace ClimbUI\Service;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';
require_once __DIR__ . '/../Component/form.php';
require_once __DIR__ . '/../Component/view.php';

use Approach\Render\HTML;
use Approach\Service\flow;
use Approach\Service\format;
use Approach\Service\Service;
use Approach\Service\target;
use ClimbUI\Service\Github;
use ClimbUI\Component;

class Server extends Service
{
    public static $registrar = [];

    /**
     * @return array<int,array<string,array<string,string>>>
     * @param mixed $action
     */
    public static function New(mixed $action): array
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
        $div[] = $climbRes = new HTML(tag: 'div');
        $climbRes->content = 'Title: ' . $title . '<br>Requirements: ' . implode(', ', $requirements);
        $div[] = $surveyRes = new HTML(tag: 'div');
        $surveyRes->content = 'Interests: ' . implode(', ', $interests) . '<br>Obstructions: ' . implode(', ', $obstructions);
        $div[] = $timeRes = new HTML(tag: 'div');
        $timeRes->content = 'Time Intent: ' . $time_intent . '<br>Energy Intent: ' . $energy_intent . '<br>Resources Intent: ' . $resources_intent;
        $div[] = $workRes = new HTML(tag: 'div');
        $workRes->content = 'Work: ' . $work . '<br>Budget: ' . $budget_res;
        $div[] = $describeRes = new HTML(tag: 'div', content: $describeForm);
        $describeRes->content = 'Interests: ' . implode(', ', $d_interests) . '<br>Hazards: ' . implode(', ', $hazards);

        // add json
        $res = [];
        $res['requirements'] = $requirements;
        $res['interests'] = $interests;
        $res['obstructions'] = $obstructions;
        $res['time_intent'] = $time_intent;
        $res['energy_intent'] = $energy_intent;
        $res['resources_intent'] = $resources_intent;
        $res['work'] = $work;
        $res['budget_res'] = $budget_res;
        $res['d_interests'] = $d_interests;
        $res['hazards'] = $hazards;

        return [[
            'REFRESH' => ['#result' => '<p>' . json_encode($res) . '</p>'],
        ]];
    }

    /**
     * @return string
     * @param mixed $query
     */
    public static function dataMapper($query): string
    {
        $mapper = [
            'cool_one' => 'm1',
            'second_one' => 'm2',
            'millionaire' => 'm3',
        ];

        return $mapper[$query];
    }

    /**
     * @return array<int,array<string,array>>
     * @param mixed $action
     */
    public static function View(mixed $action): array
    {
        $climbId = $action['climb_id'];
        $fileName = self::dataMapper($climbId) . '.json';

        $jsonFile = file_get_contents(__DIR__ . '/../Resource/' . $fileName);
        $jsonFile = json_decode($jsonFile, true);

        $jsonFile['Climb']['climb_id'] = $climbId;

        $tabsInfo = Component\getTabsInfo($jsonFile);

        return [[
            'REFRESH' => ['#some_content > div' => $tabsInfo->render()],
        ]];
    }

    /**
     * @return array<int,array<string,array>>
     * @param mixed $action
     */
    public static function Edit($action): array
    {
        $climbId = $action['climb_id'];
        $fileName = self::dataMapper($climbId) . '.json';

        $jsonFile = file_get_contents(__DIR__ . '/../Resource/' . $fileName);
        $jsonFile = json_decode($jsonFile, true);

        $tabsForm = Component\getTabsForm($jsonFile);

        return [[
            'REFRESH' => ['#some_content > div' => $tabsForm->render()],
        ]];
    }

    /**
     * @return array<int,array<string,array<string,string>>>
     * @param mixed $action
     */
    public static function Ran($action): array
    {
        return [[
            'REFRESH' => ['#some_content > div' => '<div>Ran</div>'],
        ]];
    }

    /**
     * @param mixed $context
     * @return array<int,array<string,array<string,string>>>
     */
    public static function getIssues($context): array
    {
        $owner = $context['owner'] ?? 'TheApproach';
        $repo = $context['repo'] ?? 'Approach';
        $labels = $context['labels'] ?? ['climb-payload'];

        $fetcher = new Github(
            $owner,
            $repo,
            $labels
        );
        $result = $fetcher->dispatch();

        return [[
            'REFRESH' => ['#some_content > div' => '<p>' . json_encode($result) . '</p>'],
        ]];
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
        bool $register_connection = true
    ) {
        self::$registrar['Climb']['Save'] = function ($context) {
            return self::New($context);
        };
        self::$registrar['Climb']['Edit'] = function ($context) {
            return self::Edit($context);
        };
        self::$registrar['Climb']['View'] = function ($context) {
            return self::View($context);
        };
        self::$registrar['Climb']['Ran'] = function ($context) {
            return self::Ran($context);
        };
        self::$registrar['Climb']['Issues'] = function ($context) {
            return self::getIssues($context);
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
                    if ($command == 'Climb') {
                        $this->payload = self::$registrar[$command][$context]($action);
                    }
                }
            }
        }
    }
}
