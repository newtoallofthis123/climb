<?php

global $reviewBody;

$reviewBody = <<<HTML
<style>
    #PastBudgets table {
        width: 50%;
    }

    #Budget form {
        width: 50%;
    }
</style>
<div class="p-3">
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
        <form action="">
            <div class="mb-4">
                <label for="basic-url" class="form-label">
                    Time Intended
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4">
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
                    <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon5 basic-addon6">
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
                    <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon7 basic-addon8">
                    <span class="input-group-text" id="basic-addon7">
                        <i class="bi bi-currency-dollar me-2"></i> Dollars
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
HTML;