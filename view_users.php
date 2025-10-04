<?php
require_once("connection.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}

include 'headerlogged.php';
?>

<main>

</main>

<div class="grid-container">
    <div class="grid-x grid-padding-x">
        <div class="large-12 cell">
            <h2>Toti utilizatorii</h2>
            </br>

            <?php

            $db = new mysqli("localhost", "root", "", "gamehub");

            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            $sqlv = "SELECT id, nume, username, type FROM users";
            $resultv = mysqli_query($db, $sqlv);

            if (!$resultv) {
                die('Invalid query:' . mysqli_error($db));
            } else {
                echo '<div class="table-responsive">';
                echo "<table class=\"unstriped\">";
                echo "  <thead><tr><th width=\"200\"><b>Name</b></th><th width=\"150\"><b>Username</b></th><th width=\"150\"><b>Type</b></th><th width=\"100\"><b>Actions</b></th></tr></thead><tbody>";
                while ($row = mysqli_fetch_array($resultv, MYSQLI_ASSOC)) {
                    echo "<tr><td>";
                    echo $row["nume"];
                    echo "</td><td>";
                    echo $row["username"];
                    echo "</td><td>";
                    echo ($row["type"] == 1) ? 'Admin' : 'User';
                    echo "</td><td>";
                    echo "<a href='edit_user.php?id=" . $row["id"] . "' class='button'>Edit</a>";
                    echo "</td></tr>";
                }
                echo "</tbody></table>";
                echo '</div>';
            }

            $db->close();
            ?>

            </br>
            <a href="admin_users.php" class="button">Inapoi</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
