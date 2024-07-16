<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;


class TabsInfo extends HTML
{
    // Function that returns the information of a climb
    public function __construct(array $json)
    {
        $data = $json;

        parent::__construct(
            tag: 'div', classes: ['TabsForm', ' Interface']
        );

        $TabBar = new HTML(tag: 'div', classes: ['TabBar']);
        $tabs = [
            '1' => 'Requirements',
            '2' => 'Survey',
            '3' => 'Review',
            '4' => 'Work',
            '5' => 'Describe',
            '6' => 'Adapt',
        ];

        $rightArrow = new HTML(tag: 'i', classes: ['bi ', 'bi-arrow-right ', 'ms-1']);

        foreach ($tabs as $index => $tab)
            $TabBar[] = new HTML(
                tag: 'div',
                classes: ['tab-button'],
                attributes: [
                    'id' => 'tabBtn' . $index,
                ],
                content: $tab . $rightArrow
            );

        $firstNode = new HTML(tag: 'div', classes: ['New']);
        $firstNode[] = $TabBar;

        $this->nodes[] = $editBTN = new HTML(tag: 'div', classes: ['controls']);
        $intentInfo = '{ "_response_target": "#content > div", "parent_id": "' . $data['Climb']['parent_id'] . '", "climb_id": "' . $data['Climb']['climb_id'] . '", "url": "' . $data['Climb']['url'] . '"  }';

        $editBTN->content = <<<HTML
                        <button class="control btn" 
                            data-api="/server.php"
                            data-api-method="POST"
                            data-intent='{ "REFRESH": { "Climb" : "Edit" } }'
                            data-context='$intentInfo'>
                            <i class="bi bi-pencil-square"></i>
                            Edit
                        </button>
            HTML;

        $this->nodes[] = $firstNode;

        $tabBarBody = new HTML(tag: 'div', classes: ['TabContent']);

        $requirements = new HTML(
            tag: 'div',
            classes: ['tab ', 'active ', 'tab1 '],
        );
        $requirements[] = new HTML(tag: 'h3', classes: ['fw-bold '], content: '🎯 Goal: ' . $data['Climb']['title']);
        $requirements[] = new HTML(tag: 'h5', content: 'Requirements');
        $requirements[] = new HTML(tag: 'p', content: 'Tracked with Issue ID: ' . $data['Climb']['climb_id']);
        if ($data['Climb']['parent_id'] != '') {
            $requirements[] = new HTML(tag: 'p', content: 'Parent Issue ID: ' . $data['Climb']['parent_id']);
        }
        $requirements[] = $requirementsList = new HTML(tag: 'ul');
        foreach ($data['Climb']['requirements'] as $requirement) {
            $requirementsList[] = new HTML(tag: 'li', content: $requirement);
        }

        $tabBarBody[] = $requirements;

        $survey = new HTML(
            tag: 'div',
            classes: ['tab ', 'tab2 '],
        );

        $survey[] = new HTML(tag: 'h3', content: '🔍️ Survey');
        $survey[] = new HTML(tag: 'h5', content: 'Interests');
        $survey[] = $surveyList = new HTML(tag: 'ul');
        foreach ($data['Survey']['interests'] as $interest) {
            $surveyList[] = new HTML(tag: 'li', content: $interest);
        }

        $survey[] = new HTML(tag: 'h5', content: 'Obstructions');
        $survey[] = $obstructionsList = new HTML(tag: 'ul');
        foreach ($data['Survey']['obstructions'] as $obstruction) {
            $obstructionsList[] = new HTML(tag: 'li', content: $obstruction);
        }

        $tabBarBody[] = $survey;

        $review = new HTML(
            tag: 'div',
            classes: ['tab ', 'tab3 '],
        );


        $review[] = new HTML(tag: 'h3', classes: ['fs-4 ', 'fw-bold'], content: '😊 Review');

        $review[] = new Quantity(amount: 10, unit: 'hours');
        // $review[] = new HTML(tag: 'h5', classes: ['pt-2 ', 'underline'], content: 'Amount | Units');
        $container = new HTML(
            tag: 'div',
            classes: ['d-flex', 'flex-row', 'align-items-center', 'inline-block'],
        );
        
        // Add the numbers as inline elements
        $container->content .= new HTML(
            tag: 'span',
            classes: ['fs-6', 'm-0', 'me-2'], 
            content: $data['Time']['time_intent']
        );

        $container->content .= new HTML(
            tag: 'span',
            content: '<span style="margin-right: 4em;"></span>'
        );
        
        $container->content .= new HTML(
            tag: 'span',
            classes: ['fs-6', 'm-0'],
            content: $data['Time']['energy_req']
        );
        
        $review[] = $container;
        $tabBarBody[] = $review;

        $work = new HTML(
            tag: 'div',
            classes: ['tab ', 'tab4 '],
        );

        $work[] = new HTML(tag: 'h3', content: '🏗️ Work');
        $work[] = new HTML(tag: 'h5', content: 'Document Progress');
        $work[] = new HTML(tag: 'p', content: $data['Work']['document_progress']);
        $work[] = new HTML(tag: 'h5', content: 'Budget');
        $work[] = new HTML(tag: 'p', content: $data['Describe']['budget_res']);

        $tabBarBody[] = $work;

        $describe = new HTML(
            tag: 'div',
            classes: ['tab ', 'tab5 '],
        );

        $describe[] = new HTML(tag: 'h3', content: '📔 Describe');
        $describe[] = new HTML(tag: 'h5', content: 'Interests');
        $describe[] = $describeList = new HTML(tag: 'ul');
        foreach ($data['Describe']['d_interests'] as $d_interest) {
            $describeList[] = new HTML(tag: 'li', content: $d_interest);
        }

        $describe[] = new HTML(tag: 'h5', content: 'Hazards');
        $describe[] = $hazardsList = new HTML(tag: 'ul');
        foreach ($data['Describe']['hazards'] as $hazard) {
            $hazardsList[] = new HTML(tag: 'li', content: $hazard);
        }

        $tabBarBody[] = $describe;

        $tabBarBody[] = $adaptContent = new HTML(
            tag: 'div',
            classes: ['tab ', 'tab6 '],
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

        $this->nodes[] = new HTML(tag: 'div', attributes: ['id' => 'tabs_stuff'], content: $tabBarBody);
    }
}

