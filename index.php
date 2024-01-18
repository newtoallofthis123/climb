<?php
$pageTitle = "Home Page";
$active = "home";

require("head.php");
?>

<body>
    <div class="Stage">
        <div id="main" class="Screen">
            <?php require("components/menu.php"); ?>
            <div class="Main">
                <div class="profile">
                    <div id="focus">
                        <div>
                            <img width="64" src="https://noobscience.vercel.app/favicon.ico" alt="A Cool Image">
                        </div>
                        <div>
                            <h3>John Doe</h3>
                            <p class="gray">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>