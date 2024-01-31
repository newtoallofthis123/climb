<?php

global $surveyBody;

$surveyBody = <<<HTML
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
<div id="Survey">
    <form class="p-3">
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
                <input type="text" class="form-control" placeholder="Add a Point of Interest" aria-label="interest" aria-describedby="addon-wrapping-1">
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
            <input type="text" class="form-control" placeholder="Add an obstacle" aria-label="requirement" aria-describedby="addon-wrapping-1">
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
<script>
    $(document).ready(function() {
        let group_no = 1;
        $("#add-obstacle").click(function() {
            group_no++;
            $("#Survey form").append(`
            <div id="input-group-${group_no}" class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping-${group_no}">${group_no}</span>
                <input type="text" class="form-control" placeholder="Add a Requirement" aria-label="requirement" aria-describedby="addon-wrapping-${group_no}">
            </div>
            `);
        });

        $("#remove-obstacle").click(function() {
            if (group_no > 1) {
                $(`#input-group-${group_no}`).remove();
                group_no--;
            }
        });
    })
</script>
HTML;