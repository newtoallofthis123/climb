<?php

namespace ClimbUI\Component;

require_once __DIR__ . '/../../support/lib/vendor/autoload.php';

use Approach\Render\HTML;

global $tabsForm;

$tabsForm = new HTML(tag: 'div', classes: ['TabsForm']); // this is good?

function getTabsForm(): HTML
{
    global $tabsForm;
    return $tabsForm;
}

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

$tabsForm = new HTML(tag: 'div', classes: ['Interface']);
$tabsForm[] = $firstNode;



$tabsFormBody = <<<HTML
<style>
    #Requirements form {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .input-group {
        margin-top: 10px;
    }
</style>
<div class="tab tab1 active Interface InterfaceContent" id="Requirements">
    <div class="p-3 pb-0">
        <h4 class="pb-2 fw-bolder">
            1. Decide a Goal
        </h4>
        <p>
            🎯 What's the goal?
        </p>
    </div>
    <form class="p-3 pt-0 Autoform" data-action="Climb">
        <div class="mb-3">
            <label for="title" class="form-label">Set a Destination</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Become a billionaire">
        </div>
        <div id="input-group-1" class="input-group flex-nowrap">
            <span class="input-group-text" id="addon-wrapping-1">1</span>
            <input type="text" name="requirements_1" class="form-control" placeholder="Add a Requirement" aria-label="requirement" aria-describedby="addon-wrapping-1">
        </div>
    </form>
    <div>
        <button type="button" class="btn btn-primary ms-3" id="add-input-group">Add New Requirement</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-input-group">Remove Last Requirement</button>
    </div>
</div>
<style>
    #Survey form {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .input-group {
        margin-top: 10px;
    }
</style>
<div class="tab tab2 Interface InterfaceContent" id="Survey">
    <form class="p-3 Autoform" data-action="Survey">
        <h4 class="pb-3 fw-bolder">
            2. Survey of the Environment
        </h4>
        <div class="mb-3">
            <p>
                🎯 Find the Path of Least Resistance!
            </p>
            <p>
                Note the obstacles, check if the obstacles disqualify the goal, and check if the goal is still worth pursuing.
            </p>
        </div>
        <p>
            Points of Interest for new Destinations
        </p>
        <div id="Interests">
            <div id="input-interest-1" class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping-1">1</span>
                <input type="text" class="form-control" name="interest_1" placeholder="Add a Point of Interest" aria-label="interest" aria-describedby="addon-wrapping-1">
            </div>
        </div>
        <div class="pt-3">
            <button type="button" class="btn btn-primary ms-3" id="add-interest-group">Add New Interest</button>
            <button type="button" class="btn btn-secondary ms-3" id="remove-interest-group">Remove Last Point</button>
        </div>
        <p class="pt-4">
            Points of Concern and Hazards
        </p>
        <div id="input-group-1" class="input-group flex-nowrap">
            <span class="input-group-text" id="addon-wrapping-1">1</span>
            <input type="text" class="form-control" name="obstruction_1" placeholder="Add an obstacle" aria-label="requirement" aria-describedby="addon-wrapping-1">
        </div>
    </form>
    <div>
        <button type="button" class="btn btn-info ms-3" id="add-obstacle">Add New Obstacle</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-obstacle">Remove Last Obstacle</button>
    </div>
    <div class="p-3">
        <p class="fs-5">
            Found a problem?
        </p>
        <button class="btn btn-danger" type="button">
            💀 Give Up
        </button>
    </div>
</div>
<style>
    #PastBudgets table {
        width: 50%;
    }

    #Budget form {
        width: 50%;
    }
