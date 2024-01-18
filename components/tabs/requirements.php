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
<div id="Requirements">
    <form class="p-3">
        <div class="mb-3">
            <label for="title" class="form-label">Set a Destination</label>
            <input type="text" class="form-control" id="title" placeholder="Become a billionaire">
        </div>
        <div id="input-group-1" class="input-group flex-nowrap">
            <span class="input-group-text" id="addon-wrapping-1">1</span>
            <input type="text" class="form-control" placeholder="Add a Requirement" aria-label="requirement" aria-describedby="addon-wrapping-1">
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