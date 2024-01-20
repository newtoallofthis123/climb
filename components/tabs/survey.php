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
        <div class="input-group mb-2" id="input-group-1">
            <div class="input-group-text">
                <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
                <span class="ps-3 pb-1">
                    Disqualify
                </span>
            </div>
            <input type="text" class="form-control" aria-label="Text input with checkbox" id="obstacle-input-1" placeholder="Add an Obstacle">
        </div>
    </form>
    <div>
        <button type="button" class="btn btn-info ms-3" id="add-obstacle">Add New Point</button>
        <button type="button" class="btn btn-secondary ms-3" id="remove-obstacle">Remove Last Point</button>
    </div>
</div>
<script>
    $(document).ready(function() {
        let group_no = 1;
        $("#add-obstacle").click(function() {
            group_no++;
            $("#Survey form").append(`
            <div class="input-group mb-2" id="input-group-${group_no}">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
                    <span class="ps-3 pb-1">
                        Disqualify
                    </span>
                </div>
                <input type="text" class="form-control" aria-label="Text input with checkbox" id="obstacle-input-${group_no}">
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