</style>
<div class="tab tab3 p-3 Interface InterfaceContent">
    <h4 class="pb-2 fw-bolder">
        3. Review Findings
    </h4>
    <p>
        <span class="fw-bolder">🎯 Goal:</span> Become a billionaire
    </p>
    <div id="PastBudgets">
        <p class="fs-5">
            <span class="fw-bolder">💰 Past Budgets:</span>
        </p>
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th scope="col">Task</th>
                    <th scope="col">Budget</th>
                    <th scope="col">Payee</th>
                    <th scop="col">Comments</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Design Stuff</th>
                    <td>$1200</td>
                    <td>@mdo</td>
                    <td>
                        Cause Design be like that
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Task 2
                    </th>
                    <td>$500</td>
                    <td>@fat</td>
                    <td>
                        Task 2 was eh
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Task 3
                    </th>
                    <td>$1000</td>
                    <td>@twitter</td>
                    <td>
                        Task 3 was good
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="Budget">
        <p class="fs-5">
            <span class="fw-bolder">🕧️ Budget:</span>
        </p>
        <form class="Autoform" data-action="Time">
            <div class="mb-4">
                <label for="basic-url" class="form-label">
                    Time Intended
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" name="time_intent" id="basic-url" aria-describedby="basic-addon3 basic-addon4">
                    <span class="input-group-text" id="basic-addon3">
                        <i class="bi bi-calendar-date me-2"></i> Days
                    </span>
                </div>
            </div>
            <div class="mb-4">
                <label for="basic-url" class="form-label">
                    Energy Requirements 
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" name="energy_req" id="basic-url" aria-describedby="basic-addon5 basic-addon6">
                    <span class="input-group-text" id="basic-addon5">
                        <i class="bi bi-battery-charging me-2"></i> Energy
                    </span>
                </div>
            </div>
            <div class="mb-4">
                <label for="basic-url" class="form-label">
                    Resources 
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" name="resources" id="basic-url" aria-describedby="basic-addon7 basic-addon8">
                    <span class="input-group-text" id="basic-addon7">
                        <i class="bi bi-currency-dollar me-2"></i> Dollars
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="tab tab4 p-3 Interface InterfaceContent">
    <h4 class="pb-2 fw-bolder">
        4. Time To Work
    </h4>
    <p>
        <span class="fw-bolder">🔨 Action:</span> Work on the goal
    </p>
    <form class="Autoform" data-action="Work">
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">
                Document Progress 🪜
            </label>
            <textarea class="form-control" name="document_progress" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
        <div id="passwordHelpBlock" class="form-text">
            Continue to work on the goal and document your progress here.
        </div>
    </form>
</div>
<div class="tab tab5 p-3 Interface InterfaceContent">
    <h4 class="pb-2 fw-bolder">
        5. Describe your Work
    </h4>
    <p>
        <span class="fw-bolder">📔 Reflection:</span> Describe your work
    </p>
    <form class="Describe" data-action="Describe">
        <div class="mb-3">
            <label for="budget-reality" class="form-label">
                Budget: Expectations vs Reality
            </label>
            <select class="form-select" name="budget_res" aria-label="Default select example" aria-describedby="budget-reality">
                <option selected>Choose an option</option>
                <option value=" 1">
                    Budget Met Expectations
                </option>
                <option value="2">
                    Budget Exceeded Expectations
                </option>
                <option value="3">
                    Low Budget
                </option>
            </select>
        </div>
        <p>
            Points of Interest for new Destinations
        </p>
        <div id="InterestsD">
            <div id="input-interest-d-1" class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping-1">1</span>
                <input type="text" class="form-control" name="d_interest_1" placeholder="Add a Point of Interest" aria-label="interest" aria-describedby="addon-wrapping-1">
            </div>
        </div>
        <div class="pt-3">
            <button type="button" class="btn btn-primary ms-3" id="add-interest-d-group">Add New Interest</button>
            <button type="button" class="btn btn-secondary ms-3" id="remove-interest-d-group">Remove Last Point</button>
        </div>
        <p class="pt-4">
            Points of Concern and Hazards
        </p>
        <div id="Hazards">
            <div id="input-hazard-1" class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping-1">1</span>
                <input type="text" class="form-control" name="hazard_1" placeholder="Add a Point of Hazard" aria-label="interest" aria-describedby="addon-wrapping-1">
            </div>
        </div>
    </form>
    <div class="pt-3">
        <button type="button" class="btn btn-primary ms-3" id="add-hazard-group">Add New Hazard</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-hazard-group">Remove Last Hazard</button>
    </div>
</div>
<div class="tab tab6 p-3">
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
</div>
<div class="controls">
    <button
    type="button"
    class="visual control btn btn-secondary mt-3"
    data-api="/server.php"
    data-role='autoform'
    data-api-method="POST"
    data-intent='{ "REFRESH": { "Climb" : "Save" } }'
    data-context='{ "_response_target": "#result"}'
    >
        Click Me!
    </button>
</div>
HTML;

$tabsForm[] = new HTML(tag: 'div', content: $tabsFormBody, attributes: ['id' => 'tabs_stuff']);
