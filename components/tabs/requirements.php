<?php

namespace ClimbUI;

global $requirementsBody;

$requirementsBody = <<<HTML
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
<div class="Interface InterfaceContent" id="Requirements">
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
            <input type="text" name="requirements" class="form-control" placeholder="Add a Requirement" aria-label="requirement" aria-describedby="addon-wrapping-1">
        </div>
    </form>
    <div>
        <button type="button" class="btn btn-primary ms-3" id="add-input-group">Add New Requirement</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-input-group">Remove Last Requirement</button>
    </div>
</div>
<script>
    $(document).ready(function() {
        let group_no = 1;
        
        // interface can help with this too btw
        $("#add-input-group").click(function() {
            group_no++;
            $("#Requirements form").append(`
                <div id="input-group-${group_no}" class="input-group flex-nowrap">
                    <span class="input-group-text" id="addon-wrapping-${group_no}">${group_no}</span>
                    <input type="text" class="form-control" placeholder="Add a Requirement" aria-label="requirement" aria-describedby="addon-wrapping-${group_no}">
                </div>
            `);
        });

        $("#remove-input-group").click(function() {
            if (group_no > 1) {
                $(`#input-group-${group_no}`).remove();
                group_no--;
            }
        });
    })
</script>
HTML;
