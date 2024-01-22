<?php
namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/content.php';

use Approach\Render\HTML;
echo $webpage->render();

/*    
<?php 
// /*$pageTitle = "Home Page";
// $active = "home";

// require("head.php");

<!-- <body>
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
</body> -->
*/