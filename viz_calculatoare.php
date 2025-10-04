<?php
require_once("connection.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}

include 'headerlogged.php';
?>

<main>

</main>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gamehub";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM tabel_calculatoare WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        ?>

        <div class="grid-container">
            <div class="grid-x grid-padding-x">
                <div class="cell">
                    <h1><?php echo $row['name']; ?></h1>
                    <img src="<?php echo $row['image_url']; ?>" alt="Product Image" style="width: 50%; max-width: 400px;">
                    <p><?php echo $row['description']; ?></p>
                </div>
            </div>
            <a href="calculatoare.php" class="button">Inapoi</a>
        </div>

        <?php
    } else {
        echo "<p>No product found with this ID.</p>";
    }

    $conn->close();
} else {
    echo "<p>No product ID specified.</p>";
}
?>
<?php include 'footer.php'; ?>
