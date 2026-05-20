<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

// ── Load existing bike ───────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT b.*, bv.variant_id, bv.color_name,
           bv.image_url AS variant_img, i.inventory_id,
           i.stock_qty, i.stock_status
    FROM bikes b
    LEFT JOIN bike_variants bv ON bv.bike_id   = b.bike_id AND bv.is_default = 1
    LEFT JOIN inventory     i  ON i.variant_id = bv.variant_id
    WHERE b.bike_id = ?
");
$stmt->execute([$id]);
$bike = $stmt->fetch();
if (!$bike) { header('Location: index.php'); exit; }

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id  = (int)($_POST['category_id']  ?? 0);
    $bike_name    = trim($_POST['bike_name']      ?? '');
    $model        = trim($_POST['model']          ?? '');
    $edition      = trim($_POST['edition']        ?? '');
    $price        = (float)($_POST['price']       ?? 0);
    $description  = trim($_POST['description']    ?? '');
    $is_featured  = isset($_POST['is_featured'])  ? 1 : 0;
    $color_name   = trim($_POST['color_name']     ?? '');
    $stock_qty    = (int)($_POST['stock_qty']     ?? 0);
    $stock_status = $_POST['stock_status']        ?? 'In Stock';

    if (!$category_id) $errors[] = 'Please select a category.';
    if ($bike_name === '') $errors[] = 'Bike name is required.';
    if ($model === '')     $errors[] = 'Model is required.';
    if ($price <= 0)       $errors[] = 'Price must be greater than 0.';

    // Handle new image upload
    $image_url = $bike['image_url'];
    if (isset($_FILES['bike_image']) && $_FILES['bike_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        $ftype   = mime_content_type($_FILES['bike_image']['tmp_name']);
        if (!in_array($ftype, $allowed)) {
            $errors[] = 'Image must be JPG, PNG, WEBP, or GIF.';
        } else {
            $ext      = pathinfo($_FILES['bike_image']['name'], PATHINFO_EXTENSION);
            $filename = 'uploads/' . uniqid('bike_') . '.' . $ext;
            if (!is_dir('uploads')) mkdir('uploads', 0755, true);
            if (move_uploaded_file($_FILES['bike_image']['tmp_name'], $filename)) {
                // Remove old image
                if ($image_url && file_exists($image_url)) @unlink($image_url);
                $image_url = $filename;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            if ($is_featured) $pdo->exec("UPDATE bikes SET is_featured = 0");

            // Update bike
            $pdo->prepare("
                UPDATE bikes SET category_id=?, bike_name=?, model=?, edition=?,
                price=?, description=?, image_url=?, is_featured=?, updated_at=NOW()
                WHERE bike_id=?
            ")->execute([$category_id, $bike_name, $model, $edition, $price, $description, $image_url, $is_featured, $id]);

            // Update or insert variant
            if ($bike['variant_id']) {
                $pdo->prepare("
                    UPDATE bike_variants SET color_name=?, image_url=?
                    WHERE variant_id=?
                ")->execute([$color_name, $image_url, $bike['variant_id']]);
            } else {
                $pdo->prepare("
                    INSERT INTO bike_variants (bike_id, color_name, image_url, is_default)
                    VALUES (?, ?, ?, 1)
                ")->execute([$id, $color_name, $image_url]);
                $new_vid = $pdo->lastInsertId();
                $pdo->prepare("INSERT INTO inventory (variant_id, stock_qty, stock_status) VALUES (?, ?, ?)")
                    ->execute([$new_vid, $stock_qty, $stock_status]);
                $pdo->commit();
                header('Location: index.php?updated=1');
                exit;
            }

            // Update inventory
            $valid_statuses = ['In Stock','Low Stock','Out of Stock'];
            if (!in_array($stock_status, $valid_statuses)) $stock_status = 'In Stock';
            if ($bike['inventory_id']) {
                $pdo->prepare("
                    UPDATE inventory SET stock_qty=?, stock_status=?
                    WHERE inventory_id=?
                ")->execute([$stock_qty, $stock_status, $bike['inventory_id']]);
            } else {
                $pdo->prepare("
                    INSERT INTO inventory (variant_id, stock_qty, stock_status) VALUES (?, ?, ?)
                ")->execute([$bike['variant_id'], $stock_qty, $stock_status]);
            }

            $pdo->commit();
            header('Location: index.php?updated=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    // Repopulate with submitted values on error
    $bike = array_merge($bike, [
        'category_id' => $category_id, 'bike_name' => $bike_name, 'model' => $model,
        'edition' => $edition, 'price' => $price, 'description' => $description,
        'is_featured' => $is_featured, 'color_name' => $color_name,
        'stock_qty' => $stock_qty, 'stock_status' => $stock_status,
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Bike — Bike Concept Vault</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#090909;--bg2:#0f0f0f;--bg3:#161616;--bg4:#1c1c1c;
  --red:#e8000d;--red2:#ff1a24;--reddim:rgba(232,0,13,.08);
  --border:#1a0002;--border2:#3d0005;
  --text:#f0f0f0;--textd:#9a9a9a;--textdd:#444;
}
body{background:var(--bg);color:var(--text);font-family:'Rajdhani',sans-serif;min-height:100vh;}
.topbar{background:var(--bg2);border-bottom:1px solid var(--border2);padding:0 28px;display:flex;align-items:center;justify-content:space-between;height:56px;}
.logo{font-family:'Orbitron',monospace;font-size:14px;font-weight:900;color:var(--red2);letter-spacing:2px;text-decoration:none;display:flex;align-items:center;gap:10px;}
.logo-box{width:30px;height:30px;background:var(--red);border-radius:6px;display:flex;align-items:center;justify-content:center;}
.logo-box svg{width:18px;height:18px;fill:#fff;}
.topbar-right{display:flex;gap:8px;align-items:center;}
.topbar-right a{color:var(--textd);text-decoration:none;font-size:13px;font-weight:600;padding:7px 14px;border-radius:6px;transition:all .2s;}
.topbar-right a:hover{color:var(--text);background:var(--bg3);}
.topbar-right .btn-del-top{color:#f87171;border:1px solid #7f1d1d;border-radius:6px;}
.topbar-right .btn-del-top:hover{background:rgba(239,68,68,.08);}
.page{max-width:780px;margin:0 auto;padding:36px 24px;}
.page-hdr{margin-bottom:28px;display:flex;align-items:flex-start;justify-content:space-between;}
.page-hdr h1{font-family:'Orbitron',monospace;font-size:20px;font-weight:900;margin-bottom:6px;}
.page-hdr p{font-size:13px;color:var(--textd);}
.bike-id-badge{background:var(--reddim);color:var(--red2);border:1px solid var(--border2);border-radius:4px;font-size:10px;font-weight:700;letter-spacing:1.5px;padding:4px 12px;white-space:nowrap;}
.form-card{background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:30px;position:relative;overflow:hidden;}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--red),transparent);}
.section-title{font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:1.5px;color:var(--textd);text-transform:uppercase;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;}
.form-grid.full{grid-template-columns:1fr;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:12px;font-weight:700;letter-spacing:.5px;color:var(--textd);}
.form-group input,.form-group select,.form-group textarea{
  background:var(--bg3);border:1px solid var(--border2);border-radius:6px;
  padding:9px 14px;color:var(--text);font-family:'Rajdhani',sans-serif;font-size:13px;
  outline:none;transition:border-color .2s;
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--red);}
.form-group textarea{resize:vertical;min-height:90px;}
.form-group select option{background:var(--bg3);}

/* Current image preview */
.current-img-wrap{position:relative;display:inline-block;margin-bottom:14px;}
.current-img-wrap img{max-width:220px;max-height:140px;object-fit:contain;border-radius:6px;border:1px solid var(--border2);}
.current-img-wrap .cur-label{font-size:10px;color:var(--textdd);margin-top:4px;text-align:center;}

.img-upload{border:2px dashed var(--border2);border-radius:8px;padding:24px;text-align:center;cursor:pointer;transition:border-color .2s;position:relative;}
.img-upload:hover{border-color:var(--red);}
.img-upload input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.img-upload .up-icon{font-size:24px;color:var(--textdd);}
.img-upload .up-text{font-size:13px;color:var(--textd);}
.img-upload .up-sub{font-size:11px;color:var(--textdd);margin-top:4px;}
.img-preview{max-width:100%;max-height:160px;object-fit:contain;border-radius:6px;display:none;margin-top:8px;}

.checkbox-row{display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--bg3);border:1px solid var(--border2);border-radius:6px;}
.checkbox-row input[type=checkbox]{width:16px;height:16px;accent-color:var(--red);}
.checkbox-row label{font-size:13px;font-weight:600;cursor:pointer;}
.errors{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:6px;padding:12px 16px;margin-bottom:20px;}
.errors li{font-size:13px;color:#f87171;list-style:none;margin-bottom:4px;}
.errors li:last-child{margin:0;}
.errors li::before{content:'✕ ';}
.divider{border:none;border-top:1px solid var(--border);margin:22px 0;}
.form-actions{display:flex;gap:10px;justify-content:flex-end;}
.btn-cancel{background:var(--bg3);border:1px solid var(--border2);color:var(--text);padding:10px 24px;border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .2s;}
.btn-cancel:hover{background:var(--bg4);}
.btn-submit{background:var(--red);border:none;color:#fff;padding:10px 28px;border-radius:6px;font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:1px;cursor:pointer;transition:background .2s;}
.btn-submit:hover{background:var(--red2);}
/* Delete modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:200;align-items:center;justify-content:center;}
.modal-overlay.show{display:flex;}
.modal{background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:30px;max-width:380px;width:90%;text-align:center;position:relative;}
.modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--red);border-radius:10px 10px 0 0;}
.modal h3{font-family:'Orbitron',monospace;font-size:16px;margin-bottom:8px;}
.modal p{font-size:13px;color:var(--textd);line-height:1.6;margin-bottom:22px;}
.modal-btns{display:flex;gap:10px;justify-content:center;}
.btn-confirm-del{background:var(--red);border:none;color:#fff;padding:9px 24px;border-radius:6px;font-family:'Orbitron',monospace;font-size:10px;font-weight:700;letter-spacing:1px;cursor:pointer;text-decoration:none;}
.btn-confirm-del:hover{background:var(--red2);}
@media(max-width:580px){.form-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<header class="topbar">
  <a href="index.php" class="logo">
    <div class="logo-box"><svg viewBox="0 0 20 20"><path d="M10 2L2 7v6l8 5 8-5V7L10 2z"/></svg></div>
    BIKE CONCEPT VAULT
  </a>
  <div class="topbar-right">
    <a href="index.php">&#8592; Back to Vault</a>
    <a href="#" class="btn-del-top" onclick="document.getElementById('deleteModal').classList.add('show');return false;">&#128465; Delete This Bike</a>
  </div>
</header>

<div class="page">
  <div class="page-hdr">
    <div>
      <h1>&#9998; EDIT BIKE</h1>
      <p>Update information for <?= htmlspecialchars($bike['bike_name'] . ' ' . $bike['model']) ?></p>
    </div>
    <span class="bike-id-badge">ID #<?= $id ?></span>
  </div>

  <?php if (!empty($errors)): ?>
  <ul class="errors">
    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" enctype="multipart/form-data" action="edit.php?id=<?= $id ?>">

      <div class="section-title">Bike Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Bike Name *</label>
          <input type="text" name="bike_name" value="<?= htmlspecialchars($bike['bike_name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>" <?= $bike['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['flag_emoji'] . ' ' . $cat['category_name'] . ' (' . $cat['country_code'] . ')') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Model *</label>
          <input type="text" name="model" value="<?= htmlspecialchars($bike['model']) ?>" required>
        </div>
        <div class="form-group">
          <label>Edition</label>
          <input type="text" name="edition" value="<?= htmlspecialchars($bike['edition'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Price (PHP ₱) *</label>
          <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($bike['price']) ?>" required>
        </div>
        <div class="form-group">
          <label>Featured Bike</label>
          <div class="checkbox-row">
            <input type="checkbox" name="is_featured" id="is_featured" <?= $bike['is_featured'] ? 'checked' : '' ?>>
            <label for="is_featured">Set as hero / featured bike on dashboard</label>
          </div>
        </div>
      </div>
      <div class="form-grid full">
        <div class="form-group">
          <label>Description</label>
          <textarea name="description"><?= htmlspecialchars($bike['description'] ?? '') ?></textarea>
        </div>
      </div>

      <hr class="divider">

      <div class="section-title">Bike Image</div>
      <?php $curImg = $bike['variant_img'] ?: $bike['image_url']; ?>
      <?php if ($curImg): ?>
      <div class="current-img-wrap">
        <img src="<?= htmlspecialchars($curImg) ?>" alt="Current" onerror="this.parentNode.style.display='none'">
        <div class="cur-label">Current image</div>
      </div>
      <?php endif; ?>
      <div class="img-upload">
        <input type="file" name="bike_image" accept="image/*" onchange="previewImg(this)">
        <div class="up-icon">&#128247;</div>
        <div class="up-text"><?= $curImg ? 'Upload new image to replace' : 'Click to upload bike image' ?></div>
        <div class="up-sub">JPG, PNG, WEBP, GIF — leave blank to keep current</div>
        <img id="imgPreview" class="img-preview" alt="New preview">
      </div>

      <hr class="divider">

      <div class="section-title">Default Color Variant &amp; Inventory</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Default Color Name</label>
          <input type="text" name="color_name" value="<?= htmlspecialchars($bike['color_name'] ?? 'Pearl White') ?>">
        </div>
        <div class="form-group">
          <label>Stock Quantity</label>
          <input type="number" name="stock_qty" min="0" value="<?= (int)($bike['stock_qty'] ?? 0) ?>">
        </div>
        <div class="form-group">
          <label>Stock Status</label>
          <select name="stock_status">
            <?php foreach (['In Stock','Low Stock','Out of Stock'] as $s): ?>
            <option value="<?= $s ?>" <?= ($bike['stock_status'] ?? 'In Stock') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <hr class="divider">

      <div class="form-actions">
        <a href="index.php" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">&#9998; SAVE CHANGES</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div style="font-size:34px;margin-bottom:12px;">&#9888;</div>
    <h3>CONFIRM DELETE</h3>
    <p>Delete <strong style="color:var(--red2)"><?= htmlspecialchars($bike['bike_name'] . ' ' . $bike['model']) ?></strong>?<br>All variants and inventory will be removed.</p>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="document.getElementById('deleteModal').classList.remove('show')">Cancel</button>
      <a href="delete.php?id=<?= $id ?>" class="btn-confirm-del">DELETE</a>
    </div>
  </div>
</div>

<script>
function previewImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const prev = document.getElementById('imgPreview');
      prev.src = e.target.result;
      prev.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
document.getElementById('deleteModal').addEventListener('click', function(e){
  if(e.target === this) this.classList.remove('show');
});
</script>
</body>
</html>