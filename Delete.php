<?php
// ============================================================
//  BIKE CONCEPT VAULT — Delete Bike (delete.php)
// ============================================================
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// ── Verify the bike exists ───────────────────────────────────
$stmt = $pdo->prepare("SELECT bike_id, bike_name, model, image_url FROM bikes WHERE bike_id = ?");
$stmt->execute([$id]);
$bike = $stmt->fetch();

if (!$bike) {
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Collect all variant image files before deleting
    $imgStmt = $pdo->prepare("SELECT image_url FROM bike_variants WHERE bike_id = ?");
    $imgStmt->execute([$id]);
    $variantImages = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete bike — variants and inventory cascade automatically
    $pdo->prepare("DELETE FROM bikes WHERE bike_id = ?")->execute([$id]);

    $pdo->commit();

    // Clean up uploaded image files from disk
    $allImages = array_filter(array_merge(
        [$bike['image_url']],
        $variantImages
    ));
    foreach ($allImages as $imgPath) {
        if ($imgPath && file_exists($imgPath)) {
            @unlink($imgPath);
        }
    }

    header('Location: index.php?deleted=1');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // On error, redirect back to index with error message
    header('Location: index.php?delete_error=' . urlencode($e->getMessage()));
    exit;
}