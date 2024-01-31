<?php

global $describeBody;

$describeBody = <<<HTML
<div class="p-3">
    <h4 class="pb-2 fw-bolder">
        5. Describe your Work
    </h4>
    <p>
        <span class="fw-bolder">📔 Reflection:</span> Describe your work
    </p>
    <form action="">
        <div class="mb-3">
            <label for="budget-reality" class="form-label">
                Budget: Expectations vs Reality
            </label>
            <select class="form-select" aria-label="Default select example" aria-describedby="budget-reality">
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
        <div id="Hazards">
            <div id="input-hazard-1" class="input-group flex-nowrap">
                <span class="input-group-text" id="addon-wrapping-1">1</span>
                <input type="text" class="form-control" placeholder="Add a Point of Hazard" aria-label="interest" aria-describedby="addon-wrapping-1">
            </div>
        </div>
    </form>
    <div class="pt-3">
        <button type="button" class="btn btn-primary ms-3" id="add-hazard-group">Add New Hazard</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-hazard-group">Remove Last Hazard</button>
    </div>
</div>
<script>
    $(document).ready(function() {
        let group_no = 1;
        $("#add-interest-group").click(function() {
            group_no++;
            $("#Interests").append(`
                <div id="input-interest-${group_no}" class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping-${group_no}">${group_no}</span>
                    <input type="text" class="form-control" placeholder="Add a Point of Interest" aria-label="interest" aria-describedby="addon-wrapping-${group_no}">
                </div>
            `);
        });

        $("#remove-interest-group").click(function() {
            if (group_no > 1) {
                $(`#input-interest-${group_no}`).remove();
                group_no--;
            }
        });

        let hazard_group_no = 1;
        $("#add-hazard-group").click(function() {
            hazard_group_no++;
            $("#Hazards").append(`
                <div id="input-hazard-${hazard_group_no}" class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping-${hazard_group_no}">${hazard_group_no}</span>
                    <input type="text" class="form-control" placeholder="Add a Point of Interest" aria-label="interest" aria-describedby="addon-wrapping-${hazard_group_no}">
                </div>
            `);
        });

        $("#remove-hazard-group").click(function() {
            if (hazard_group_no > 1) {
                $(`#input-hazard-${hazard_group_no}`).remove();
                hazard_group_no--;
            }
        });
    })
</script>
HTML;