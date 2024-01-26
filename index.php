<?php

namespace ClimbUI;

require_once __DIR__ . '/support/lib/vendor/autoload.php';

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/content.php';

use Approach\Render\HTML;
use mysqli;

$serverName = "localhost";
$username = "noob";
$password = "NoobScience";

// Create connection
$conn = new mysqli($serverName, $username, $password, "t3_test");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$sql = "CREATE TABLE IF NOT EXISTS `t3_test`.`users` ( `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;";

if ($conn->query($sql) === TRUE) {
    echo "Table users created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$oneMoreSQL = "INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES (NULL, 'admin', 'admin', 'admin', current_timestamp());";

if ($conn->query($oneMoreSQL) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $oneMoreSQL . "<br>" . $conn->error;
}

$selectQuery = "SELECT * FROM `users` WHERE 1";

$result = $conn->query($selectQuery);

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