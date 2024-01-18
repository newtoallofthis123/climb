<?php
$pageTitle = "Home Page";
$active = "home";

require("head.php");
?>

<body>
    <div class="Stage">
        <div class="Overlay">
            <div class="menu">
                <button <?php
                        if ($active == "home") {
                            echo "id='active'";
                        }
                        ?>>
                    <p>
                        <i class="bi bi-card-checklist"></i> Procedures
                    </p>
                </button>
                <button <?php
                        if ($active == "reports") {
                            echo "id='active'";
                        }
                        ?>>
                    <p>
                        <i class="bi bi-clipboard-check"></i> Incident Reports
                    </p>
                </button>
                <button <?php
                        if ($active == "users") {
                            echo "id='active'";
                        }
                        ?>>
                    <p>
                        <i class="bi bi-person-circle"></i> Users
                    </p>
                </button>
            </div>
            <div class="signout">
                <button>
                    <p>
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </p>
                </button>
            </div>
        </div>
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
            <div class="sandbox">
                <div class="Toolbar">
                    <?php
                    require("components/menu.php");
                    ?>
                </div>
                <div class="Screen">
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
    </div>
</body>