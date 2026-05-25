<?php
require_once 'db.php';

$appSettings = getAppSettings($pdo);
$themePrimary = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_primary'] ?? ''))
    ? strtoupper($appSettings['theme_primary'])
    : '#FF3442';
$themeAccent = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_accent'] ?? ''))
    ? strtoupper($appSettings['theme_accent'])
    : '#9FE7FF';
$themeSurface = preg_match('/^#[0-9a-fA-F]{6}$/', (string)($appSettings['theme_surface'] ?? ''))
    ? strtoupper($appSettings['theme_surface'])
    : '#04070B';

$errors = [];
$success = false;

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();

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

function uploadPartImage(array $file): string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return '';

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ftype = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$ftype])) {
        throw new RuntimeException('Part images must be JPG, PNG, or WEBP.');
    }

    if (!is_dir('uploads/parts')) mkdir('uploads/parts', 0755, true);
    $filename = 'uploads/parts/' . uniqid('part_') . '.' . $allowed[$ftype];
    if (!move_uploaded_file($file['tmp_name'], $filename)) {
        throw new RuntimeException('Failed to upload part image.');
    }
    return $filename;
}

function normalizeDuplicateText(?string $value): string {
    return strtolower(trim(preg_replace('/\s+/', ' ', (string)$value)));
}

function duplicateTokens(string $value): array {
    $normalized = preg_replace('/[^a-z0-9]+/', ' ', normalizeDuplicateText($value));
    $tokens = array_filter(explode(' ', trim($normalized)), fn($token) => strlen($token) >= 3);
    return array_values(array_unique($tokens));
}

function duplicateTextLooksSimilar(string $left, string $right): bool {
    $left = normalizeDuplicateText($left);
    $right = normalizeDuplicateText($right);
    if ($left === $right) return true;
    if ($left === '' || $right === '') return false;
    if (str_contains($left, $right) || str_contains($right, $left)) return true;

    similar_text($left, $right, $percent);
    if ($percent >= 82) return true;

    $leftTokens = duplicateTokens($left);
    $rightTokens = duplicateTokens($right);
    if (!$leftTokens || !$rightTokens) return false;

    $shared = array_intersect($leftTokens, $rightTokens);
    return count($shared) >= min(count($leftTokens), count($rightTokens));
}

function averageImageHash(string $path): ?string {
    if (!function_exists('imagecreatetruecolor') || !is_file($path)) return null;

    $info = @getimagesize($path);
    $mime = $info['mime'] ?? '';
    $source = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png'  => @imagecreatefrompng($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        'image/gif'  => @imagecreatefromgif($path),
        default      => false,
    };
    if (!$source) return null;

    $thumb = imagecreatetruecolor(8, 8);
    imagecopyresampled(
        $thumb,
        $source,
        0,
        0,
        0,
        0,
        8,
        8,
        imagesx($source),
        imagesy($source)
    );

    $pixels = [];
    $total = 0;
    for ($y = 0; $y < 8; $y++) {
        for ($x = 0; $x < 8; $x++) {
            $rgb = imagecolorat($thumb, $x, $y);
            $r = ($rgb >> 16) & 255;
            $g = ($rgb >> 8) & 255;
            $b = $rgb & 255;
            $gray = (int)round(($r + $g + $b) / 3);
            $pixels[] = $gray;
            $total += $gray;
        }
    }

    imagedestroy($thumb);
    imagedestroy($source);

    $average = $total / count($pixels);
    return implode('', array_map(fn($gray) => $gray >= $average ? '1' : '0', $pixels));
}

function hammingDistance(string $left, string $right): int {
    $distance = abs(strlen($left) - strlen($right));
    $limit = min(strlen($left), strlen($right));
    for ($i = 0; $i < $limit; $i++) {
        if ($left[$i] !== $right[$i]) $distance++;
    }
    return $distance;
}

