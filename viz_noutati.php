<?php
require_once("connection.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}

include 'headerlogged.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $db = new mysqli("localhost", "root", "", "gamehub");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $sql = "SELECT * FROM tabel_noutati WHERE id='$id'";
    $result = $db->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        ?>

        <main>
            <div class="grid-container">
                <div class="grid-x grid-padding-x">
                    <div class="cell">
                        <h1><?php echo $row['title']; ?></h1>
                        <img src="<?php echo $row['image_url']; ?>" alt="News Image" style="width: 50%; max-width: 400px;">
                        <p><?php echo $row['content']; ?></p>
                        <p><strong>Published on:</strong> <?php echo $row['published_date']; ?></p>
                    </div>
                </div>
                <a href="noutati.php" class="button">Back to News</a>
            </div>
        </main>

        <?php
    } else {
        echo "<p>No news found with this ID.</p>";
    }

    $db->close();
} else {
    echo "<p>No news ID specified.</p>";
}

include 'footer.php';
?>
