<?php
require_once("connection.php");
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") { redirect("index.php"); }
include 'headerlogged.php';

$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
if ($BASE === '' || $BASE === '\\') { $BASE = '/'; }
function img_url($v){ /* identic ca mai sus */ 
    global $BASE; $v=trim((string)$v);
    if($v==='')return $BASE.'poze/no-image.jpg';
    if(preg_match('#^https?://#i',$v))return $v;
    $v=str_replace('\\','/',$v); $v=preg_replace('#^https?://[^/]+/#i','/',$v);
    $v=str_ireplace(['/GameHub-main/','/gamehub-main/'],'/gamehub/',$v);
    $v=ltrim($v,'/'); if(stripos($v,'gamehub/')===0)$v=substr($v,8);
    $file=basename($v); $name=pathinfo($file,PATHINFO_FILENAME); $ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
    $docroot=rtrim($_SERVER['DOCUMENT_ROOT'],'/\\'); $baseDir=rtrim($docroot.$BASE,'/\\'); $pozeDir=$baseDir.'/poze';
    if($ext && is_file($pozeDir.'/'.$file)) return $BASE.'poze/'.rawurlencode($file);
    foreach(['jpg','jpeg','png','webp','gif','JPG','JPEG','PNG','WEBP','GIF'] as $e){ if(is_file($pozeDir.'/'.$name.'.'.$e)) return $BASE.'poze/'.rawurlencode($name.'.'.$e); }
    return $BASE.'poze/'.rawurlencode($file);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $userId=(int)$_SESSION["user_id"]; $pid=(int)$_POST['product_id'];
    $pname=$_POST['product_name']; $price=(float)$_POST['product_price'];
    $stmt=$db->prepare("INSERT INTO cos_cumparaturi (user_id, product_id, product_name, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisd",$userId,$pid,$pname,$price); $stmt->execute(); $stmt->close();
}
?>

<main></main>

<div class="grid-container">
  <div class="grid-x grid-padding-x">
    <div class="large-12 cell">
      <h2 style="font-size:42px;margin:10px 0 20px;">Toate perifericele</h2>
      </br>

      <?php
      $db2 = new mysqli("localhost", "root", "", "gamehub");
      if ($db2->connect_error) die("Connection failed: " . $db2->connect_error);
      $sqlv = "SELECT id, name, description, price, image_url FROM tabel_periferice";
      $resultv = mysqli_query($db2, $sqlv);
      if (!$resultv) die('Invalid query:' . mysqli_error($db2));
      ?>

      <div class="grid-x grid-margin-x small-up-1 medium-up-2 large-up-3">
        <?php while ($row = mysqli_fetch_array($resultv, MYSQLI_ASSOC)): ?>
          <div class="cell">
            <div class="card" style="border:1px solid #e6e6e6;border-radius:12px;overflow:hidden;background:#fff;">
              <img src="<?= img_url($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>"
                   style="width:100%;height:280px;object-fit:cover;display:block;"
                   onerror="this.src='<?= $BASE ?>poze/no-image.jpg'">
              <div class="card-section" style="padding:14px 16px;">
                <h3 style="margin:0 0 6px;font-size:24px;"><?= htmlspecialchars($row['name']) ?></h3>
                <p style="margin:0 0 10px;color:#444;line-height:1.45;"><?= htmlspecialchars($row['description']) ?></p>
                <div class="grid-x align-justify align-middle" style="margin-top:8px;">
                  <span style="font-weight:700;"><?= number_format((float)$row['price'], 2, '.', '') ?> $</span>
                  <form method="post" action="">
                    <input type="hidden" name="product_id" value="<?= (int)$row['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
                    <input type="hidden" name="product_price" value="<?= number_format((float)$row['price'], 2, '.', '') ?>">
                    <button type="submit" name="add_to_cart" class="button small success">Adaugă în coș</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      </br>
      <a href="admin_periferice.php" class="button">Înapoi</a>
      <?php $db2->close(); ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
