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
                <div class="gallery-container">
                    <div class="gallery-item">
                        <img src="/static/img/placeholder.png" alt="Image 1">
                        <div>
                            <h3>Hello World</h3>
                            <p>The Hello World Loc</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="/static/img/placeholder.png" alt="Image 2">
                        <div>
                            <h3>Cool World</h3>
                            <p>Very nice thing</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="/static/img/placeholder.png" alt="Image 2">
                        <div>
                            <h3>Nice One</h3>
                            <p>I know right</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="/static/img/placeholder.png" alt="Image 2">
                        <div>
                            <h3>Example Too</h3>
                            <p>Example indeed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>