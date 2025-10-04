<?php
require_once "connection.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}

include "headerlogged.php";

/* Bază + normalizare imagine */
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
if ($BASE === '' || $BASE === '\\') { $BASE = '/'; }

function img_url($value) {
    global $BASE;
    $v = trim((string)$value);
    if ($v === '') return $BASE . 'poze/no-image.jpg';
    if (preg_match('#^https?://#i', $v)) return $v;
    $v = str_replace('\\','/',$v);
    $v = preg_replace('#^https?://[^/]+/#i','/',$v);
    $v = str_ireplace(['/GameHub-main/','/gamehub-main/'],'/gamehub/',$v);
    $v = ltrim($v,'/');
    if (stripos($v,'gamehub/')===0) $v = substr($v,8);
    $file = basename($v);
    $name = pathinfo($file, PATHINFO_FILENAME);
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $docroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
    $baseDir = rtrim($docroot.$BASE,'/\\');
    $pozeDir = $baseDir.'/poze';
    if ($ext && is_file($pozeDir.'/'.$file)) return $BASE.'poze/'.rawurlencode($file);
    foreach (['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'] as $e) {
        if (is_file($pozeDir.'/'.$name.'.'.$e)) return $BASE.'poze/'.rawurlencode($name.'.'.$e);
    }
    return $BASE.'poze/'.rawurlencode($file);
}

/* Adăugare în coș */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_to_cart'])) {
    $userId = (int)$_SESSION["user_id"];
    $pid    = (int)$_POST['product_id'];
    $pname  = $_POST['product_name'];
    $price  = (float)$_POST['product_price'];

    $stmt = $db->prepare("INSERT INTO cos_cumparaturi (user_id, product_id, product_name, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisd", $userId, $pid, $pname, $price);
    $stmt->execute();
    $stmt->close();

    // opțional: redirect ca să eviți re-post
    header("Location: vizualizare.php?id=".$pid);
    exit;
}

/* Jocul cerut */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { redirect("jocuri.php"); }

$stmt = $db->prepare("SELECT id, name, description, price, image_url, categorie FROM tabel_jocuri WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$game) { redirect("jocuri.php"); }
?>

<main></main>

<div class="grid-container">
  <div class="grid-x grid-padding-x">
    <div class="large-10 large-offset-1 cell">
      <div class="grid-x grid-margin-x">
        <div class="medium-6 cell">
          <img src="<?= img_url($game['image_url']) ?>" alt="<?= htmlspecialchars($game['name']) ?>"
               style="width:100%;height:420px;object-fit:cover;display:block;"
               onerror="this.src='<?= $BASE ?>poze/no-image.jpg'">
        </div>
        <div class="medium-6 cell">
          <h2 style="margin-top:0;"><?= htmlspecialchars($game['name']) ?></h2>
          <?php if (!empty($game['categorie'])): ?>
            <p style="color:#666;margin:6px 0 14px;">Categorie: <strong><?= htmlspecialchars($game['categorie']) ?></strong></p>
          <?php endif; ?>
          <p style="line-height:1.55;color:#444;"><?= htmlspecialchars($game['description']) ?></p>

          <div class="grid-x align-justify align-middle" style="margin-top:16px;">
            <span style="font-size:20px;font-weight:700;">
              <?= number_format((float)$game['price'], 2, '.', '') ?> $
            </span>
            <form method="post" action="">
              <input type="hidden" name="product_id" value="<?= (int)$game['id'] ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($game['name']) ?>">
              <input type="hidden" name="product_price" value="<?= number_format((float)$game['price'], 2, '.', '') ?>">
              <button type="submit" name="add_to_cart" class="button success">Adaugă în coș</button>
            </form>
          </div>

          <div style="margin-top:12px;">
            <a class="button hollow" href="jocuri.php">Înapoi la jocuri</a>
            <a class="button" href="viewcos.php">Coșul meu</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
