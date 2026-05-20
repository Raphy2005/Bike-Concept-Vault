<?php
require_once 'db.php';

$errors = [];
$success = false;

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $bike_name   = trim($_POST['bike_name']   ?? '');
    $model       = trim($_POST['model']       ?? '');
    $edition     = trim($_POST['edition']     ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $color_name  = trim($_POST['color_name']  ?? 'Pearl White');
    $stock_qty   = (int)($_POST['stock_qty']  ?? 0);
    $stock_status = $_POST['stock_status']    ?? 'In Stock';

    if (!$category_id) $errors[] = 'Please select a category.';
    if ($bike_name === '') $errors[] = 'Bike name is required.';
    if ($model === '')     $errors[] = 'Model is required.';
    if ($price <= 0)       $errors[] = 'Price must be greater than 0.';

    $image_url = '';
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
                $image_url = $filename;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            if ($is_featured) {
                $pdo->exec("UPDATE bikes SET is_featured = 0");
            }

            $stmt = $pdo->prepare("
                INSERT INTO bikes (category_id, bike_name, model, edition, price, description, image_url, is_featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$category_id, $bike_name, $model, $edition, $price, $description, $image_url, $is_featured]);
            $bike_id = $pdo->lastInsertId();

            $vstmt = $pdo->prepare("
                INSERT INTO bike_variants (bike_id, color_name, color_hex, image_url, is_default)
                VALUES (?, ?, ?, ?, 1)
            ");
            $vstmt->execute([$bike_id, $color_name, '#FFFFFF', $image_url]);
            $variant_id = $pdo->lastInsertId();

            $valid_statuses = ['In Stock','Low Stock','Out of Stock'];
            if (!in_array($stock_status, $valid_statuses)) $stock_status = 'In Stock';
            $istmt = $pdo->prepare("INSERT INTO inventory (variant_id, stock_qty, stock_status) VALUES (?, ?, ?)");
            $istmt->execute([$variant_id, $stock_qty, $stock_status]);

            $pdo->commit();
            header('Location: index.php?created=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

$old = $_POST;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Bike — Bike Concept Vault</title>
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
.topbar-right a{color:var(--textd);text-decoration:none;font-size:13px;font-weight:600;padding:7px 14px;border-radius:6px;transition:all .2s;}
.topbar-right a:hover{color:var(--text);background:var(--bg3);}

.page{max-width:780px;margin:0 auto;padding:36px 24px;}
.page-hdr{margin-bottom:28px;}
.page-hdr h1{font-family:'Orbitron',monospace;font-size:20px;font-weight:900;margin-bottom:6px;}
.page-hdr p{font-size:13px;color:var(--textd);}

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

.img-upload{border:2px dashed var(--border2);border-radius:8px;padding:30px;text-align:center;cursor:pointer;transition:border-color .2s;position:relative;}
.img-upload:hover{border-color:var(--red);}
.img-upload input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.img-upload .up-icon{font-size:28px;margin-bottom:8px;color:var(--textdd);}
.img-upload .up-text{font-size:13px;color:var(--textd);}
.img-upload .up-sub{font-size:11px;color:var(--textdd);margin-top:4px;}
.img-preview{max-width:100%;max-height:180px;object-fit:contain;border-radius:6px;display:none;}

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
  </div>
</header>

<div class="page">
  <div class="page-hdr">
    <h1>&#43; ADD NEW BIKE</h1>
    <p>Register a new motorcycle to the vault inventory</p>
  </div>

  <?php if (!empty($errors)): ?>
  <ul class="errors">
    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
  </ul>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" enctype="multipart/form-data" action="create.php">

      <!-- BIKE INFO -->
      <div class="section-title">Bike Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Bike Name *</label>
          <input type="text" name="bike_name" placeholder="e.g. Click" value="<?= htmlspecialchars($old['bike_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category_id" required>
            <option value="">— Select Category —</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>" <?= ($old['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['flag_emoji'] . ' ' . $cat['category_name'] . ' (' . $cat['country_code'] . ')') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Model *</label>
          <input type="text" name="model" placeholder="e.g. Click 2024 V2.0 PF" value="<?= htmlspecialchars($old['model'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Edition</label>
          <input type="text" name="edition" placeholder="e.g. Scooter Edition" value="<?= htmlspecialchars($old['edition'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Price (PHP ₱) *</label>
          <input type="number" name="price" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Featured Bike</label>
          <div class="checkbox-row">
            <input type="checkbox" name="is_featured" id="is_featured" <?= !empty($old['is_featured']) ? 'checked' : '' ?>>
            <label for="is_featured">Set as hero / featured bike on dashboard</label>
          </div>
        </div>
      </div>
      <div class="form-grid full">
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" placeholder="Describe the bike's features, performance, and highlights..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>
      </div>

      <hr class="divider">

      <!-- IMAGE -->
      <div class="section-title">Bike Image</div>
      <div class="img-upload" id="imgUpload">
        <input type="file" name="bike_image" accept="image/*" id="imgInput" onchange="previewImg(this)">
        <div id="upPlaceholder">
          <div class="up-icon">&#128247;</div>
          <div class="up-text">Click or drag to upload bike image</div>
          <div class="up-sub">JPG, PNG, WEBP, GIF — max 5MB</div>
        </div>
        <img id="imgPreview" class="img-preview" alt="Preview">
      </div>

      <hr class="divider">

      <!-- VARIANT & INVENTORY -->
      <div class="section-title">Default Color Variant &amp; Inventory</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Default Color Name</label>
          <input type="text" name="color_name" placeholder="e.g. Pearl White" value="<?= htmlspecialchars($old['color_name'] ?? 'Pearl White') ?>">
        </div>
        <div class="form-group">
          <label>Initial Stock Quantity</label>
          <input type="number" name="stock_qty" min="0" placeholder="0" value="<?= htmlspecialchars($old['stock_qty'] ?? '0') ?>">
        </div>
        <div class="form-group">
          <label>Stock Status</label>
          <select name="stock_status">
            <?php foreach (['In Stock','Low Stock','Out of Stock'] as $s): ?>
            <option value="<?= $s ?>" <?= ($old['stock_status'] ?? 'In Stock') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <hr class="divider">

      <div class="form-actions">
        <a href="index.php" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">&#43; CREATE BIKE</button>
      </div>
    </form>
  </div>
</div>

<script>
function previewImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const prev = document.getElementById('imgPreview');
      const ph   = document.getElementById('upPlaceholder');
      prev.src = e.target.result;
      prev.style.display = 'block';
      ph.style.display   = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>