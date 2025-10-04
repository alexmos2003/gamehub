<?php
require_once "connection.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    redirect("index.php");
}
include "headerlogged.php";

$userId = (int)$_SESSION["user_id"];

/* ștergere 1 produs din coș */
if (isset($_GET['remove'])) {
    $pid = (int)$_GET['remove'];
    $stmt = $db->prepare("DELETE FROM cos_cumparaturi WHERE user_id=? AND product_id=? LIMIT 1");
    $stmt->bind_param("ii", $userId, $pid);
    $stmt->execute();
    $stmt->close();
    header("Location: viewcos.php"); exit;
}

/* trimitere comanda (simplu: goliți coșul și mesaj) */
$successMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // aici ai putea salva în tabelul comenzi + adresa etc.
    $db->query("DELETE FROM cos_cumparaturi WHERE user_id=".$userId);
    $successMsg = "Comanda a fost trimisă! Vei fi contactat pentru confirmare.";
}

/* preluare produse din coș */
$stmt = $db->prepare("SELECT product_id, product_name, price FROM cos_cumparaturi WHERE user_id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$items = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0.0;
foreach ($items as $it) { $total += (float)$it['price']; }

?>

<main>
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="large-12 cell">
        <h2 style="text-align:center;margin:20px 0;">Coș de cumpărături</h2>

        <?php if ($successMsg): ?>
          <div class="callout success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <div class="table-responsive" style="max-width:760px;margin:0 auto;">
          <table class="unstriped" style="width:100%;background:#f7f7f7;">
            <thead>
              <tr>
                <th>Produs</th>
                <th style="width:140px;">Preț</th>
                <th style="width:120px;">Acțiune</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$items): ?>
                <tr><td colspan="3" style="text-align:center;">Coșul este gol.</td></tr>
              <?php else: ?>
                <?php foreach ($items as $it): ?>
                  <tr>
                    <td><?= htmlspecialchars($it['product_name']) ?></td>
                    <td><?= number_format((float)$it['price'], 2, '.', '') ?> $</td>
                    <td>
                      <a class="button alert small" href="viewcos.php?remove=<?= (int)$it['product_id'] ?>">Șterge</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div style="text-align:center;margin:18px 0;">
          <strong>Preț total: <?= number_format($total, 2, '.', '') ?> $</strong>
        </div>

        <?php if ($items): ?>
          <?php if (!isset($_GET['confirm'])): ?>
            <div style="text-align:center;">
              <a href="viewcos.php?confirm=1" class="button success">Confirmă Comanda</a>
            </div>
          <?php else: ?>
            <!-- Formular detalii comandă (sub buton), ca în poză -->
            <div style="max-width:760px;margin:18px auto;background:#f7f7f7;padding:16px;">
              <h3>Detalii Comandă</h3>
              <form method="post" action="">
                <label>Nume:
                  <input type="text" name="nume" required>
                </label>
                <label>Adresă:
                  <input type="text" name="adresa" required>
                </label>
                <label>Telefon:
                  <input type="text" name="telefon" required>
                </label>
                <label>Număr Card:
                  <input type="text" name="card" required>
                </label>
                <div class="grid-x grid-padding-x">
                  <div class="medium-6 cell">
                    <label>Data Expirare:
                      <input type="text" name="exp" placeholder="MM/YY" required>
                    </label>
                  </div>
                  <div class="medium-6 cell">
                    <label>CVV:
                      <input type="text" name="cvv" required>
                    </label>
                  </div>
                </div>
                <button type="submit" name="place_order" class="button success">Trimite Comanda</button>
              </form>
            </div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>

<?php include "footer.php"; ?>
