<?php

namespace ClimbUI\Component;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

global $tabsInfo;

$tabsInfo = new HTML(tag: 'div', classes: ['TabsForm']); // this is good?

function getTabsInfo(array $json)
{
    global $tabsInfo;

    $data = $json;

    $TabBar = new HTML(tag: 'div', classes: ['TabBar']);
    $tabs = [
        '1' => 'Requirements',
        '2' => 'Survey',
        '3' => 'Review',
        '4' => 'Work',
        '5' => 'Describe',
        '6' => 'Adapt',
    ];

    $rightArrow = new HTML(tag: 'i', classes: ['bi', 'bi-arrow-right']);

    foreach ($tabs as $index => $tab) $TabBar[] = new HTML(
        tag: 'div',
        classes: ['tab-button'],
        attributes: [
            'id' => 'tabBtn' . $index,
        ],
        content: $tab . $rightArrow
    );

    $firstNode = new HTML(tag: 'div', classes: ['New']);
    $firstNode[] = $TabBar;

    $tabsInfo = new HTML(tag: 'div', classes: ['Interface']);
    
    $tabsInfo[] = $editBTN =  new HTML(tag: 'div', classes: ['controls']);
    $intentInfo = '{ "_response_target": "#some_content > div", "climb_id": "' . $data['Climb']['climb_id'] . '" }';

    $editBTN->content = <<<HTML
                <button class="control" 
                    data-api="/server.php"
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "Edit" } }'
                    data-context='$intentInfo'>
                >
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </button>
    HTML;

    $tabsInfo[] = $firstNode;

    $tabsInfoBody = new HTML(tag: 'div', classes: ['TabContent']);

    $requirements = new HTML(
        tag: 'div',
        classes: ['tab ', 'active ', 'tab1 ', 'pt-4'],
    );
    $requirements[] = new HTML(tag: 'h3', classes: ["fs-4 ", "fw-bold "], content: '🎯 Goal: ' . $data['Climb']['title']);
    $requirements[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Requirements');
    $requirements[] = $requirementsList = new HTML(tag: 'ul');
    foreach ($data['Climb']['requirements'] as $requirement) {
        $requirementsList[] = new HTML(tag: 'li', content: $requirement, classes: ['fs-6']);
    }

    $tabsInfoBody[] = $requirements;

    $survey = new HTML(
        tag: 'div',
        classes: ['tab ', 'tab2 ', 'pt-4'],
    );

    $survey[] = new HTML(tag: 'h3', classes: ['fs-4 ', 'fw-bold'], content: '🔍️ Survey');
    $survey[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Interests');
    $survey[] = $surveyList = new HTML(tag: 'ul');
    foreach ($data['Survey']['interests'] as $interest) {
        $surveyList[] = new HTML(tag: 'li', content: $interest, classes: ['fs-6']);
    }

    $survey[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Obstructions');
    $survey[] = $obstructionsList = new HTML(tag: 'ul');
    foreach ($data['Survey']['obstructions'] as $obstruction) {
        $obstructionsList[] = new HTML(tag: 'li', content: $obstruction, classes: ['fs-6']);
    }

    $tabsInfoBody[] = $survey;

    $review = new HTML(
        tag: 'div',
        classes: ['tab ', 'tab3 ', 'pt-4'],
    );

    $review[] = new HTML(tag: 'h3', classes: ['fs-4 ', 'fw-bold'], content: '😵‍💫 Review');
    $review[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Time Intent');
    $review[] = new HTML(tag: 'p', content: $data['Time']['time_intent'], classes: ['fs-6']);
    $review[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Energy Intent');
    $review[] = new HTML(tag: 'p', content: $data['Time']['energy_req'], classes: ['fs-6']);
    $review[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Resources Intent');
    $review[] = new HTML(tag: 'p', content: $data['Time']['resources'], classes: ['fs-6']);

    $tabsInfoBody[] = $review;

    $work = new HTML(
        tag: 'div',
        classes: ['tab ', 'tab4 ', 'pt-4'],
    );

    $work[] = new HTML(tag: 'h3', classes: ['fs-4 ', 'fw-bold'], content: '🏗️ Work');
    $work[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Document Progress');
    $work[] = new HTML(tag: 'p', content: $data['Work']['document_progress'], classes: ['fs-6']);
    $work[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Budget');
    $work[] = new HTML(tag: 'p', content: $data['Describe']['budget_res'], classes: ['fs-6']);

    $tabsInfoBody[] = $work;

    $describe = new HTML(
        tag: 'div',
        classes: ['tab ', 'tab5 ', 'pt-4'],
    );

    $describe[] = new HTML(tag: 'h3', classes: ['fs-4 ', 'fw-bold'], content: '📔 Describe');
    $describe[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Interests');
    $describe[] = $describeList = new HTML(tag: 'ul');
    foreach ($data['Describe']['d_interests'] as $d_interest) {
        $describeList[] = new HTML(tag: 'li', content: $d_interest, classes: ['fs-6']);
    }

    $describe[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Hazards');
    $describe[] = $hazardsList = new HTML(tag: 'ul');
    foreach ($data['Describe']['hazards'] as $hazard) {
        $hazardsList[] = new HTML(tag: 'li', content: $hazard, classes: ['fs-6']);
    }

    $tabsInfoBody[] = $describe;

    $tabsInfoBody[] = $adaptContent = new HTML(
        tag: 'div',
        classes: ['tab ', 'tab6 ', 'pt-4'],
    );

    $adaptContent->content = <<<HTML
        <h4 class="pb-2 fw-bolder">
            6. Adapt from Findings
        </h4>
        <p>
            <span class="fw-bolder">🦎 Adapt:</span> Adapt from the findings
        </p>
        <div class="btn-group btn-group-lg" role="group" aria-label="Basic mixed styles example">
            <button type="button" class="btn btn-warning">
                <i class="bi bi-arrow-repeat"></i>
                Iterate</button>
            <button type="button" class="btn btn-success">
                <i class="bi bi-signpost-split"></i>
                Branch
            </button>
            <button type="button" class="btn btn-danger">
                <i class="bi bi-arrow-clockwise"></i>
                Terminate
            </button>
        </div>
    HTML;

    $tabsInfo[] = new HTML(tag: 'div', content: $tabsInfoBody, attributes: ['id' => 'tabs_stuff']);

    return $tabsInfo;
}
