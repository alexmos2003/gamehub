<?php
require_once("connection.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}

include 'headerlogged.php';

/* === helper: baza + normalizare imagine (ca la paginile anterioare) === */
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
if ($BASE === '' || $BASE === '\\') { $BASE = '/'; }

function img_url($value) {
    global $BASE;
    $v = trim((string)$value);
    if ($v === '') return $BASE . 'poze/no-image.jpg';
    if (preg_match('#^https?://#i', $v)) return $v;

    $v = str_replace('\\', '/', $v);
    $v = preg_replace('#^https?://[^/]+/#i', '/', $v);
    $v = str_ireplace(['/GameHub-main/', '/gamehub-main/'], '/gamehub/', $v);
    $v = ltrim($v, '/');
    if (stripos($v, 'gamehub/') === 0) $v = substr($v, strlen('gamehub/'));

    $file = basename($v);
    $name = pathinfo($file, PATHINFO_FILENAME);
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
    $baseDir = rtrim($docroot . $BASE, '/\\');
    $pozeDir = $baseDir . '/poze';

    if ($ext && is_file($pozeDir . '/' . $file)) {
        return $BASE . 'poze/' . rawurlencode($file);
    }
    foreach (['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'] as $e) {
        $candidate = $pozeDir . '/' . $name . '.' . $e;
        if (is_file($candidate)) {
            return $BASE . 'poze/' . rawurlencode($name . '.' . $e);
        }
    }
    return $BASE . 'poze/' . rawurlencode($file);
}
?>

<main>
    <div class="grid-container">
        <div class="grid-x grid-padding-x">
            <div class="large-12 cell">
                <h2 style="font-size:42px;margin:10px 0 20px;">Toate noutățile</h2>
                </br>

                <?php
                $db = new mysqli("localhost", "root", "", "gamehub");
                if ($db->connect_error) {
                    die("Connection failed: " . $db->connect_error);
                }

                $sqlv = "SELECT id, title, description, image_url, published_date FROM tabel_noutati ORDER BY published_date DESC, id DESC";
                $resultv = mysqli_query($db, $sqlv);
                if (!$resultv) {
                    die('Invalid query:' . mysqli_error($db));
                }
                ?>

                <!-- Grid 3 coloane desktop, 2 tabletă, 1 mobil -->
                <div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-3">
                    <?php while ($row = mysqli_fetch_array($resultv, MYSQLI_ASSOC)): ?>
                        <?php
                            $title = htmlspecialchars($row['title']);
                            $desc  = htmlspecialchars($row['description']);
                            $date  = $row['published_date'] ? date('d.m.Y', strtotime($row['published_date'])) : '';
                            $img   = img_url($row['image_url']);
                            $id    = (int)$row['id'];
                        ?>
                        <div class="cell">
                            <div class="card" style="border:1px solid #e6e6e6;border-radius:12px;overflow:hidden;background:#fff;">
                                <img
                                    src="<?= $img ?>"
                                    alt="<?= $title ?>"
                                    style="width:100%;height:240px;object-fit:cover;display:block;"
                                    onerror="this.src='<?= $BASE ?>poze/no-image.jpg'">
                                <div class="card-section" style="padding:14px 16px;">
                                    <h3 style="margin:0 0 6px;font-size:22px;"><?= $title ?></h3>
                                    <?php if ($date): ?>
                                        <p style="margin:0 0 8px;color:#666;font-size:14px;">Publicat: <?= $date ?></p>
                                    <?php endif; ?>
                                    <p style="margin:0 0 12px;color:#444;line-height:1.5;"><?= $desc ?></p>
                                    <div class="grid-x align-right">
                                        <a href="edit_noutati.php?id=<?= $id ?>" class="button small">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                </br>
                <a href="admin_noutati.php" class="button">Înapoi</a>

                <?php $db->close(); ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
