<?php
require_once 'db.php';

$appSettings = getAppSettings($pdo);
$vaultPin = preg_match('/^\d{4,8}$/', (string)($appSettings['vault_pin'] ?? '654321'))
    ? (string)$appSettings['vault_pin']
    : '654321';
$themePrimary = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_primary'] ?? ''))
    ? strtoupper($appSettings['theme_primary'])
    : '#FF3442';
$themeAccent = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_accent'] ?? ''))
    ? strtoupper($appSettings['theme_accent'])
    : '#9FE7FF';
$themeSurface = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_surface'] ?? ''))
    ? strtoupper($appSettings['theme_surface'])
    : '#04070B';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

// ── Load existing bike ───────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT b.*
    FROM bikes b
    WHERE b.bike_id = ?
");
$stmt->execute([$id]);
$bike = $stmt->fetch();
if (!$bike) { header('Location: index.php'); exit; }

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();
$errors = [];

function flagIso(string $countryCode): string {
    $map = [
        'PHL' => 'ph', 'PHI' => 'ph', 'PHIL' => 'ph', 'PINOY' => 'ph', 'PIN' => 'ph',
        'IDN' => 'id', 'INA' => 'id', 'INDO' => 'id',
        'MYS' => 'my', 'MAS' => 'my', 'MAL' => 'my', 'MALAY' => 'my',
        'THA' => 'th', 'THAI' => 'th',
    ];
    $code = strtoupper(trim($countryCode));
    return $map[$code] ?? strtolower(substr($code, 0, 2));
}

