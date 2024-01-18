<?php
$pageTitle = "New Page";
$stage = $_GET["stage"];
if ($stage == "") {
    $stage = 1;
}

require("head.php");
?>
<style>
    .tab {
        display: none;
    }

    .tab.active {
        display: block;
    }

    .tab-button {
        cursor: pointer;
        padding: 2px;
        text-align: center;
        background-color: #ccc;
        border: 1px solid #999;
        width: calc(100% / 6);
    }

    .tab-button.active {
        background-color: #999;
        color: #fff;
    }

    .TabBar {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
</style>

<body>
    <div class="New">
        <div class="TabBar">
            <div class="tab-button" id="tabBtn1">
                Requirements <i class="bi bi-arrow-right"></i>
            </div>
            <div class="tab-button" id="tabBtn2">
                Survey <i class="bi bi-arrow-right"></i>
            </div>
            <div class="tab-button" id="tabBtn3">
                Review <i class="bi bi-arrow-right"></i>
            </div>
            <div class="tab-button" id="tabBtn4">
                Work <i class="bi bi-arrow-right"></i>
            </div>
            <div class="tab-button" id="tabBtn5">
                Describe <i class="bi bi-arrow-right"></i>
            </div>
            <div class="tab-button" id="tabBtn6">
                Adapt
            </div>
        </div>

        <div class="tab active" id="tab1">
            <?php
            require("components/tabs/requirements.php");
            ?>
        </div>
        <div class="tab" id="tab2">Content for Tab 2</div>
        <div class="tab" id="tab3">Content for Tab 3</div>
        <div class="tab" id="tab4">Content for Tab 4</div>
        <div class="tab" id="tab5">Content for Tab 5</div>
        <div class="tab" id="tab6">Content for Tab 6</div>

        <script>
            // Just some very cool Jquery string stuff
            // to make the tab buttons work
            $(".tab-button").click(function() {
                $(".tab-button").removeClass("active");
                $(this).addClass("active");

                $(".tab").removeClass("active");
                var tabId = $(this).attr("id");
                tabId = tabId.replace("tabBtn", "tab");
                $("#" + tabId).addClass("active");

                var newUrl = window.location.href.replace(/stage=\d/, "stage=" + tabId.replace("tab", ""));
                window.history.pushState("", "", newUrl);
            });

            $(document).ready(function(){
                $("#tabBtn<?php echo $stage ?>").click();
            })
        </script>
    </div>
</body>