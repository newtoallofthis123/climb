<?php

namespace ClimbUI\Render;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

/*
 * Function that returns the form of a climb that can be used for a new climb or editing an existing one
 */

class TabsForm extends HTML
{
    public function __construct(array $json)
    {
        $data = $json;
        parent::__construct(
            tag: 'div',
            classes: ['TabsForm', ' Interface']
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

        $rightArrow = new HTML(tag: 'i', classes: ['bi ', 'bi-arrow-right']);

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

        $this->nodes[] = $firstNode;

        $this->nodes[] = $formBody = new HTML(tag: 'div', attributes: ['id' => 'tabs_stuff']);

        $formBody[] = $requirementsForm = new HTML(tag: 'div', classes: ['tab ', 'tab1 ', 'active ', 'Interface ', 'InterfaceContent ', ' p-3', ' pb-0'], attributes: ['id' => 'Requirements']);
        $requirementsForm[] = new HTML(tag: 'h4', content: '1. Decide a Goal');
        $requirementsForm[] = new HTML(tag: 'p', content: "🎯 What's the goal?");
        $requirementsForm[] = $reqForm = new HTML(tag: 'form', classes: ['p-3 ', 'pt-0 ', 'Autoform'], attributes: ['data-action' => 'Climb']);
        $reqForm[] = $inputTitle = new HTML(tag: 'div', classes: ['mb-3']);
        $reqForm[] = $parent = new HTML(tag: 'div');
        $parent[] = new HTML(tag: 'label', content: 'Parent Id');
        $parent[] = new HTML(tag: 'input', attributes: ['type' => 'text', 'name' => 'parent_id', 'value' => $data['Climb']['parent_id'], 'placeholder' => 'Parent ID']);
        $inputTitle[] = new HTML(tag: 'label', content: 'Set a Destination');
        $inputTitle[] = new HTML(tag: 'input', attributes: ['type' => 'text', 'name' => 'title', 'id' => 'title', 'value' => $json['Climb']['title'], 'placeholder' => 'Become a billionaire']);

        foreach ($data['Climb']['requirements'] as $key => $value) {
            $reqForm[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group '], attributes: ['id' => 'input-group-' . $key]);
            $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'addon-wrapping-' . $key], content: $key);
            // check if $key is in $data['Climb']['read_only']
            if(isset($data['Climb']['read_only']) && in_array($key, $data['Climb']['read_only'])){
                $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'requirement_' . $key, 'value' => $value, 'placeholder' => 'Add a Requirement', 'aria-label' => 'requirement', 'aria-describedby' => 'addon-wrapping-' . $key, 'readonly' => 'readonly']);
            } else {
                $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'requirement_' . $key, 'value' => $value, 'placeholder' => 'Add a Requirement', 'aria-label' => 'requirement', 'aria-describedby' => 'addon-wrapping-' . $key]);
            }
        }

        $requirementsForm[] = $div = new HTML(tag: 'div');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-primary '], attributes: ['id' => 'add-input-group'], content: 'Add New Requirement');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-secondary '], attributes: ['id' => 'remove-input-group'], content: 'Remove Last Requirement');

        $formBody[] = $reviewForm = new HTML(tag: 'div', classes: ['tab ', 'tab3 ', 'p-3 ', 'Interface ', 'InterfaceContent']);

        $reviewForm[] = new HTML(tag: 'h4', classes: ['pb-2 ', 'fw-bolder'], content: '3. Review Findings');
        $reviewForm[] = $p = new HTML(tag: 'p', content: '<span class="fw-bolder">🎯 Goal:</span> ' . $data['Climb']['title']);
        $reviewForm[] = $div = new HTML(tag: 'div', attributes: ['id' => 'Budgets']);

        $div[] = new HTML(tag: 'p', classes: ['fs-5'], content: '<span class="fw-bolder">🕧️ Budget:</span>');
        $div[] = $budgetForm = new HTML(tag: 'form', classes: ['Autoform'], attributes: ['data-action' => 'Time']);

        $budgetForm[] = $budgetSection = new HTML(tag: 'div', classes: ['mb-4', 'budget-section']);
        $budgetSection[] = $budgetItem = new HTML(tag: 'div', classes:['budget-item']);
        $budgetItem[] = $labels = new HTML(tag: 'div');
        $labels[] = new HTML(tag: 'label', classes: ['form-label'], content: '<span class= "amount">Amount</span><span>|</span><span class= "unit">Units</span>');

        $budgetItem[] = new HTML(tag: 'input', classes: [''], attributes: ['type' => 'text', 'name' => 'budget_item', 'aria-describedby' => 'basic-addon3 basic-addon4', 'value' => $data['Time']['time_intent']]);
        $budgetItem[] = new HTML(tag: 'input', classes: [''], attributes: ['type' => 'text', 'name' => 'budget_unit', 'aria-describedby' => 'basic-addon3 basic-addon4', 'value' => $data['Time']['energy_req']]);

        $budgetSection[] = new HTML(tag: 'button', classes: ['btn', 'btn-primary', 'ms-3', 'control'], attributes: ['type' => 'button', 'data-action' => 'new-budget-item.climb'], content: 'New Amount');
        $budgetSection[] = new HTML(tag: 'button', classes: ['btn', 'btn-primary', 'ms-3', 'control'], attributes: ['type' => 'button', 'data-action' => 'remove-item.climb'], content: 'Remove Last');

        $formBody[] = $surveyForm = new HTML(tag: 'div', classes: ['tab ', 'tab2 ', 'Interface ', 'InterfaceContent'], attributes: ['id' => 'Survey']);
        $surveyForm[] = $surveyFormBody = new HTML(tag: 'form', classes: ['p-3 ', 'Autoform'], attributes: ['data-action' => 'Survey']);

        $surveyFormBody[] = new HTML(tag: 'h4', classes: ['pb-3 ', 'fw-bolder'], content: '2. Survey of the Environment');

        $surveyFormBody[] = new HTML(tag: 'p', content: 'Note the obstacles, check if the obstacles disqualify the goal, and check if the goal is still worth pursuing.');

        $surveyFormBody[] = $p = new HTML(tag: 'p', content: 'Points of Interest for new Destinations');
        $surveyFormBody[] = $div = new HTML(tag: 'div', attributes: ['id' => 'Interests']);
        foreach ($data['Survey']['interests'] as $key => $value) {
            $div[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group '], attributes: ['id' => 'input-interest-' . $key]);
            $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'addon-wrapping-' . $key], content: $key);
            $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'interest_' . $key, 'value' => $value, 'placeholder' => 'Add a Point of Interest', 'aria-label' => 'interest', 'aria-describedby' => 'addon-wrapping-' . $key]);
        }

        $surveyFormBody[] = $div = new HTML(tag: 'div');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-primary '], attributes: ['id' => 'add-interest-group', 'type' => 'reset'], content: 'Add New Interest');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-secondary '], attributes: ['id' => 'remove-interest-group', 'type' => 'reset'], content: 'Remove Last Point');

        $surveyFormBody[] = $p = new HTML(tag: 'p', content: 'Points of Concern and Hazards');
        $surveyFormBody[] = $div = new HTML(tag: 'div', attributes: ['id' => 'Obstructions']);

        foreach ($data['Survey']['obstructions'] as $key => $value) {
            $div[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group '], attributes: ['id' => 'input-group-' . $key]);
            $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'addon-wrapping-' . $key], content: $key);
            $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'obstruction_' . $key, 'value' => $value, 'placeholder' => 'Add an obstacle', 'aria-label' => 'requirement', 'aria-describedby' => 'addon-wrapping-' . $key]);
        }

        $surveyForm[] = $div = new HTML(tag: 'div');

        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-info '], attributes: ['id' => 'add-obstacle', 'type' => 'button'], content: 'Add New Obstacle');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-secondary '], attributes: ['id' => 'remove-obstacle', 'type' => 'button'], content: 'Remove Last Obstacle');

        $surveyForm[] = $div = new HTML(tag: 'div', classes: []);

        $div[] = new HTML(tag: 'p', classes: ['fs-5'], content: 'Found a problem?');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-danger'], attributes: ['type' => 'button'], content: '💀 Give Up');

        $formBody[] = $reviewForm = new HTML(tag: 'div', classes: ['tab ', 'tab3 ', 'p-3 ', 'Interface ', 'InterfaceContent'], attributes: ['id' => 'Budgets']);

        $reviewForm[] = new HTML(tag: 'h4', content: '3. Review Findings');
        $reviewForm[] = $div = new HTML(tag: 'div');

        $div[] = new HTML(tag: 'p', classes: ['fs-5'], content: '<span>🕧️ Budget:</span>');
        $div[] = $budgetForm = new HTML(tag: 'form', classes: ['Autoform'], attributes: ['data-action' => 'Time']);

        $budgetForm[] = $inputTime = new HTML(tag: 'div', classes: ['mb-4']);

        $inputTime[] = new HTML(tag: 'label', classes: ['form-label'], content: 'Time Intended');
        $inputTime[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group']);

        $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'time_intent', 'aria-describedby' => 'basic-addon3 basic-addon4', 'value' => $data['Time']['time_intent']]);
        $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'basic-addon3'], content: '<i class="bi bi-calendar-date me-2"></i> Days');

        $budgetForm[] = $inputEnergy = new HTML(tag: 'div', classes: ['mb-4']);

        $inputEnergy[] = new HTML(tag: 'label', classes: ['form-label'], content: 'Energy Requirements');
        $inputEnergy[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group']);

        $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'energy_req', 'aria-describedby' => 'basic-addon5 basic-addon6', 'value' => $data['Time']['energy_req']]);
        $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'basic-addon5'], content: '<i class="bi bi-battery-charging me-2"></i> Energy');

        $budgetForm[] = $inputResources = new HTML(tag: 'div', classes: ['mb-4']);

        $inputResources[] = new HTML(tag: 'label', classes: ['form-label'], content: 'Resources');
        $inputResources[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group']);

        $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'resources', 'aria-describedby' => 'basic-addon7 basic-addon8', 'value' => $data['Time']['resources']]);
        $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'basic-addon7'], content: '<i class="bi bi-currency-dollar me-2"></i> Dollars');

        $formBody[] = $workForm = new HTML(tag: 'div', classes: ['tab ', 'tab4 ', 'p-3 ', 'Interface ', 'InterfaceContent']);
        $workForm[] = new HTML(tag: 'h4', content: '4. Time To Work');
        $workForm[] = $p = new HTML(tag: 'p', content: '<span>🔨 Action:</span> Work on the goal');
        $workForm[] = $workFormBody = new HTML(tag: 'form', classes: ['Autoform'], attributes: ['data-action' => 'Work']);

        $workFormBody[] = $inputProgress = new HTML(tag: 'div', classes: ['mb-3']);
        $inputProgress[] = new HTML(tag: 'textarea', classes: ['form-control'], attributes: ['name' => 'document_progress', 'id' => 'exampleFormControlTextarea1', 'rows' => '3'], content: $data['Work']['document_progress']);
        $workFormBody[] = $p = new HTML(tag: 'div', classes: ['form-text'], content: 'Continue to work on the goal and document your progress here.');

        $formBody[] = $describeForm = new HTML(tag: 'div', classes: ['tab ', 'tab5 ', 'Interface ', 'InterfaceContent']);

        $describeForm[] = new HTML(tag: 'h4', content: '5. Describe your Work');
        $describeForm[] = $p = new HTML(tag: 'p', content: '<span>📔 Reflection:</span> Describe your work');
        $describeForm[] = $describeFormBody = new HTML(tag: 'form', classes: ['Autoform'], attributes: ['data-action' => 'Describe']);
        $describeFormBody[] = $inputBudget = new HTML(tag: 'div', classes: ['mb-3']);

        $inputBudget[] = new HTML(tag: 'label', classes: ['form-label'], content: 'Budget: Expectations vs Reality');
        $inputBudget[] = $select = new HTML(tag: 'select', classes: ['form-select'], attributes: ['name' => 'budget_res', 'aria-label' => 'Default select example', 'aria-describedby' => 'budget-reality']);

        $select[] = new HTML(tag: 'option', attributes: ['selected'], content: 'Choose an option');

        $map = [
            '1' => 'Budget Met Expectations',
            '2' => 'Budget Exceeded Expectations',
            '3' => 'Low Budget'
        ];

        foreach ($map as $key => $value) {
            $select[] = new HTML(tag: 'option', attributes: ['value' => $key], content: $value);
        }

        $describeFormBody[] = $p = new HTML(tag: 'p', content: 'Points of Interest for new Destinations');
        $describeFormBody[] = $div = new HTML(tag: 'div', attributes: ['id' => 'InterestsD']);
        foreach ($data['Describe']['d_interests'] as $key => $value) {
            $div[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group '], attributes: ['id' => 'input-interest-d-' . $key]);
            $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'addon-wrapping-' . $key], content: $key);
            $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'd_interest_' . $key, 'value' => $value, 'placeholder' => 'Add a Point of Interest', 'aria-label' => 'interest', 'aria-describedby' => 'addon-wrapping-' . $key]);
        }

        $describeFormBody[] = $div = new HTML(tag: 'div');

        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-primary '], attributes: ['id' => 'add-interest-d-group', 'type' => 'button'], content: 'Add New Interest');
        $div[] = new HTML(tag: 'button', classes: ['btn ', 'btn-secondary '], attributes: ['id' => 'remove-interest-d-group', 'type' => 'button'], content: 'Remove Last Point');

        $describeFormBody[] = $p = new HTML(tag: 'p', content: 'Points of Concern and Hazards');
        $describeFormBody[] = $div = new HTML(tag: 'div', attributes: ['id' => 'Hazards']);

        foreach ($data['Describe']['hazards'] as $key => $value) {
            $div[] = $inputGroup = new HTML(tag: 'div', classes: ['input-group '], attributes: ['id' => 'input-hazard-' . $key]);
            $inputGroup[] = new HTML(tag: 'span', classes: ['input-group-text'], attributes: ['id' => 'addon-wrapping-' . $key], content: $key);
            $inputGroup[] = new HTML(tag: 'input', classes: ['form-control'], attributes: ['type' => 'text', 'name' => 'hazard_' . $key, 'value' => $value, 'placeholder' => 'Add a Point of Hazard', 'aria-label' => 'interest', 'aria-describedby' => 'addon-wrapping-' . $key]);
        }

        $this->nodes[] = new HTML(tag: 'style', content: '#Survey form { display: flex; flex-direction: column; justify-content: space-between; } .input-group { margin-top: 10px; }');

        $this->nodes[] = $adapt = new HTML(tag: 'div', classes: ['tab ', 'tab6 ', ]);

        $adapt[] = new HTML(tag: 'h4', content: '6. Adapt from Finding');
        $adapt[] = new HTML(tag: 'p', content: '<span>🔧 Adapt:</span> Adapt from the findings');
        $adapt[] = $formAdapt = new HTML(tag: 'div', classes: ['btn-group ', 'btn-group-lg'], attributes: ['role' => 'group', 'aria-label' => 'Basic mixed styles example']);

        $formAdapt[] = new HTML(tag: 'button', classes: ['btn ', 'btn-warning'], content: '<i class="bi bi-arrow-repeat"></i> Iterate');
        $formAdapt[] = new HTML(tag: 'button', classes: ['btn ', 'btn-success'], content: '<i class="bi bi-sign
    post-split"></i> Branch');

        $submitContent = '';
        if(isset($data['save']) && $data['save'] == 'true'){
            $submitContent = <<<HTML
                <div class="controls">
                <button
                    type="button"
                    class="visual control btn btn-success mt-3"
                    data-api="/server.php"
                    data-role='autoform'
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "Update" } }'
                    data-context='{ "_response_target": "#result", "climb_id": "{$data['Climb']['climb_id']}", "owner": "newtoallofthis123", "repo": "test_for_issues", "save": "true" }'
                >
                    Save
                </button>
                </div>
            HTML;

        } else{
            $submitContent = <<<HTML
                <div class="controls">
                <button
                    type="button"
                    class="visual control btn btn-success mt-3"
                    data-api="/server.php"
                    data-role='autoform'
                    data-api-method="POST"
                    data-intent='{ "REFRESH": { "Climb" : "Update" } }'
                    data-context='{ "_response_target": "#result", "climb_id": "{$data['Climb']['climb_id']}", "owner": "newtoallofthis123", "repo": "test_for_issues" }'
                >
                    Save
                </button>
                </div>
            HTML;

        }

        $this->nodes[] = new HTML(tag: 'div', content: $submitContent);
    }
}
