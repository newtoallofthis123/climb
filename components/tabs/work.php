<?php

global $workBody;

$workBody = <<<HTML
<div class="p-3">
    <h4 class="pb-2 fw-bolder">
        4. Time To Work
    </h4>
    <p>
        <span class="fw-bolder">🔨 Action:</span> Work on the goal
    </p>
    <form action="">
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">
                Document Progress 🪜
            </label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
        <div id="passwordHelpBlock" class="form-text">
            Continue to work on the goal and document your progress here.
        </div>
    </form>
</div>
HTML;