function flagImg(string $countryCode, string $alt = '', string $cls = ''): string {
    $iso = flagIso($countryCode);
    $clsAttr = $cls ? " class=\"$cls\"" : '';
    return "<img src=\"https://flagcdn.com/24x18/{$iso}.png\"
                 srcset=\"https://flagcdn.com/48x36/{$iso}.png 2x\"
                 width=\"24\" height=\"18\"
                 alt=\"" . htmlspecialchars($alt ?: $countryCode, ENT_QUOTES) . "\"
                 onerror=\"this.style.display='none'\"
                 {$clsAttr}>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id  = (int)($_POST['category_id']  ?? 0);
    $bike_name    = trim($_POST['bike_name']      ?? '');
    $model        = trim($_POST['model']          ?? '');
    $edition      = trim($_POST['edition']        ?? '');
    $price        = (float)($_POST['price']       ?? 0);
    $description  = trim($_POST['description']    ?? '');
    $is_featured  = (int)($bike['is_featured'] ?? 0);

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
        'is_featured' => $is_featured,
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
  --bg:<?= htmlspecialchars($themeSurface) ?>;--panel:rgba(8,13,20,.68);--panel2:rgba(16,23,32,.76);
  --red:<?= htmlspecialchars($themePrimary) ?>;--red2:color-mix(in srgb, <?= htmlspecialchars($themePrimary) ?> 72%, #ffffff 28%);--redsoft:color-mix(in srgb, <?= htmlspecialchars($themePrimary) ?> 24%, transparent);
  --cyan:<?= htmlspecialchars($themeAccent) ?>;--cyansoft:color-mix(in srgb, <?= htmlspecialchars($themeAccent) ?> 22%, transparent);
  --line:rgba(255,103,112,.6);--line2:rgba(159,231,255,.58);
  --text:#f8e8e9;--textd:#d8a6aa;--textdd:#7d8b96;
}
html{min-height:100%;}
body{
  background:
    radial-gradient(circle at 51% 43%, rgba(255,73,82,.2) 0 15%, transparent 33%),
    linear-gradient(90deg, rgba(2,5,10,.78), rgba(5,8,13,.34) 45%, rgba(2,5,10,.86)),
    url('assets/img/Background.png') center center/cover fixed no-repeat;
  color:var(--text);font-family:'Rajdhani',sans-serif;min-height:100vh;overflow-x:hidden;
}
body::before{
  content:'';position:fixed;inset:0;pointer-events:none;z-index:-1;
  background:
    linear-gradient(180deg, rgba(2,4,9,.36), rgba(2,4,9,.74)),
    repeating-linear-gradient(0deg, rgba(255,255,255,.035) 0 1px, transparent 1px 5px);
  mix-blend-mode:screen;opacity:.65;
}
.topbar{
  background:linear-gradient(90deg, rgba(5,9,14,.88), rgba(13,19,28,.58));
  border-bottom:1px solid rgba(159,231,255,.25);padding:0 28px;
  display:flex;align-items:center;justify-content:space-between;height:56px;
  box-shadow:0 0 24px rgba(255,52,66,.14);backdrop-filter:blur(10px);
}
.logo{font-family:'Orbitron',monospace;text-decoration:none;display:flex;align-items:center;gap:10px;}
.logo-icon{width:34px;height:34px;background:var(--red);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.logo-icon img{width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;}
.logo-text .top{color:var(--red2);font-size:11.5px;font-weight:900;letter-spacing:.5px;display:block;}
.logo-text .bot{color:var(--textd);font-size:8.5px;letter-spacing:3.5px;display:block;}
.topbar-right{display:flex;gap:8px;align-items:center;}
.topbar-right a{color:var(--textd);text-decoration:none;font-size:13px;font-weight:700;padding:7px 14px;border-radius:6px;transition:all .2s;}
.topbar-right a:hover{color:var(--cyan);background:rgba(159,231,255,.08);}
.topbar-right .btn-del-top{color:#ff9da3;border:1px solid rgba(255,104,112,.6);border-radius:6px;box-shadow:0 0 16px rgba(255,52,66,.12);}
.topbar-right .btn-del-top:hover{background:rgba(255,52,66,.12);color:#fff;}
.page{max-width:1040px;margin:0 auto;padding:34px 28px 42px;}
.page-hdr{margin-bottom:28px;display:flex;align-items:flex-start;justify-content:space-between;text-shadow:0 0 20px rgba(255,52,66,.48);}
.page-hdr h1{font-family:'Orbitron',monospace;font-size:25px;font-weight:900;margin-bottom:6px;color:#ffd4d7;letter-spacing:.8px;}
.page-hdr h1::first-letter{color:var(--red2);}
.page-hdr p{font-size:15px;color:#ffc0c5;}
.form-card{
  background:
    linear-gradient(135deg, rgba(255,72,80,.08), transparent 24%),
    linear-gradient(180deg, var(--panel), rgba(7,12,18,.72));
  border:1px solid rgba(159,231,255,.42);border-radius:8px;padding:36px 38px 30px;
  position:relative;overflow:hidden;box-shadow:0 0 0 1px rgba(255,78,86,.24), 0 28px 70px rgba(0,0,0,.55), inset 0 0 46px rgba(255,52,66,.1);
  backdrop-filter:blur(9px);
}
.form-card::before{
  content:'';position:absolute;inset:0;pointer-events:none;
  background:
    linear-gradient(90deg, var(--red2), transparent 18%, transparent 82%, var(--cyan)) top/100% 1px no-repeat,
    linear-gradient(90deg, rgba(255,52,66,.18) 1px, transparent 1px),
    linear-gradient(0deg, rgba(159,231,255,.12) 1px, transparent 1px);
  background-size:100% 1px, 96px 96px, 96px 96px;
}
.form-card::after{
  content:'';position:absolute;inset:16px;pointer-events:none;border:1px solid rgba(255,104,112,.28);
  clip-path:polygon(0 0, 22% 0, 25% 18px, 100% 18px, 100% 100%, 0 100%);
}
.form-card form{position:relative;z-index:1;}
.section-title{font-family:'Orbitron',monospace;font-size:14px;font-weight:800;letter-spacing:1.4px;color:#ffd0d4;text-transform:uppercase;margin:4px 0 22px;padding-bottom:10px;border-bottom:1px solid rgba(255,104,112,.45);text-shadow:0 0 12px rgba(255,52,66,.48);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:22px;}
.form-grid.full{grid-template-columns:1fr;}
.form-group{display:flex;flex-direction:column;gap:8px;}
.form-group label{font-size:15px;font-weight:800;letter-spacing:.3px;color:#ffd8db;text-shadow:0 0 10px rgba(255,52,66,.45);}
.form-group input,.form-group select,.form-group textarea{
  background:rgba(14,21,30,.62);border:1px solid var(--line2);border-radius:7px;
  padding:12px 16px;color:var(--text);font-family:'Rajdhani',sans-serif;font-size:16px;font-weight:600;
  outline:none;transition:border-color .2s, box-shadow .2s, background .2s;
  box-shadow:inset 0 0 22px rgba(159,231,255,.08), 0 0 15px rgba(159,231,255,.12);
}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--red2);box-shadow:inset 0 0 24px rgba(159,231,255,.12),0 0 0 1px rgba(255,91,99,.56),0 0 24px rgba(255,52,66,.28);}
.form-group textarea{resize:vertical;min-height:118px;}
.form-group select option{background:#101722;}
.form-group select[name=category_id]{display:none;}
.category-picker{position:relative;}
.category-picker input[type=hidden]{display:none;}
.category-trigger{
  width:100%;height:48px;background:rgba(14,21,30,.62);border:1px solid var(--line2);border-radius:7px;
  color:var(--text);font-family:'Rajdhani',sans-serif;font-size:16px;font-weight:800;
  display:flex;align-items:center;justify-content:space-between;gap:12px;padding:0 12px;cursor:pointer;
  transition:border-color .2s, box-shadow .2s;text-align:left;
  box-shadow:inset 0 0 22px rgba(159,231,255,.08), 0 0 15px rgba(159,231,255,.12);
}
.category-trigger:focus,.category-picker.open .category-trigger{outline:none;border-color:var(--red2);box-shadow:0 0 0 1px rgba(255,91,99,.56),0 0 24px rgba(255,52,66,.28);}
.category-trigger-main,.category-option-main{display:flex;align-items:center;gap:8px;min-width:0;}
.category-flag{width:24px;height:18px;border-radius:2px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.16);}
.category-trigger-text,.category-option-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.category-trigger-code,.category-option-code{color:var(--textd);font-size:12px;font-weight:800;letter-spacing:.4px;}
.category-trigger-arrow{color:#ffd0d4;font-size:12px;flex-shrink:0;}
.category-menu{
  display:none;position:absolute;left:0;right:0;top:calc(100% + 6px);z-index:30;
  background:rgba(8,13,20,.96);border:1px solid var(--line2);border-radius:7px;overflow:hidden;
  box-shadow:0 14px 28px rgba(0,0,0,.45),0 0 24px rgba(159,231,255,.18);
}
.category-picker.open .category-menu{display:block;}
.category-option{
  width:100%;height:42px;background:transparent;border:0;color:var(--text);font-family:'Rajdhani',sans-serif;
  display:flex;align-items:center;justify-content:space-between;gap:8px;padding:0 12px;cursor:pointer;
  text-align:left;font-size:14px;font-weight:800;
}
.category-option:hover,.category-option.active{background:rgba(255,52,66,.28);color:#fff;}
.category-option:hover .category-option-code,.category-option.active .category-option-code{color:#fff;}

/* Current image preview */
.current-img-wrap{position:relative;display:inline-block;margin-bottom:18px;padding:10px;border:1px solid rgba(159,231,255,.45);border-radius:8px;background:rgba(14,21,30,.5);box-shadow:inset 0 0 22px rgba(159,231,255,.08),0 0 18px rgba(255,52,66,.12);}
.current-img-wrap img{max-width:240px;max-height:150px;object-fit:contain;border-radius:6px;}
.current-img-wrap .cur-label{font-size:11px;color:var(--textd);margin-top:6px;text-align:center;text-transform:uppercase;letter-spacing:1px;font-weight:800;}

.img-upload{
  border:1px dashed rgba(159,231,255,.72);border-radius:8px;min-height:190px;padding:34px;text-align:center;
  cursor:pointer;transition:border-color .2s, box-shadow .2s;position:relative;display:flex;align-items:center;justify-content:center;flex-direction:column;
  background:linear-gradient(135deg, rgba(159,231,255,.08), rgba(255,52,66,.08));
  box-shadow:inset 0 0 36px rgba(159,231,255,.12);
}
.img-upload::before,.img-upload::after{content:'';position:absolute;width:78px;height:38px;border-color:var(--cyan);opacity:.75;}
.img-upload::before{left:14px;top:14px;border-top:1px solid;border-left:1px solid;}
.img-upload::after{right:14px;bottom:14px;border-right:1px solid;border-bottom:1px solid;}
.img-upload:hover{border-color:var(--red2);box-shadow:inset 0 0 36px rgba(159,231,255,.16),0 0 28px rgba(255,52,66,.22);}
.img-upload input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.img-upload .up-icon{font-size:46px;margin-bottom:8px;color:var(--cyan);text-shadow:0 0 16px rgba(159,231,255,.65);}
.img-upload .up-text{font-size:15px;color:#ffd7da;font-weight:700;}
.img-upload .up-sub{font-size:12px;color:var(--textd);margin-top:5px;}
.img-preview{max-width:100%;max-height:180px;object-fit:contain;border-radius:6px;display:none;margin-top:10px;}

.errors{background:rgba(239,68,68,.16);border:1px solid rgba(255,104,112,.45);border-radius:6px;padding:12px 16px;margin-bottom:20px;backdrop-filter:blur(8px);}
.errors li{font-size:13px;color:#f87171;list-style:none;margin-bottom:4px;}
.errors li:last-child{margin:0;}
.errors li::before{content:'✕ ';}
.divider{border:none;border-top:1px solid rgba(255,104,112,.45);margin:28px 0 24px;}
.form-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:22px;}
.btn-cancel{background:rgba(14,21,30,.72);border:1px solid rgba(159,231,255,.64);color:var(--text);padding:12px 28px;border-radius:7px;font-family:'Rajdhani',sans-serif;font-size:16px;font-weight:800;cursor:pointer;text-decoration:none;transition:all .2s;box-shadow:0 0 16px rgba(159,231,255,.12);}
.btn-cancel:hover{color:var(--cyan);box-shadow:0 0 22px rgba(159,231,255,.22);}
.btn-submit{background:linear-gradient(180deg, rgba(255,111,118,.92), rgba(205,28,40,.92));border:1px solid rgba(255,179,184,.72);color:#fff;padding:12px 30px;border-radius:7px;font-family:'Orbitron',monospace;font-size:13px;font-weight:900;letter-spacing:1px;cursor:pointer;transition:filter .2s, box-shadow .2s;box-shadow:0 0 18px rgba(255,52,66,.45), inset 0 0 18px rgba(255,255,255,.12);}
.btn-submit:hover{filter:brightness(1.12);box-shadow:0 0 28px rgba(255,52,66,.58), inset 0 0 18px rgba(255,255,255,.16);}
/* Delete modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.82);backdrop-filter:blur(5px);z-index:200;align-items:center;justify-content:center;}
.modal-overlay.show{display:flex;}
.modal{background:rgba(8,13,20,.94);border:1px solid rgba(159,231,255,.42);border-radius:8px;padding:30px;max-width:380px;width:90%;text-align:center;position:relative;box-shadow:0 24px 70px rgba(0,0,0,.72);}
.modal::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--red2),transparent);border-radius:8px 8px 0 0;}
.modal h3{font-family:'Orbitron',monospace;font-size:16px;margin-bottom:8px;}
.modal p{font-size:13px;color:var(--textd);line-height:1.6;margin-bottom:22px;}
.modal-btns{display:flex;gap:10px;justify-content:center;}
.btn-confirm-del{background:var(--red);border:1px solid rgba(255,179,184,.62);color:#fff;padding:10px 24px;border-radius:6px;font-family:'Orbitron',monospace;font-size:10px;font-weight:700;letter-spacing:1px;cursor:pointer;text-decoration:none;box-shadow:0 0 18px rgba(255,52,66,.32);}
.btn-confirm-del:hover{background:var(--red2);}
.pin-modal{
  background:linear-gradient(135deg, rgba(159,231,255,.1), rgba(232,0,13,.16)),rgba(9,12,18,.9);
  border:1px solid rgba(159,231,255,.56);
  box-shadow:0 0 0 1px rgba(255,26,36,.24),0 24px 70px rgba(0,0,0,.72),inset 0 0 38px rgba(159,231,255,.1);
  overflow:hidden;
}
.pin-modal::before{height:1px;background:linear-gradient(90deg,var(--red2),#9fe7ff,transparent);}
.pin-modal::after{content:'';position:absolute;inset:14px;pointer-events:none;border:1px solid rgba(159,231,255,.2);background:repeating-linear-gradient(0deg, rgba(159,231,255,.05) 0 1px, transparent 1px 7px);}
.pin-core{position:relative;z-index:1;}
.pin-orb{width:54px;height:54px;margin:0 auto 14px;border-radius:50%;border:1px solid rgba(159,231,255,.78);display:flex;align-items:center;justify-content:center;color:#dff7ff;font-family:'Orbitron',monospace;font-size:22px;font-weight:900;text-shadow:0 0 14px rgba(159,231,255,.85);box-shadow:0 0 28px rgba(159,231,255,.28),inset 0 0 22px rgba(255,26,36,.18);}
.pin-input{width:100%;height:46px;margin:4px 0 10px;background:rgba(6,12,20,.72);border:1px solid rgba(159,231,255,.65);border-radius:7px;color:#fff;text-align:center;font-family:'Orbitron',monospace;font-size:18px;font-weight:800;letter-spacing:8px;outline:none;box-shadow:inset 0 0 18px rgba(159,231,255,.12),0 0 18px rgba(159,231,255,.14);}
.pin-input:focus{border-color:var(--red2);box-shadow:inset 0 0 18px rgba(159,231,255,.16),0 0 22px rgba(232,0,13,.32);}
.pin-error{min-height:18px;color:#ff8b92;font-size:12px;font-weight:700;margin-bottom:14px;text-transform:uppercase;letter-spacing:.8px;}
.btn-pin-unlock{background:linear-gradient(180deg, rgba(255,83,93,.95), rgba(199,0,13,.95));border:1px solid rgba(255,184,188,.72);color:#fff;padding:10px 24px;border-radius:6px;font-family:'Orbitron',monospace;font-size:10px;font-weight:800;letter-spacing:1px;cursor:pointer;box-shadow:0 0 22px rgba(232,0,13,.38);}
.btn-pin-unlock:hover{filter:brightness(1.1);}
@media(max-width:720px){
  .topbar{padding:0 16px;}
  .topbar-right{gap:6px;}
  .topbar-right a{font-size:12px;padding:7px 10px;}
  .page{padding:26px 16px 32px;}
  .page-hdr{display:block;}
  .form-card{padding:26px 18px 22px;}
  .form-grid{grid-template-columns:1fr;gap:16px;}
  .form-actions{flex-direction:column-reverse;}
  .btn-cancel,.btn-submit{text-align:center;width:100%;}
}

/* Holographic buttons only */
.topbar-right .btn-del-top,.btn-cancel,.btn-submit,.btn-confirm-del,.btn-pin-unlock{
  position:relative;overflow:hidden;
  border:1px solid rgba(159,231,255,.55);
  background:
    linear-gradient(135deg, rgba(159,231,255,.2), rgba(255,52,66,.2) 52%, rgba(159,231,255,.12)),
    rgba(12,16,24,.86);
  color:#fff;
  box-shadow:0 0 18px rgba(159,231,255,.14), inset 0 0 18px rgba(255,255,255,.08);
}
.topbar-right .btn-del-top::after,.btn-cancel::after,.btn-submit::after,.btn-confirm-del::after,.btn-pin-unlock::after{
  content:'';position:absolute;inset:-60% -35%;pointer-events:none;
  background:linear-gradient(115deg, transparent 35%, rgba(255,255,255,.34) 48%, transparent 61%);
  transform:translateX(-70%);transition:transform .35s ease;
}
.topbar-right .btn-del-top:hover,.btn-cancel:hover,.btn-submit:hover,.btn-confirm-del:hover,.btn-pin-unlock:hover{
  border-color:rgba(255,179,184,.78);
  box-shadow:0 0 24px rgba(255,52,66,.26),0 0 18px rgba(159,231,255,.2), inset 0 0 18px rgba(255,255,255,.12);
  filter:brightness(1.08);
}
.topbar-right .btn-del-top:hover::after,.btn-cancel:hover::after,.btn-submit:hover::after,.btn-confirm-del:hover::after,.btn-pin-unlock:hover::after{transform:translateX(70%);}
</style>
</head>
<body>
<header class="topbar">
  <a href="index.php" class="logo">
    <div class="logo-icon">
      <img src="assets/img/logo.png" alt="Bike Concept Vault">
    </div>
    <div class="logo-text">
      <span class="top">BIKE CONCEPT</span>
      <span class="bot">VAULT</span>
    </div>
  </a>
  <div class="topbar-right">
    <a href="index.php">&#8592; Back to Vault</a>
    <a href="#" class="btn-del-top" onclick="return requireVaultPin(event, () => document.getElementById('deleteModal').classList.add('show'), 'DELETE BIKE')">&#128465; Delete This Bike</a>
  </div>
</header>

<div class="page">
  <div class="page-hdr">
    <div>
      <h1>&#9998; EDIT BIKE</h1>
      <p>Update information for <?= htmlspecialchars($bike['model'] . ' ' . $bike['bike_name']) ?></p>
    </div>
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
          <?php
            $selectedCategoryId = (int)$bike['category_id'];
            $selectedCategory = null;
            foreach ($categories as $cat) {
                if ((int)$cat['category_id'] === $selectedCategoryId) {
                    $selectedCategory = $cat;
                    break;
                }
            }
          ?>
          <div class="category-picker" data-category-picker>
            <button class="category-trigger" type="button" aria-haspopup="listbox" aria-expanded="false">
              <span class="category-trigger-main">
                <?php if ($selectedCategory): ?>
                  <?= flagImg($selectedCategory['country_code'], $selectedCategory['country_code'], 'category-flag') ?>
                  <span class="category-trigger-text"><?= htmlspecialchars($selectedCategory['category_name']) ?></span>
                  <span class="category-trigger-code">(<?= htmlspecialchars($selectedCategory['country_code']) ?>)</span>
                <?php else: ?>
                  <span class="category-trigger-text">Select Category</span>
                <?php endif; ?>
              </span>
              <span class="category-trigger-arrow">&#9662;</span>
            </button>
            <div class="category-menu" role="listbox">
              <?php foreach ($categories as $cat): ?>
              <button class="category-option <?= $selectedCategoryId === (int)$cat['category_id'] ? 'active' : '' ?>"
                      type="button"
                      data-value="<?= (int)$cat['category_id'] ?>"
                      data-label="<?= htmlspecialchars($cat['category_name'], ENT_QUOTES) ?>"
                      data-code="<?= htmlspecialchars($cat['country_code'], ENT_QUOTES) ?>"
                      role="option">
                <span class="category-option-main">
                  <?= flagImg($cat['country_code'], $cat['country_code'], 'category-flag') ?>
                  <span class="category-option-text"><?= htmlspecialchars($cat['category_name']) ?></span>
                </span>
                <span class="category-option-code">(<?= htmlspecialchars($cat['country_code']) ?>)</span>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
          <select name="category_id">
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
      </div>
      <div class="form-grid full">
        <div class="form-group">
          <label>Description</label>
          <textarea name="description"><?= htmlspecialchars($bike['description'] ?? '') ?></textarea>
        </div>
      </div>

      <hr class="divider">

      <div class="section-title">Bike Image</div>
      <?php $curImg = $bike['image_url']; ?>
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
    <p>Delete <strong style="color:var(--red2)"><?= htmlspecialchars($bike['model'] . ' ' . $bike['bike_name']) ?></strong>?<br>This action cannot be undone.</p>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="document.getElementById('deleteModal').classList.remove('show')">Cancel</button>
      <a href="delete.php?id=<?= $id ?>" class="btn-confirm-del">DELETE</a>
    </div>
  </div>
</div>

<div class="modal-overlay" id="pinModal">
  <div class="modal pin-modal">
    <div class="pin-core">
      <div class="pin-orb">PIN</div>
      <h3 id="pinTitle">VAULT ACCESS</h3>
      <p>Enter the security PIN to continue.</p>
      <input class="pin-input" id="pinInput" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="off" aria-label="Security PIN">
      <div class="pin-error" id="pinError"></div>
      <div class="modal-btns">
        <button class="btn-cancel" type="button" onclick="closePinModal()">Cancel</button>
        <button class="btn-pin-unlock" type="button" onclick="submitVaultPin()">UNLOCK</button>
      </div>
    </div>
  </div>
</div>

<script>
const VAULT_PIN = <?= json_encode($vaultPin) ?>;
let pendingVaultAction = null;

function requireVaultPin(event, action, label = 'VAULT ACCESS') {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  const modal = document.getElementById('pinModal');
  const input = document.getElementById('pinInput');
  const error = document.getElementById('pinError');
  const title = document.getElementById('pinTitle');
  pendingVaultAction = typeof action === 'function' ? action : () => { window.location.href = action; };
  if (title) title.textContent = label;
  if (error) error.textContent = '';
  if (input) input.value = '';
  modal.classList.add('show');
  setTimeout(() => input && input.focus(), 60);
  return false;
}

function submitVaultPin() {
  const input = document.getElementById('pinInput');
  const error = document.getElementById('pinError');
  if (!input || input.value !== VAULT_PIN) {
    if (error) error.textContent = 'Invalid PIN';
    if (input) {
      input.value = '';
      input.focus();
    }
    return;
  }
  const action = pendingVaultAction;
  closePinModal();
  if (action) action();
}

function closePinModal() {
  const modal = document.getElementById('pinModal');
  if (modal) modal.classList.remove('show');
  pendingVaultAction = null;
}

function initCategoryPickers() {
  document.querySelectorAll('[data-category-picker]').forEach(picker => {
    const trigger = picker.querySelector('.category-trigger');
    const triggerMain = picker.querySelector('.category-trigger-main');
    const select = picker.parentElement.querySelector('select[name="category_id"]');
    const options = Array.from(picker.querySelectorAll('.category-option'));

    trigger.addEventListener('click', () => {
      const isOpen = picker.classList.toggle('open');
      trigger.setAttribute('aria-expanded', String(isOpen));
    });

    options.forEach(option => {
      option.addEventListener('click', () => {
        if (select) select.value = option.dataset.value;
        options.forEach(item => item.classList.remove('active'));
        option.classList.add('active');
        triggerMain.innerHTML = option.querySelector('.category-option-main').innerHTML +
          '<span class="category-trigger-code">(' + option.dataset.code + ')</span>';
        picker.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
      });
    });
  });
}

document.addEventListener('click', event => {
  document.querySelectorAll('[data-category-picker].open').forEach(picker => {
    if (!picker.contains(event.target)) {
      picker.classList.remove('open');
      const trigger = picker.querySelector('.category-trigger');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }
  });
});

initCategoryPickers();

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
document.getElementById('pinModal').addEventListener('click', function(e){
  if(e.target === this) closePinModal();
});
document.getElementById('pinInput').addEventListener('keydown', function(e){
  if (e.key === 'Enter') submitVaultPin();
  if (e.key === 'Escape') closePinModal();
});
</script>
</body>
</html>