function imagesMatchDuplicate(string $uploadedPath, ?string $existingPath): bool {
    $existingPath = trim((string)$existingPath);
    if ($uploadedPath === '' || $existingPath === '') return $uploadedPath === $existingPath;
    if (!is_file($uploadedPath) || !is_file($existingPath)) return false;

    $uploadedSha = @sha1_file($uploadedPath);
    $existingSha = @sha1_file($existingPath);
    if ($uploadedSha && $existingSha && hash_equals($existingSha, $uploadedSha)) return true;

    $uploadedHash = averageImageHash($uploadedPath);
    $existingHash = averageImageHash($existingPath);
    return $uploadedHash !== null
        && $existingHash !== null
        && hammingDistance($uploadedHash, $existingHash) <= 5;
}

function motorcycleDuplicateExists(
    PDO $pdo,
    int $categoryId,
    string $bikeName,
    string $model,
    string $edition,
    float $price,
    string $description,
    string $imagePath
): bool {
    $stmt = $pdo->prepare("
        SELECT b.bike_id, b.bike_name, b.model, b.edition, b.price, b.description,
               b.image_url, bv.image_url AS variant_img
        FROM bikes b
        LEFT JOIN bike_variants bv ON bv.bike_id = b.bike_id AND bv.is_default = 1
        WHERE b.category_id = ?
    ");
    $stmt->execute([$categoryId]);

    $target = [
        'bike_name' => normalizeDuplicateText($bikeName),
        'model' => normalizeDuplicateText($model),
        'edition' => normalizeDuplicateText($edition),
        'price' => number_format($price, 2, '.', ''),
        'description' => normalizeDuplicateText($description),
    ];

    foreach ($stmt->fetchAll() as $bike) {
        $sameDetails =
            normalizeDuplicateText($bike['bike_name'] ?? '') === $target['bike_name'] &&
            normalizeDuplicateText($bike['model'] ?? '') === $target['model'] &&
            normalizeDuplicateText($bike['edition'] ?? '') === $target['edition'] &&
            number_format((float)($bike['price'] ?? 0), 2, '.', '') === $target['price'] &&
            normalizeDuplicateText($bike['description'] ?? '') === $target['description'];

        $existingImage = $bike['variant_img'] ?: ($bike['image_url'] ?? '');
        $sameImage = imagesMatchDuplicate($imagePath, $existingImage);
        $samePrice = number_format((float)($bike['price'] ?? 0), 2, '.', '') === $target['price'];
        $similarIdentity =
            duplicateTextLooksSimilar($bikeName, $bike['bike_name'] ?? '') &&
            duplicateTextLooksSimilar($model, $bike['model'] ?? '');

        if ($sameImage && ($sameDetails || ($samePrice && $similarIdentity))) {
            return true;
        }
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $bike_name   = trim($_POST['bike_name']   ?? '');
    $model       = trim($_POST['model']       ?? '');
    $edition     = trim($_POST['edition']     ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $part_names = $_POST['part_name'] ?? [];

    if (!$is_featured) {
        $hasFeatured = (int)$pdo->query("SELECT COUNT(*) FROM bikes WHERE is_featured = 1")->fetchColumn();
        if ($hasFeatured === 0) $is_featured = 1;
    }

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

    if (
        empty($errors) &&
        motorcycleDuplicateExists($pdo, $category_id, $bike_name, $model, $edition, $price, $description, $image_url)
    ) {
        if ($image_url && is_file($image_url)) @unlink($image_url);
        $image_url = '';
        $errors[] = 'This motorcycle already exists in the system.';
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

            $variantStmt = $pdo->prepare("
                INSERT INTO bike_variants (bike_id, color_name, image_url, is_default)
                VALUES (?, ?, ?, 1)
            ");
            $variantStmt->execute([$bike_id, 'Default', $image_url]);

            $partStmt = $pdo->prepare("
                INSERT INTO bike_parts (bike_id, part_type, category, part_name, brand, description, price, quantity, image_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($part_names as $idx => $part_name_raw) {
                $part_name = trim($part_name_raw);
                if ($part_name === '') continue;

                $partFile = [
                    'name' => $_FILES['part_image']['name'][$idx] ?? '',
                    'type' => $_FILES['part_image']['type'][$idx] ?? '',
                    'tmp_name' => $_FILES['part_image']['tmp_name'][$idx] ?? '',
                    'error' => $_FILES['part_image']['error'][$idx] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $_FILES['part_image']['size'][$idx] ?? 0,
                ];

                $partStmt->execute([
                    $bike_id,
                    $_POST['part_type'][$idx] ?? 'Accessory',
                    $_POST['part_category'][$idx] ?? 'Accessories',
                    $part_name,
                    trim($_POST['part_brand'][$idx] ?? ''),
                    trim($_POST['part_description'][$idx] ?? ''),
                    max(0, (float)($_POST['part_price'][$idx] ?? 0)),
                    max(1, (int)($_POST['part_quantity'][$idx] ?? 1)),
                    uploadPartImage($partFile),
                ]);
            }

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
.topbar-right a{color:var(--textd);text-decoration:none;font-size:13px;font-weight:700;padding:7px 14px;border-radius:6px;transition:all .2s;}
.topbar-right a:hover{color:var(--cyan);background:rgba(159,231,255,.08);}

.page{max-width:1040px;margin:0 auto;padding:34px 28px 42px;}
.page-hdr{margin-bottom:28px;text-shadow:0 0 20px rgba(255,52,66,.48);}
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

.img-upload{
  border:1px dashed rgba(159,231,255,.72);border-radius:8px;min-height:190px;padding:34px;text-align:center;
  cursor:pointer;transition:border-color .2s, box-shadow .2s;position:relative;display:flex;align-items:center;justify-content:center;
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
.img-preview{max-width:100%;max-height:180px;object-fit:contain;border-radius:6px;display:none;}

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

@media(max-width:720px){
  .page{padding:26px 16px 32px;}
  .form-card{padding:26px 18px 22px;}
  .form-grid{grid-template-columns:1fr;gap:16px;}
  .form-actions{flex-direction:column-reverse;}
  .btn-cancel,.btn-submit{text-align:center;width:100%;}
}

/* Holographic buttons only */
.btn-cancel,.btn-submit{
  position:relative;overflow:hidden;
  border:1px solid rgba(159,231,255,.55);
  background:
    linear-gradient(135deg, rgba(159,231,255,.2), rgba(255,52,66,.2) 52%, rgba(159,231,255,.12)),
    rgba(12,16,24,.86);
  color:#fff;
  box-shadow:0 0 18px rgba(159,231,255,.14), inset 0 0 18px rgba(255,255,255,.08);
}
.btn-cancel::after,.btn-submit::after{
  content:'';position:absolute;inset:-60% -35%;pointer-events:none;
  background:linear-gradient(115deg, transparent 35%, rgba(255,255,255,.34) 48%, transparent 61%);
  transform:translateX(-70%);transition:transform .35s ease;
}
.btn-cancel:hover,.btn-submit:hover{
  border-color:rgba(255,179,184,.78);
  box-shadow:0 0 24px rgba(255,52,66,.26),0 0 18px rgba(159,231,255,.2), inset 0 0 18px rgba(255,255,255,.12);
  filter:brightness(1.08);
}
.btn-cancel:hover::after,.btn-submit:hover::after{transform:translateX(70%);}
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
          <?php $selectedCategoryId = (int)($old['category_id'] ?? 0); ?>
          <div class="category-picker" data-category-picker>
            <button class="category-trigger" type="button" aria-haspopup="listbox" aria-expanded="false">
              <span class="category-trigger-main">
                <?php
                  $selectedCategory = null;
                  foreach ($categories as $cat) {
                      if ((int)$cat['category_id'] === $selectedCategoryId) {
                          $selectedCategory = $cat;
                          break;
                      }
                  }
                ?>
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

      <div class="form-actions">
        <a href="index.php" class="btn-cancel">Cancel</a>
        <button type="submit" class="btn-submit">&#43; CREATE BIKE</button>
      </div>
    </form>
  </div>
</div>

<script>
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

