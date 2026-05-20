<?php
require_once 'db.php';

define('FALLBACK_BIKE_IMG', 'data:image/jpeg;base64,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ластакес…');

$featured = $pdo->query("
    SELECT b.*, c.country_code, c.flag_emoji,
           bv.color_name, bv.image_url AS variant_img,
           i.stock_qty, i.stock_status
    FROM bikes b
    JOIN categories    c  ON c.category_id = b.category_id
    JOIN bike_variants bv ON bv.bike_id    = b.bike_id AND bv.is_default = 1
    JOIN inventory     i  ON i.variant_id  = bv.variant_id
    WHERE b.is_featured = 1
    LIMIT 1
")->fetch();

$variants = [];
if ($featured) {
    $stmt = $pdo->prepare("
        SELECT bv.*, i.stock_qty, i.stock_status
        FROM bike_variants bv
        JOIN inventory i ON i.variant_id = bv.variant_id
        WHERE bv.bike_id = ?
        ORDER BY bv.is_default DESC, bv.variant_id ASC
    ");
    $stmt->execute([$featured['bike_id']]);
    $variants = $stmt->fetchAll();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_id")->fetchAll();

$catHeroes = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("
        SELECT b.*, c.country_code, c.flag_emoji,
               bv.color_name, bv.image_url AS variant_img,
               i.stock_qty, i.stock_status
        FROM bikes b
        JOIN categories    c  ON c.category_id = b.category_id
        JOIN bike_variants bv ON bv.bike_id = b.bike_id AND bv.is_default = 1
        JOIN inventory     i  ON i.variant_id = bv.variant_id
        WHERE b.category_id = ?
        ORDER BY b.is_featured DESC, b.bike_id DESC
        LIMIT 1
    ");
    $stmt->execute([$cat['category_id']]);
    $row = $stmt->fetch();
    if ($row) $catHeroes[$cat['category_id']] = $row;
}

$sql = "
    SELECT b.bike_id, b.bike_name, b.model, b.price, b.image_url, b.is_featured,
           b.category_id,
           c.country_code, c.flag_emoji,
           bv.color_name, bv.image_url AS variant_img,
           i.stock_qty, i.stock_status
    FROM bikes b
    JOIN categories    c  ON c.category_id  = b.category_id
    JOIN bike_variants bv ON bv.bike_id     = b.bike_id AND bv.is_default = 1
    JOIN inventory     i  ON i.variant_id   = bv.variant_id
    ORDER BY b.is_featured DESC, b.bike_id DESC
";
$bikes = $pdo->query($sql)->fetchAll();

$partsByBike = [];
$partRows = $pdo->query("
    SELECT part_id, bike_id, part_type, category, part_name, brand, description, price, quantity, image_url
    FROM bike_parts
    ORDER BY part_id ASC
")->fetchAll();
foreach ($partRows as $part) {
    $partsByBike[(int)$part['bike_id']][] = [
        'id' => (int)$part['part_id'],
        'type' => $part['part_type'],
        'category' => $part['category'],
        'name' => $part['part_name'],
        'brand' => $part['brand'] ?? '',
        'description' => $part['description'] ?? '',
        'price' => (float)$part['price'],
        'qty' => (int)$part['quantity'],
        'image' => $part['image_url'] ?? '',
    ];
}

function stockBadge(string $status): string {
    $map = ['In Stock' => 'badge-green', 'Low Stock' => 'badge-yellow', 'Out of Stock' => 'badge-red'];
    $cls = $map[$status] ?? 'badge-red';
    return "<span class='badge $cls'>$status</span>";
}

function resolveImg(?string $dbPath, string $fallback): string {
    if ($dbPath && (str_starts_with($dbPath, 'http') || file_exists($dbPath))) {
        return htmlspecialchars($dbPath);
    }
    return $fallback;
}

// Maps country_code (e.g. "JPN", "PHL") → ISO 3166-1 alpha-2 (e.g. "jp", "ph")
// Used by flagcdn.com: https://flagcdn.com/24x18/{code}.png
function flagIso(string $countryCode): string {
    $map = [
        // Philippines — Pinoy / PHL / PHI / PHIL
        'PHL' => 'ph', 'PHI' => 'ph', 'PHIL' => 'ph', 'PINOY' => 'ph', 'PIN' => 'ph',
        // Indonesia — Indo / IDN / INA
        'IDN' => 'id', 'INA' => 'id', 'INDO' => 'id',
        // Malaysia — Malay / MYS / MAS
        'MYS' => 'my', 'MAS' => 'my', 'MAL' => 'my', 'MALAY' => 'my',
        // Thailand — Thai / THA
        'THA' => 'th', 'THAI' => 'th',
        // Other Asia-Pacific
        'JPN' => 'jp', 'JAP' => 'jp',
        'KOR' => 'kr', 'CHN' => 'cn', 'TWN' => 'tw',
        'SGP' => 'sg', 'VNM' => 'vn', 'VNE' => 'vn',
        'IND' => 'in', 'PAK' => 'pk', 'BGD' => 'bd', 'LKA' => 'lk',
        // Europe
        'GBR' => 'gb', 'DEU' => 'de', 'FRA' => 'fr', 'ITA' => 'it',
        'ESP' => 'es', 'NLD' => 'nl', 'SWE' => 'se', 'NOR' => 'no',
        // Americas & Oceania
        'USA' => 'us', 'CAN' => 'ca', 'MEX' => 'mx', 'BRA' => 'br',
        'AUS' => 'au', 'NZL' => 'nz',
        // fallback: lowercase first 2 chars
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
                 alt=\"" . htmlspecialchars($alt ?: $countryCode) . "\"
                 onerror=\"this.style.display='none'\"
                 {$clsAttr}>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bike Concept Vault</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
<style>
/* ── RESET & VARS ─────────────────────────────────────────────── */
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#080808;
  --bg2:#0e0e0e;
  --bg3:#161616;
  --bg4:#1c1c1c;
  --red:#e8000d;
  --red2:#ff2030;
  --red3:#ff5060;
  --reddim:rgba(232,0,13,.07);
  --redbright:rgba(232,0,13,.15);
  --border:#180001;
  --border2:#2c0004;
  --border3:#3d0005;
  --text:#eeeeee;
  --textd:#666;
  --textdd:#2c2c2c;
  --radius:8px;
  --card-radius:11px;
}
body{
  background:var(--bg);
  color:var(--text);
  font-family:'Rajdhani',sans-serif;
  min-height:100vh;
  overflow-x:hidden;
}

/* ── TOPBAR ───────────────────────────────────────────────────── */
.topbar{
  background:rgba(8,8,8,.97);
  border-bottom:1px solid var(--border2);
  padding:0 28px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  height:60px;
  position:sticky;
  top:0;
  z-index:100;
  backdrop-filter:blur(14px);
}
/* red accent line under topbar */
.topbar::after{
  content:'';
  position:absolute;
  bottom:0;left:0;right:0;
  height:1px;
  background:linear-gradient(90deg,var(--red) 0%,rgba(232,0,13,.25) 45%,transparent 80%);
}

.logo{
  display:flex;align-items:center;gap:10px;
  text-decoration:none;
  font-family:'Orbitron',monospace;
  color:var(--text);
}
.logo-icon{
  width:34px;height:34px;
  background:var(--red);
  border-radius:7px;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.logo-icon svg{width:20px;height:20px;fill:none;stroke:#fff;stroke-width:1.6;}
.logo-text .top{color:var(--red2);font-size:11.5px;font-weight:900;letter-spacing:.5px;display:block;}
.logo-text .bot{color:var(--textd);font-size:8.5px;letter-spacing:3.5px;display:block;}

.cat-nav{display:flex;align-items:center;gap:2px;margin:0 14px;}
.cat-nav a{
  display:flex;align-items:center;gap:5px;
  padding:6px 13px;border-radius:6px;
  font-size:10.5px;font-weight:700;letter-spacing:.8px;
  text-decoration:none;color:var(--textd);
  transition:all .15s;border:1px solid transparent;
  text-transform:uppercase;cursor:pointer;
}
.cat-nav a .flag{
  display:inline-flex;align-items:center;
  width:20px;height:15px;overflow:hidden;border-radius:2px;
  flex-shrink:0;
}
.cat-nav a .flag img{width:100%;height:100%;object-fit:cover;display:block;border-radius:2px;}
.cat-nav a:hover{color:var(--text);background:var(--bg3);border-color:var(--border2);}
.cat-nav a.active{color:var(--red2);background:var(--reddim);border-color:var(--border3);}

.topbar-right{display:flex;align-items:center;gap:8px;}
.search-box{
  background:var(--bg3);
  border:1px solid var(--border2);
  border-radius:6px;
  padding:0 12px;
  display:flex;align-items:center;gap:7px;
  width:210px;height:36px;
  transition:border-color .15s;
}
.search-box:focus-within{border-color:var(--red);}
.search-box svg{width:14px;height:14px;stroke:var(--textd);flex-shrink:0;}
.search-box input{
  background:none;border:none;outline:none;
  color:var(--text);font-family:'Rajdhani',sans-serif;
  font-size:13px;width:100%;
}
.search-box input::placeholder{color:var(--textdd);}
.search-clear{
  background:none;border:none;color:var(--textd);
  cursor:pointer;font-size:13px;padding:0;line-height:1;
  display:none;transition:color .15s;flex-shrink:0;
}
.search-clear:hover{color:var(--red2);}
.search-clear.visible{display:block;}

/* ADD NEW button in topbar */
.btn-add-new{
  display:flex;align-items:center;gap:6px;
  background:var(--red);border:none;border-radius:6px;
  padding:0 16px;height:36px;
  color:#fff;font-family:'Orbitron',monospace;
  font-size:9.5px;font-weight:700;letter-spacing:1px;
  cursor:pointer;text-decoration:none;
  transition:background .15s;white-space:nowrap;
}
.btn-add-new:hover{background:var(--red2);}
.btn-add-new svg{width:13px;height:13px;stroke:#fff;stroke-width:2.2;}

/* ── HERO ─────────────────────────────────────────────────────── */
.hero{
  position:relative;
  background:#000;
  min-height:490px;
  display:flex;
  border-bottom:1px solid var(--border2);
  overflow:hidden;
}
/* dark overlay on hero-left so bike stays readable over background */
.hero-left::before{
  content:'';
  position:absolute;inset:0;z-index:1;
  pointer-events:none;
  background:linear-gradient(
    to right,
    rgba(0,0,0,.38) 0%,
    rgba(0,0,0,.18) 50%,
    rgba(0,0,0,.55) 100%
  );
}
.hero-top-line{
  position:absolute;top:0;left:0;right:0;
  height:2px;
  background:linear-gradient(90deg,var(--red) 0%,rgba(232,0,13,.3) 50%,transparent 100%);
  z-index:10;
}

/* ── HERO LEFT ── */
.hero-left{
  flex:1;position:relative;
  display:flex;align-items:center;justify-content:center;
  padding:30px 30px 128px 50px;
  overflow:hidden;
  background-image:url('data:image/png;base64,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');
  background-size:cover;
  background-position:center center;
  background-repeat:no-repeat;
}

.hero-wm{
  position:absolute;
  bottom:112px;left:42px;
  z-index:2;pointer-events:none;line-height:1;
}
.hero-wm-brand{
  font-family:'Orbitron',monospace;
  font-size:92px;font-weight:900;
  font-style:italic;letter-spacing:-4px;line-height:.88;
}
.hero-wm-brand .part-red{color:var(--red);}
.hero-wm-brand .part-white{color:rgba(255,255,255,.95);}
.hero-wm-sub{
  font-size:11px;letter-spacing:8px;color:var(--textd);
  margin-top:10px;text-transform:uppercase;font-style:normal;
}

.hero-bike-img{
  position:relative;z-index:3;
  max-width:620px;max-height:360px;width:100%;
  object-fit:contain;
  filter:drop-shadow(0 20px 55px rgba(232,0,13,.16)) drop-shadow(0 8px 20px rgba(0,0,0,.7));
  transform:scale(1.05);
  transition:opacity .28s ease;
}

/* ── VARIANT BAR ── */
.variants-bar{
  position:absolute;bottom:0;left:0;right:0;z-index:20;
  display:flex;flex-direction:column;gap:6px;
  padding:10px 42px 14px;
  background:linear-gradient(to top,rgba(0,0,0,.76) 55%,transparent 100%);
  pointer-events:auto;
}
.var-choose{
  font-size:9.5px;font-weight:700;letter-spacing:2px;
  color:var(--textd);text-transform:uppercase;
}
.variants-bottom{display:flex;gap:10px;flex-wrap:wrap;}
.var-item{
  display:flex;flex-direction:column;align-items:center;gap:4px;
  cursor:pointer;
}
.var-thumb{
  width:56px;height:48px;border-radius:6px;
  background:var(--bg3);border:2px solid var(--border2);
  overflow:hidden;display:flex;align-items:center;justify-content:center;
  transition:border-color .15s;
}
.var-thumb img{width:100%;height:100%;object-fit:cover;}
.var-swatch{width:100%;height:100%;}
.var-item.active .var-thumb,
.var-item:hover .var-thumb{border-color:var(--red2);}
.var-name{
  font-size:8.5px;font-weight:700;color:var(--textd);
  letter-spacing:.4px;text-transform:uppercase;
  text-align:center;max-width:60px;line-height:1.2;
}

/* ── HERO RIGHT PANEL ── */
.hero-right{
  width:296px;flex-shrink:0;
  background:rgba(6,6,6,.78);
  border-left:1px solid var(--border2);
  padding:0 24px 28px;
  display:flex;flex-direction:column;justify-content:flex-end;
  position:relative;z-index:5;
  backdrop-filter:blur(10px);
}
/* colored top accent */
.hero-right::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--red),transparent);
}

.hr-eyebrow{
  display:flex;align-items:center;gap:8px;
  font-size:9px;font-weight:700;letter-spacing:2.5px;
  color:var(--textd);text-transform:uppercase;
  margin-bottom:10px;padding-top:28px;
}
.hr-eyebrow::before{content:'';width:16px;height:1px;background:var(--red);}

.hero-price{
  font-family:'Orbitron',monospace;
  font-size:36px;font-weight:900;
  color:var(--red2);letter-spacing:-1.5px;line-height:1;
  margin-bottom:4px;
}
.hero-model{
  font-family:'Orbitron',monospace;
  font-size:12px;font-weight:700;
  color:var(--text);letter-spacing:.6px;
  margin-bottom:14px;
}

.stock-pill{
  display:inline-flex;align-items:center;gap:6px;
  padding:5px 14px;border-radius:20px;
  font-size:10.5px;font-weight:700;letter-spacing:.5px;
  text-transform:uppercase;margin-bottom:14px;width:fit-content;
}
.stock-pill.green{background:var(--red);color:#fff;}
.stock-pill.yellow{background:rgba(234,179,8,.12);color:#eab308;border:1px solid rgba(234,179,8,.25);}
.stock-pill.red-pill{background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.18);}
.stock-dot{width:5px;height:5px;border-radius:50%;background:currentColor;}

.hero-desc{
  font-size:12.5px;color:var(--textd);line-height:1.9;
  margin-bottom:18px;
}

/* mini stats grid */
.hero-stats{
  display:grid;grid-template-columns:1fr 1fr;
  gap:8px;margin-bottom:18px;
}
.hstat{
  background:rgba(255,255,255,.025);
  border:1px solid var(--border);
  border-radius:6px;padding:7px 10px;
}
.hstat-lbl{
  font-size:8px;font-weight:700;letter-spacing:1.5px;
  color:var(--textd);text-transform:uppercase;margin-bottom:3px;
}
.hstat-val{
  font-family:'Orbitron',monospace;
  font-size:11px;font-weight:700;color:var(--text);
  display:flex;align-items:center;gap:5px;
}
.hstat-flag{
  width:20px;height:14px;border-radius:2px;
  object-fit:cover;vertical-align:middle;flex-shrink:0;
}

.hero-btns{display:flex;gap:8px;}
.btn-hero-edit{
  flex:1;background:#fff;color:#111;border:none;border-radius:7px;
  padding:11px 14px;
  font-family:'Orbitron',monospace;font-size:10px;font-weight:700;
  letter-spacing:1px;cursor:pointer;text-decoration:none;
  display:flex;align-items:center;justify-content:center;gap:6px;
  transition:background .15s;
}
.btn-hero-edit:hover{background:#e0e0e0;}
.btn-hero-edit svg{width:13px;height:13px;stroke:#111;stroke-width:2;}
.btn-hero-add{
  flex:1;background:var(--bg3);color:var(--textd);
  border:1px solid var(--border2);border-radius:7px;
  padding:11px 14px;
  font-family:'Orbitron',monospace;font-size:10px;font-weight:700;
  letter-spacing:1px;cursor:pointer;text-decoration:none;
  display:flex;align-items:center;justify-content:center;gap:6px;
  transition:all .15s;
}
.btn-hero-add:hover{border-color:var(--red);color:var(--red3);}
.btn-hero-add svg{width:13px;height:13px;stroke:currentColor;stroke-width:2;}

.sparkle{
  position:absolute;bottom:22px;right:22px;
  font-size:26px;color:rgba(255,255,255,.1);z-index:4;
}

/* ── INVENTORY ────────────────────────────────────────────────── */
.page-content{padding:24px 28px;}

/* BUILD PARTS SYSTEM */
.build-system{
  padding:24px 28px 0;
  background:linear-gradient(180deg,#0a0a0a 0%,var(--bg) 100%);
}
.build-shell{
  border:1px solid var(--border2);
  border-radius:var(--card-radius);
  background:var(--bg2);
  overflow:hidden;
  box-shadow:0 18px 50px rgba(0,0,0,.34);
}
.build-head{
  display:flex;align-items:flex-start;justify-content:space-between;gap:16px;
  padding:18px 20px;
  border-bottom:1px solid var(--border);
  background:linear-gradient(90deg,rgba(232,0,13,.12),rgba(255,255,255,.015));
}
.build-kicker{
  font-family:'Orbitron',monospace;font-size:9px;font-weight:700;
  letter-spacing:2px;color:var(--red2);text-transform:uppercase;margin-bottom:6px;
}
.build-title{
  font-family:'Orbitron',monospace;font-size:15px;font-weight:900;
  letter-spacing:.5px;color:var(--text);line-height:1.3;
}
.build-sub{font-size:12px;color:var(--textd);margin-top:5px;}
.build-total{
  min-width:178px;text-align:right;
  background:rgba(0,0,0,.32);
  border:1px solid var(--border2);
  border-radius:8px;padding:11px 14px;
}
.build-total span{
  display:block;font-size:9px;font-weight:700;letter-spacing:1.5px;
  color:var(--textd);text-transform:uppercase;margin-bottom:4px;
}
.build-total strong{
  font-family:'Orbitron',monospace;font-size:22px;color:var(--red2);line-height:1;
}
.build-tools{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  padding:14px 16px;border-bottom:1px solid var(--border);
  flex-wrap:wrap;
}
.part-tabs{display:flex;gap:6px;flex-wrap:wrap;}
.part-tab{
  background:var(--bg3);border:1px solid var(--border2);border-radius:5px;
  color:var(--textd);font-family:'Rajdhani',sans-serif;font-size:10px;
  font-weight:800;letter-spacing:.8px;text-transform:uppercase;
  padding:6px 11px;cursor:pointer;transition:all .15s;
}
.part-tab.active,.part-tab:hover{color:var(--text);border-color:var(--red);}
.btn-add-part{
  background:var(--red);border:none;border-radius:6px;color:#fff;
  font-family:'Orbitron',monospace;font-size:9px;font-weight:800;
  letter-spacing:1px;padding:9px 13px;cursor:pointer;
}
.btn-add-part:hover{background:var(--red2);}
.part-add-panel{
  display:none;grid-template-columns:1.2fr 1fr .85fr .72fr .72fr 1.4fr 1fr auto;
  gap:8px;padding:14px 16px;border-bottom:1px solid var(--border);
  background:#0b0b0b;
}
.part-add-panel.show{display:grid;}
.part-add-panel input,.part-add-panel select,.part-add-panel textarea{
  background:var(--bg3);border:1px solid var(--border2);border-radius:6px;
  color:var(--text);font-family:'Rajdhani',sans-serif;font-size:13px;
  padding:9px 10px;min-width:0;
}
.part-add-panel textarea{resize:vertical;min-height:38px;}
.part-add-panel input[type="file"]{padding:7px 10px;color:var(--textd);}
.part-add-panel input:focus,.part-add-panel select:focus{outline:none;border-color:var(--red);}
.part-add-panel button{
  background:#fff;color:#111;border:none;border-radius:6px;
  font-family:'Orbitron',monospace;font-size:9px;font-weight:800;
  letter-spacing:1px;padding:0 14px;cursor:pointer;
}
.parts-list{display:flex;flex-direction:column;}
.part-row{
  display:grid;grid-template-columns:64px minmax(210px,1.25fr) minmax(130px,.8fr) 112px 82px 120px 148px;
  align-items:center;gap:12px;
  padding:13px 16px;border-bottom:1px solid rgba(61,0,5,.55);
  transition:background .15s,border-color .15s;
}
.part-row:hover{background:rgba(255,255,255,.018);}
.part-row:last-child{border-bottom:none;}
.part-thumb{
  width:58px;height:48px;border-radius:8px;
  background:radial-gradient(circle at 45% 35%,#2a2a2a,#090909 70%);
  border:1px solid var(--border2);
  display:flex;align-items:center;justify-content:center;overflow:hidden;
  cursor:pointer;transition:border-color .15s,transform .15s;
  padding:5px;
}
.part-thumb:hover{border-color:var(--red);transform:translateY(-1px);}
.part-thumb img{
  display:block;width:100%;height:100%;object-fit:contain;object-position:center;
  filter:drop-shadow(0 7px 10px rgba(0,0,0,.65));
}
.part-name-input,.part-brand-input,.part-cat-select{
  width:100%;background:#111;border:1px solid transparent;border-radius:6px;
  color:var(--text);font-family:'Rajdhani',sans-serif;font-weight:800;
  padding:6px 8px;min-width:0;
}
.part-name-display{
  font-family:'Orbitron',monospace;font-size:11px;font-weight:800;color:var(--text);
}
.part-brand-display{font-size:11px;color:var(--textd);margin-top:3px;}
.part-desc-display{font-size:11px;color:#777;line-height:1.35;margin-top:6px;max-width:360px;}
.part-meta-display{font-size:11px;color:var(--text);font-weight:800;}
.part-edit-field{display:flex;flex-direction:column;gap:5px;}
.part-edit-label{
  font-size:8px;font-weight:800;letter-spacing:1px;color:var(--textd);text-transform:uppercase;
}
.part-desc-input{
  width:100%;background:#111;border:1px solid transparent;border-radius:6px;
  color:var(--text);font-family:'Rajdhani',sans-serif;font-size:12px;
  padding:7px 8px;resize:vertical;min-height:56px;
}
.part-desc-input:focus{outline:none;border-color:var(--red);box-shadow:0 0 0 2px rgba(232,0,13,.16);}
.part-name-input{
  font-family:'Orbitron',monospace;font-size:10.5px;letter-spacing:.2px;
}
.part-brand-input{font-size:11px;color:var(--textd);margin-top:4px;}
.part-name-input:focus,.part-brand-input:focus,.part-cat-select:focus{
  outline:none;border-color:var(--red);box-shadow:0 0 0 2px rgba(232,0,13,.16);
}
.part-cat{
  display:inline-flex;width:max-content;
  background:rgba(232,0,13,.1);border:1px solid var(--border2);
  color:var(--red3);border-radius:12px;
  padding:3px 9px;font-size:9px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;
}
.part-cat-select{
  color:var(--red3);font-size:10px;text-transform:uppercase;
  background:rgba(232,0,13,.08);border-color:var(--border2);
}
.part-image-tools{display:flex;flex-direction:column;gap:5px;align-items:center;}
.part-image-file{display:none;}
.part-image-btn{
  width:58px;border:1px solid var(--border2);background:var(--bg3);
  color:var(--textd);border-radius:5px;font-size:8px;font-weight:800;
  letter-spacing:.6px;padding:4px 0;cursor:pointer;font-family:'Rajdhani',sans-serif;
  text-align:center;
}
.part-image-btn:hover{border-color:var(--red);color:var(--red3);}
.part-image-remove{
  width:58px;border:1px solid #431014;background:transparent;color:#f87171;
  border-radius:5px;font-size:8px;font-weight:800;letter-spacing:.6px;
  padding:4px 0;cursor:pointer;font-family:'Rajdhani',sans-serif;
}
.part-image-remove:hover{background:rgba(239,68,68,.08);}
.part-price,.part-qty{
  width:100%;background:#111;border:1px solid var(--border2);
  border-radius:6px;color:var(--text);font-family:'Rajdhani',sans-serif;
  font-size:13px;font-weight:700;padding:8px 9px;
}
.part-price:focus,.part-qty:focus{outline:none;border-color:var(--red);box-shadow:0 0 0 2px rgba(232,0,13,.16);}
.part-line-total{
  font-family:'Orbitron',monospace;font-size:12px;font-weight:800;color:var(--text);
  text-align:right;
}
.part-actions{display:flex;gap:6px;justify-content:flex-end;}
.part-actions button{
  border-radius:5px;padding:7px 9px;font-size:9.5px;font-weight:800;
  letter-spacing:.6px;cursor:pointer;font-family:'Rajdhani',sans-serif;
}
.part-edit{background:var(--bg3);border:1px solid var(--border2);color:var(--text);}
.part-save{background:#fff;border:1px solid #fff;color:#111;}
.part-cancel{background:var(--bg3);border:1px solid var(--border2);color:var(--textd);}
.part-remove{background:transparent;border:1px solid #431014;color:#f87171;}
.part-edit:hover{border-color:var(--red);color:var(--red3);}
.part-save:hover{background:#e7e7e7;}
.part-cancel:hover{border-color:var(--border3);color:var(--text);}
.part-remove:hover{background:rgba(239,68,68,.08);}
.parts-empty{
  padding:34px 18px;text-align:center;color:var(--textd);font-size:13px;
}
.image-modal{
  width:min(92vw,680px);max-width:680px;padding:22px;
  border-color:var(--border3);
  box-shadow:0 30px 90px rgba(0,0,0,.78),0 0 0 1px rgba(232,0,13,.18);
  animation:modalPop .18s ease-out;
}
.image-modal-frame{
  width:100%;
  aspect-ratio:1 / 1;
  max-height:min(72vh,640px);
  display:flex;align-items:center;justify-content:center;
  padding:18px;
  background:#050505;
  border:1px solid var(--border2);
  border-radius:10px;
  overflow:hidden;
}
.image-modal-img{
  display:block;
  max-width:100%;
  max-height:100%;
  width:auto;
  height:auto;
  object-fit:contain;
  object-position:center;
  border-radius:6px;
}
.image-modal-title{
  font-family:'Orbitron',monospace;font-size:12px;font-weight:800;
  color:var(--text);margin-bottom:12px;text-align:left;
}
@keyframes modalPop{
  from{opacity:0;transform:translateY(10px) scale(.98);}
  to{opacity:1;transform:translateY(0) scale(1);}
}

@media (max-width:980px){
  .build-head{flex-direction:column;}
  .build-total{width:100%;text-align:left;}
  .part-add-panel{grid-template-columns:1fr 1fr;}
  .part-row{grid-template-columns:58px 1fr 92px;align-items:start;}
  .part-cat,.part-line-total,.part-actions{grid-column:2 / span 2;}
  .part-line-total{text-align:left;}
  .part-actions{justify-content:flex-start;}
}

@media (max-width:640px){
  .build-system{padding:16px 12px 0;}
  .build-head,.build-tools{padding:14px;}
  .part-add-panel{grid-template-columns:1fr;padding:12px;}
  .part-add-panel button{height:38px;}
  .part-row{grid-template-columns:54px 1fr;gap:10px;padding:12px;}
  .part-cat,.part-price,.part-qty,.part-line-total,.part-actions{grid-column:1 / span 2;}
  .part-actions button{flex:1;}
  .image-modal{width:94vw;padding:14px;}
  .image-modal-frame{padding:12px;max-height:70vh;}
}

.section-hdr{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:16px;padding-bottom:13px;
  border-bottom:1px solid var(--border);
}
.section-hdr-left{display:flex;align-items:center;gap:10px;}
.section-hdr h2{
  font-family:'Orbitron',monospace;font-size:10px;
  font-weight:700;letter-spacing:2.5px;color:var(--textd);
}
.section-count{
  font-size:11px;color:var(--textd);
  background:var(--bg3);border:1px solid var(--border2);
  border-radius:12px;padding:2px 10px;
}
.section-hdr .right-side{display:flex;align-items:center;gap:6px;}
.filter-pill{
  background:var(--bg3);border:1px solid var(--border2);
  border-radius:5px;padding:4px 11px;
  font-size:10px;font-weight:700;letter-spacing:.5px;
  color:var(--textd);cursor:pointer;
  transition:all .15s;font-family:'Rajdhani',sans-serif;
}
.filter-pill:hover,.filter-pill.active{border-color:var(--border3);color:var(--text);}

/* ── GRID ── */
.bike-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(196px,1fr));
  gap:12px;
}

.bike-card{
  background:var(--bg2);
  border:1px solid var(--border);
  border-radius:var(--card-radius);
  overflow:hidden;
  transition:transform .2s,border-color .2s,box-shadow .2s;
  cursor:pointer;
  position:relative;
}
.bike-card:hover{
  border-color:var(--border2);
  transform:translateY(-4px);
  box-shadow:0 14px 44px rgba(0,0,0,.55);
}
.bike-card.feat{border-color:var(--red);}
.bike-card.feat::after{
  content:'★ FEATURED';
  position:absolute;top:8px;left:8px;
  font-size:7.5px;font-weight:900;letter-spacing:1.5px;
  background:var(--red);color:#fff;
  padding:2px 7px;border-radius:3px;z-index:2;
}
.bike-card.previewing{
  border-color:var(--red2);
  box-shadow:0 0 0 2px rgba(255,32,48,.22),0 14px 44px rgba(0,0,0,.55);
  transform:translateY(-4px);
}

.card-img{
  height:124px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
}
/* subtle bottom gradient on card image */
.card-img::after{
  content:'';
  position:absolute;bottom:0;left:0;right:0;height:36px;
  background:linear-gradient(transparent,rgba(0,0,0,.45));
}
.card-img img{
  max-width:90%;max-height:104px;object-fit:contain;
  filter:drop-shadow(0 4px 12px rgba(0,0,0,.55));
  transition:transform .2s;
}
.bike-card:hover .card-img img{transform:scale(1.05);}
.card-img .no-img{font-size:11px;color:var(--textdd);}
.card-country{
  position:absolute;top:7px;right:7px;z-index:2;
  font-size:8.5px;font-weight:700;letter-spacing:.8px;
  background:rgba(0,0,0,.62);color:var(--textd);
  border:1px solid var(--border2);border-radius:3px;
  padding:3px 7px;backdrop-filter:blur(4px);
  display:flex;align-items:center;gap:5px;
}
.card-country .cflag{
  width:18px;height:13px;border-radius:1px;overflow:hidden;
  display:inline-flex;align-items:center;flex-shrink:0;
}
.card-country .cflag img{width:100%;height:100%;object-fit:cover;display:block;}

.card-body{padding:10px 12px;}
.card-name{
  font-family:'Orbitron',monospace;font-size:10.5px;font-weight:700;
  margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.card-model{
  font-size:11px;color:var(--textd);margin-bottom:8px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.card-row{display:flex;align-items:center;justify-content:space-between;}
.card-price{font-family:'Orbitron',monospace;font-size:12.5px;color:var(--red2);}
.card-actions{display:flex;gap:6px;margin-top:9px;}
.card-btn{
  flex:1;text-align:center;padding:5px 0;border-radius:4px;
  font-size:10px;font-weight:700;letter-spacing:.4px;
  cursor:pointer;transition:all .15s;text-decoration:none;
  font-family:'Rajdhani',sans-serif;
}
.card-btn.edit{background:var(--bg3);border:1px solid var(--border2);color:var(--text);}
.card-btn.edit:hover{border-color:var(--red);color:var(--red3);}
.card-btn.del{background:none;border:1px solid var(--border);color:var(--textd);}
.card-btn.del:hover{border-color:#7f1d1d;color:#f87171;}

/* ── BADGES ── */
.badge{
  display:inline-block;font-size:9.5px;font-weight:700;
  letter-spacing:.4px;padding:2.5px 9px;border-radius:12px;
}
.badge-green{background:rgba(34,197,94,.1);color:#22c55e;border:1px solid rgba(34,197,94,.18);}
.badge-yellow{background:rgba(234,179,8,.1);color:#eab308;border:1px solid rgba(234,179,8,.18);}
.badge-red{background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.18);}

/* ── DELETE MODAL ── */
.modal-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.82);
  backdrop-filter:blur(5px);
  z-index:200;align-items:center;justify-content:center;
}
.modal-overlay.show{display:flex;}
.modal{
  background:var(--bg2);border:1px solid var(--border2);
  border-radius:var(--card-radius);
  padding:30px;max-width:380px;width:90%;
  text-align:center;position:relative;
}
.modal::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:var(--red);
  border-radius:var(--card-radius) var(--card-radius) 0 0;
}
.modal-icon{font-size:36px;margin-bottom:12px;}
.modal h3{font-family:'Orbitron',monospace;font-size:15px;font-weight:700;margin-bottom:8px;}
.modal p{font-size:13px;color:var(--textd);line-height:1.65;margin-bottom:22px;}
.modal-name{color:var(--red2);font-weight:700;}
.modal-btns{display:flex;gap:10px;justify-content:center;}
.btn-cancel{
  background:var(--bg3);border:1px solid var(--border2);
  color:var(--text);padding:9px 24px;border-radius:6px;
  font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:600;cursor:pointer;
}
.btn-cancel:hover{background:var(--bg4);}
.btn-confirm-del{
  background:var(--red);border:none;color:#fff;
  padding:9px 24px;border-radius:6px;
  font-family:'Orbitron',monospace;font-size:9.5px;font-weight:700;
  letter-spacing:1px;cursor:pointer;
}
.btn-confirm-del:hover{background:var(--red2);}

/* ── EMPTY STATE ── */
.empty{text-align:center;padding:60px 20px;color:var(--textd);}
.empty-icon{font-size:40px;margin-bottom:12px;opacity:.35;}
.empty h3{font-family:'Orbitron',monospace;font-size:13px;margin-bottom:8px;color:var(--textdd);}
.empty p{font-size:13px;}

/* ── TOAST ── */
.toast{
  position:fixed;bottom:24px;right:24px;
  background:var(--bg2);border:1px solid var(--border2);
  border-left:3px solid #22c55e;border-radius:7px;
  padding:11px 18px;font-size:13px;font-weight:600;
  z-index:300;transition:all .3s;
  display:flex;align-items:center;gap:8px;
}
.toast.hide{transform:translateY(16px);opacity:0;}
</style>
</head>
<body>

<!-- ── TOPBAR ────────────────────────────────────────────────── -->
<header class="topbar">
  <a href="index.php" class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 22 22"><polygon points="11,2 20,8 20,14 11,20 2,14 2,8"/><polyline points="11,2 11,20"/><line x1="2" y1="8" x2="20" y2="8"/><line x1="2" y1="14" x2="20" y2="14"/></svg>
    </div>
    <div class="logo-text">
      <span class="top">BIKE CONCEPT</span>
      <span class="bot">VAULT</span>
    </div>
  </a>

  <nav class="cat-nav">
    <a href="#" class="active" data-cat="0" onclick="setCat(0,this);return false;">ALL</a>
    <?php foreach ($categories as $cat): ?>
    <a href="#" data-cat="<?= $cat['category_id'] ?>"
       onclick="setCat(<?= $cat['category_id'] ?>,this);return false;">
      <span class="flag"><?= flagImg($cat['country_code'], $cat['country_code']) ?></span>
      <?= htmlspecialchars($cat['country_code']) ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <div class="topbar-right">
    <div class="search-box">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="17" y1="17" x2="22" y2="22"/></svg>
      <input type="text" id="liveSearch" placeholder="Search bikes..." autocomplete="off"
             oninput="applyFilters()">
      <button class="search-clear" id="searchClear"
              onclick="clearSearch()" title="Clear">&#10005;</button>
    </div>
    <a href="create.php" class="btn-add-new">
      <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      ADD BIKE
    </a>
  </div>
</header>

<!-- ── TOASTS ─────────────────────────────────────────────────── -->
<?php if (isset($_GET['deleted'])): ?>
<div class="toast" id="toast">&#10003; Bike deleted</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php elseif (isset($_GET['created'])): ?>
<div class="toast" id="toast">&#10003; Bike added</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php elseif (isset($_GET['updated'])): ?>
<div class="toast" id="toast">&#10003; Bike updated</div>
<script>setTimeout(()=>{document.getElementById('toast').classList.add('hide')},3200);</script>
<?php endif; ?>

<!-- ── HERO ───────────────────────────────────────────────────── -->
<?php if ($featured): ?>
<?php
  $heroImg  = resolveImg($featured['variant_img'] ?: $featured['image_url'], FALLBACK_BIKE_IMG);
  $bikeName = htmlspecialchars($featured['bike_name']);
  $wmRed    = mb_substr($featured['bike_name'], 0, 3);
  $wmWhite  = mb_substr($featured['bike_name'], 3);
  $sq       = $featured['stock_status'];
  $pillCls  = $sq === 'In Stock' ? 'green' : ($sq === 'Low Stock' ? 'yellow' : 'red-pill');
?>
<section class="hero">
  <div class="hero-top-line"></div>

  <!-- Stage (left) -->
  <div class="hero-left">
    <div class="hero-wm">
      <div class="hero-wm-brand">
        <span class="part-red"><?= htmlspecialchars($wmRed) ?></span><span class="part-white"><?= htmlspecialchars($wmWhite) ?></span>
      </div>
      <div class="hero-wm-sub">Scooter Edition</div>
    </div>

    <img class="hero-bike-img"
         id="heroBikeImg"
         src="<?= $heroImg ?>"
         alt="<?= $bikeName ?>"
         onerror="this.src='<?= FALLBACK_BIKE_IMG ?>'">

    <?php if ($variants): ?>
    <div class="variants-bar" id="variantsBar">
      <div class="var-choose" id="varChooseLabel">Choose your <?= $bikeName ?> :</div>
      <div class="variants-bottom" id="variantsRow">
        <?php foreach ($variants as $v): ?>
        <?php $vImg = resolveImg($v['image_url'] ?? null, ''); ?>
        <div class="var-item <?= $v['is_default'] ? 'active' : '' ?>"
             onclick="selectVariant(this,'<?= htmlspecialchars(addslashes($v['image_url'] ?? '')) ?>')">
          <div class="var-thumb">
            <?php if ($vImg): ?>
              <img src="<?= $vImg ?>" alt="<?= htmlspecialchars($v['color_name']) ?>" onerror="this.style.display='none'">
            <?php else: ?>
              <div class="var-swatch" style="background:#555"></div>
            <?php endif; ?>
          </div>
          <div class="var-name"><?= htmlspecialchars($v['color_name']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Info panel (right) -->
  <div class="hero-right">
    <div class="hr-eyebrow">Featured Model</div>

    <div class="hero-price" id="heroPrice">&#8369;<?= number_format($featured['price'], 0) ?></div>
    <div class="hero-model" id="heroModel"><?= $bikeName ?> <?= htmlspecialchars($featured['model']) ?></div>

    <div class="stock-pill <?= $pillCls ?>" id="heroPill">
      <span class="stock-dot"></span>
      <?= htmlspecialchars($sq) ?>
    </div>

    <div class="hero-desc" id="heroDesc"><?= nl2br(htmlspecialchars($featured['description'] ?? 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.')) ?></div>

    <!-- Mini stats -->
    <div class="hero-stats">
      <div class="hstat">
        <div class="hstat-lbl">Category</div>
        <div class="hstat-val" id="heroStatCat"><?= flagImg($featured['country_code'], $featured['country_code'], 'hstat-flag') ?> <?= htmlspecialchars($featured['country_code']) ?></div>
      </div>
      <div class="hstat">
        <div class="hstat-lbl">Color</div>
        <div class="hstat-val" id="heroStatColor"><?= htmlspecialchars($featured['color_name'] ?? '—') ?></div>
      </div>
      <div class="hstat">
        <div class="hstat-lbl">Stock</div>
        <div class="hstat-val" id="heroStatQty"><?= (int)$featured['stock_qty'] ?> units</div>
      </div>
      <div class="hstat">
        <div class="hstat-lbl">Status</div>
        <div class="hstat-val" style="color:var(--red2)">Active</div>
      </div>
    </div>

    <div class="hero-btns">
      <a href="edit.php?id=<?= $featured['bike_id'] ?>" class="btn-hero-edit" id="heroEditBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        EDIT
      </a>
      <a href="create.php" class="btn-hero-add">
        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        ADD NEW
      </a>
    </div>
  </div>

  <div class="sparkle">✦</div>
</section>
<?php endif; ?>

<!-- ── INVENTORY GRID ─────────────────────────────────────────── -->
<!-- BUILD PARTS SYSTEM -->
<section class="build-system" aria-label="Motorcycle modification build parts">
  <div class="build-shell">
    <div class="build-head">
      <div>
        <div class="build-kicker">Concept Build Parts</div>
        <div class="build-title" id="buildTitle">
          <?= $featured ? htmlspecialchars($featured['bike_name'] . ' ' . $featured['model']) : 'Selected Motorcycle' ?>
        </div>
        <div class="build-sub">Installed parts with editable prices, quantities, and estimated build cost.</div>
      </div>
      <div class="build-total">
        <span>Total Build Cost</span>
        <strong id="buildGrandTotal">&#8369;0</strong>
      </div>
    </div>

    <div class="build-tools">
      <div class="part-tabs" id="partTabs">
        <button class="part-tab active" type="button" data-part-filter="All" onclick="setPartFilter('All',this)">All</button>
        <button class="part-tab" type="button" data-part-filter="Wheels" onclick="setPartFilter('Wheels',this)">Wheels</button>
        <button class="part-tab" type="button" data-part-filter="Engine" onclick="setPartFilter('Engine',this)">Engine</button>
        <button class="part-tab" type="button" data-part-filter="Electrical" onclick="setPartFilter('Electrical',this)">Electrical</button>
        <button class="part-tab" type="button" data-part-filter="Body Parts" onclick="setPartFilter('Body Parts',this)">Body Parts</button>
        <button class="part-tab" type="button" data-part-filter="Accessories" onclick="setPartFilter('Accessories',this)">Accessories</button>
      </div>
      <button class="btn-add-part" type="button" onclick="togglePartForm()">ADD PART</button>
    </div>

    <div class="part-add-panel" id="partAddPanel">
      <input type="text" id="newPartName" placeholder="Part name">
      <input type="text" id="newPartBrand" placeholder="Brand">
      <select id="newPartCategory">
        <option>Engine</option>
        <option>Body</option>
        <option>Accessories</option>
        <option>Electrical</option>
      </select>
      <input type="number" id="newPartPrice" placeholder="Price" min="0" step="100">
      <input type="number" id="newPartQty" placeholder="Qty" min="1" step="1" value="1">
      <textarea id="newPartDesc" placeholder="Description"></textarea>
      <input type="file" id="newPartImage" accept="image/*" aria-label="Part image">
      <button type="button" onclick="addPart()">SAVE</button>
    </div>

    <div class="parts-list" id="partsList"></div>
  </div>
</section>

<div class="page-content">
  <div class="section-hdr">
    <div class="section-hdr-left">
      <h2 id="sectionLabel">Collection</h2>
      <span class="section-count" id="bikeCount"><?= count($bikes) ?> Bike<?= count($bikes) !== 1 ? 's' : '' ?></span>
    </div>
    <div class="right-side">
      <button class="filter-pill active" onclick="setStockFilter('all',this)">All</button>
      <button class="filter-pill" onclick="setStockFilter('in',this)">In Stock</button>
      <button class="filter-pill" onclick="setStockFilter('low',this)">Low Stock</button>
      <button class="filter-pill" onclick="setStockFilter('out',this)">Out of Stock</button>
    </div>
  </div>

  <?php if (empty($bikes)): ?>
  <div class="empty">
    <div class="empty-icon">&#128663;</div>
    <h3>No bikes found</h3>
    <p>Add your first bike to get started</p>
  </div>
  <?php else: ?>
  <div class="bike-grid" id="bikeGrid">
    <?php foreach ($bikes as $b): ?>
    <div class="bike-card <?= $b['is_featured'] ? 'feat' : '' ?>"
         data-search="<?= strtolower(htmlspecialchars($b['bike_name'].' '.$b['model'])) ?>"
         data-cat="<?= (int)$b['category_id'] ?>"
         data-bike-id="<?= $b['bike_id'] ?>"
         data-bike-name="<?= htmlspecialchars(addslashes($b['bike_name'])) ?>"
         data-model="<?= htmlspecialchars(addslashes($b['model'])) ?>"
         data-price="<?= $b['price'] ?>"
         data-stock="<?= htmlspecialchars($b['stock_status']) ?>"
         data-img="<?= resolveImg($b['variant_img'] ?: $b['image_url'], '') ?>"
         data-desc="<?= htmlspecialchars(addslashes($b['description'] ?? '')) ?>"
         onclick="previewBike(this)">
      <div class="card-img">
        <?php $cimg = resolveImg($b['variant_img'] ?: $b['image_url'], FALLBACK_BIKE_IMG); ?>
        <img src="<?= $cimg ?>" alt="<?= htmlspecialchars($b['bike_name']) ?>"
             onerror="this.src='<?= FALLBACK_BIKE_IMG ?>'">
        <span class="card-country"><span class="cflag"><?= flagImg($b['country_code'], $b['country_code']) ?></span><?= htmlspecialchars($b['country_code']) ?></span>
      </div>
      <div class="card-body">
        <div class="card-name"><?= htmlspecialchars($b['bike_name']) ?></div>
        <div class="card-model"><?= htmlspecialchars($b['model']) ?></div>
        <div class="card-row">
          <span class="card-price">&#8369;<?= number_format($b['price'], 0) ?></span>
          <?= stockBadge($b['stock_status']) ?>
        </div>
        <div class="card-actions">
          <a href="edit.php?id=<?= $b['bike_id'] ?>" class="card-btn edit" onclick="event.stopPropagation()">&#9998; EDIT</a>
          <button class="card-btn del" onclick="event.stopPropagation();openDelete(<?= $b['bike_id'] ?>, '<?= addslashes($b['bike_name']) ?> <?= addslashes($b['model']) ?>')">&#128465; DEL</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="empty" id="liveEmpty" style="display:none;">
    <div class="empty-icon">&#128663;</div>
    <h3>No bikes found</h3>
    <p id="liveEmptyMsg"></p>
  </div>
  <?php endif; ?>
</div>

<!-- ── DELETE MODAL ──────────────────────────────────────────── -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-icon">&#9888;</div>
    <h3>CONFIRM DELETE</h3>
    <p>Are you sure you want to delete<br><span class="modal-name" id="deleteName"></span>?<br><br>This will remove all variants and inventory records. This action cannot be undone.</p>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="closeDelete()">Cancel</button>
      <a href="#" id="deleteConfirmBtn" class="btn-confirm-del">DELETE</a>
    </div>
  </div>
</div>

<div class="modal-overlay" id="partImageModal">
  <div class="modal image-modal">
    <div class="image-modal-title" id="partImageTitle">Part Preview</div>
    <div class="image-modal-frame">
      <img src="" alt="Installed part preview" class="image-modal-img" id="partImagePreview">
    </div>
    <div class="modal-btns" style="margin-top:16px;">
      <button class="btn-cancel" onclick="closePartImage()">Close</button>
    </div>
  </div>
</div>

<script>
/* ── HERO DATA per category ── */
const FALLBACK_IMG = <?= json_encode(FALLBACK_BIKE_IMG) ?>;

const CAT_HEROES = {
  0: <?php
    if ($featured) {
      echo json_encode([
        'bike_id'      => $featured['bike_id'],
        'bike_name'    => $featured['bike_name'],
        'model'        => $featured['model'],
        'price'        => $featured['price'],
        'stock_status' => $featured['stock_status'],
        'img'          => $featured['variant_img'] ?: ($featured['image_url'] ?? ''),
        'description'  => $featured['description'] ?? '',
      ]);
    } else { echo 'null'; }
  ?>
  <?php foreach ($catHeroes as $catId => $hero): ?>,
  <?= (int)$catId ?>: <?= json_encode([
    'bike_id'      => $hero['bike_id'],
    'bike_name'    => $hero['bike_name'],
    'model'        => $hero['model'],
    'price'        => $hero['price'],
    'stock_status' => $hero['stock_status'],
    'img'          => $hero['variant_img'] ?: ($hero['image_url'] ?? ''),
    'description'  => $hero['description'] ?? '',
  ]) ?>
  <?php endforeach ?>
};

const CAT_LABELS = {
  0: 'INVENTORY'
  <?php foreach ($categories as $cat): ?>,
  <?= $cat['category_id'] ?>: 'INVENTORY — <?= addslashes($cat['country_code']) ?>'
  <?php endforeach; ?>
};

/* ── STATE ── */
const PARTS_FROM_DB = <?= json_encode($partsByBike) ?>;

let activeCat    = 0;
let searchTerm   = '';
let stockFilter  = 'all';

const allCards = Array.from(document.querySelectorAll('.bike-card'));
const countEl  = document.getElementById('bikeCount');
const labelEl  = document.getElementById('sectionLabel');
const gridEl   = document.getElementById('bikeGrid');
const emptyEl  = document.getElementById('liveEmpty');
const emptyMsg = document.getElementById('liveEmptyMsg');
const clearBtn = document.getElementById('searchClear');

/* ── HERO ELEMENTS ── */
const heroBikeImg    = document.getElementById('heroBikeImg');
const heroPrice      = document.getElementById('heroPrice');
const heroModel      = document.getElementById('heroModel');
const heroDesc       = document.getElementById('heroDesc');
const heroPill       = document.getElementById('heroPill');
const heroEditBtn    = document.getElementById('heroEditBtn');
const heroWmRed      = document.querySelector('.hero-wm-brand .part-red');
const heroWmWhite    = document.querySelector('.hero-wm-brand .part-white');
const varChooseLabel = document.getElementById('varChooseLabel');

/* BUILD PARTS SYSTEM */
const partsListEl = document.getElementById('partsList');
const buildTitleEl = document.getElementById('buildTitle');
const buildGrandTotalEl = document.getElementById('buildGrandTotal');
const partAddPanel = document.getElementById('partAddPanel');
const PARTS_STORAGE_KEY = 'bikeConceptVault.parts.v1';
const ALLOWED_PART_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
let activePartFilter = 'All';
let activeBuildKey = CAT_HEROES[0] ? String(CAT_HEROES[0].bike_id) : 'default';
let activeBuildName = CAT_HEROES[0] ? (CAT_HEROES[0].bike_name + ' ' + CAT_HEROES[0].model) : 'Selected Motorcycle';
let partIdSeq = 100;
const conceptPartsCache = {};
let pendingPartImage = '';
let editingPartId = null;
let editingDraft = null;

function formatPeso(value) {
  return '\u20B1' + Number(value || 0).toLocaleString();
}

function escapeHTML(value) {
  return String(value || '').replace(/[&<>"']/g, ch => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[ch]));
}

function loadStoredParts() {
  try {
    const stored = JSON.parse(localStorage.getItem(PARTS_STORAGE_KEY) || '{}');
    if (stored && typeof stored === 'object') {
      Object.assign(conceptPartsCache, stored);
      Object.values(conceptPartsCache).flat().forEach(part => {
        if (Number(part.id) > partIdSeq) partIdSeq = Number(part.id);
      });
    }
  } catch (error) {
    console.warn('Unable to load saved parts:', error);
  }
}

function savePartsState() {
  try {
    localStorage.setItem(PARTS_STORAGE_KEY, JSON.stringify(conceptPartsCache));
  } catch (error) {
    alert('Image is too large to save in browser storage. Please use a smaller JPG, PNG, or WEBP file.');
  }
}

function isAllowedPartImage(file) {
  return file && ALLOWED_PART_IMAGE_TYPES.includes(file.type);
}

function readPartImage(file, onLoad) {
  if (!isAllowedPartImage(file)) {
    alert('Please upload a JPG, PNG, or WEBP image.');
    return false;
  }
  const reader = new FileReader();
  reader.onload = event => onLoad(event.target.result);
  reader.readAsDataURL(file);
  return true;
}

function partImage(kind) {
  const svgMap = {
    wheel: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><circle cx="45" cy="35" r="25" fill="#141414" stroke="#e8000d" stroke-width="5"/><circle cx="45" cy="35" r="10" fill="#333" stroke="#ddd" stroke-width="3"/><g stroke="#eee" stroke-width="3" stroke-linecap="round"><path d="M45 10v50M20 35h50M27 17l36 36M63 17 27 53"/></g></svg>',
    shock: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><g transform="rotate(-22 45 35)"><rect x="40" y="8" width="10" height="54" rx="5" fill="#ddd"/><path d="M33 16h24M33 24h24M33 32h24M33 40h24M33 48h24M33 56h24" stroke="#e8000d" stroke-width="4" stroke-linecap="round"/></g></svg>',
    brake: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><circle cx="38" cy="36" r="22" fill="none" stroke="#ccc" stroke-width="7"/><circle cx="38" cy="36" r="6" fill="#e8000d"/><path d="M53 22h18v27H53c7-8 7-19 0-27z" fill="#2b2b2b" stroke="#e8000d" stroke-width="3"/></svg>',
    seat: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><path d="M16 41c8-18 25-23 47-18 8 2 12 8 11 16-18 5-38 7-58 2z" fill="#1d1d1d" stroke="#e8000d" stroke-width="4"/><path d="M26 35c14 3 29 3 43 0" stroke="#555" stroke-width="3" stroke-linecap="round"/></svg>',
    light: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><path d="M24 22h31c9 0 16 7 16 16s-7 16-16 16H24z" fill="#151515" stroke="#e8000d" stroke-width="4"/><path d="M33 29h20c5 0 9 4 9 9s-4 9-9 9H33z" fill="#f6f0b5"/><path d="M14 28 4 20M13 42 3 50" stroke="#f6f0b5" stroke-width="4" stroke-linecap="round"/></svg>',
    engine: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><rect x="20" y="24" width="42" height="28" rx="5" fill="#202020" stroke="#e8000d" stroke-width="4"/><path d="M29 18h24v10H29zM62 32h10v13H62M28 52l-8 8M54 52l8 8" fill="none" stroke="#ddd" stroke-width="4" stroke-linecap="round"/></svg>',
    default: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 70"><path d="M20 48h38l12-18H47l-8-10H25z" fill="#181818" stroke="#e8000d" stroke-width="4"/><circle cx="27" cy="51" r="7" fill="#ddd"/><circle cx="61" cy="51" r="7" fill="#ddd"/></svg>'
  };
  return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svgMap[kind] || svgMap.default);
}

function imageKindForPart(part) {
  const text = (part.name + ' ' + part.category).toLowerCase();
  if (text.includes('wheel') || text.includes('mags')) return 'wheel';
  if (text.includes('shock')) return 'shock';
  if (text.includes('brake')) return 'brake';
  if (text.includes('seat')) return 'seat';
  if (text.includes('headlight') || text.includes('led')) return 'light';
  if (text.includes('engine')) return 'engine';
  return 'default';
}

function basePartsForBuild(buildName) {
  const concept = (buildName || '').toLowerCase();
  const wheelBrand = concept.includes('thai') ? 'RCB Thailand' : 'Concept Garage';
  return [
    {id:1, name:'Thai Mags Wheel', brand:wheelBrand, category:'Wheels', price:4500, qty:1, description:'Lightweight Thai-style mags wheel set for daily concept builds.'},
    {id:2, name:'Racing Shock', brand:'YSS Performance', category:'Engine', price:2800, qty:1, description:'Adjustable rear shock tuned for a lower, sportier stance.'},
    {id:3, name:'Disc Brake Set', brand:'Brembo Style', category:'Engine', price:3200, qty:1, description:'Front disc brake conversion kit with caliper, rotor, and bracket.'},
    {id:4, name:'Thai Seat', brand:'Bangkok Trim', category:'Body Parts', price:1500, qty:1, description:'Slim custom seat with Thai concept profile.'},
    {id:5, name:'LED Headlight', brand:'Koso Vision', category:'Electrical', price:1200, qty:1, description:'Bright LED headlight assembly for night riding and show display.'}
  ];
}

function getActiveParts() {
  if (!conceptPartsCache[activeBuildKey]) {
    const dbParts = PARTS_FROM_DB[activeBuildKey] || [];
    conceptPartsCache[activeBuildKey] = dbParts.length
      ? dbParts.map(part => ({...part}))
      : basePartsForBuild(activeBuildName).map(part => ({...part}));
    savePartsState();
  }
  return conceptPartsCache[activeBuildKey];
}

loadStoredParts();

const newPartImageInput = document.getElementById('newPartImage');
if (newPartImageInput) {
  newPartImageInput.addEventListener('change', function() {
    const file = this.files && this.files[0];
    if (!file) {
      pendingPartImage = '';
      return;
    }
    readPartImage(file, imageData => {
      pendingPartImage = imageData;
    });
  });
}

function renderParts() {
  if (!partsListEl) return;
  const parts = getActiveParts();
  const shown = parts.filter(part => activePartFilter === 'All' || part.category === activePartFilter);
  if (buildTitleEl) buildTitleEl.textContent = activeBuildName;
  if (!shown.length) {
    partsListEl.innerHTML = '<div class="parts-empty">No installed parts in this category.</div>';
  } else {
    partsListEl.innerHTML = shown.map(part => {
      const isEditing = editingPartId === part.id;
      const rowData = isEditing && editingDraft ? editingDraft : part;
      const lineTotal = Number(rowData.price || 0) * Number(rowData.qty || 0);
      const img = rowData.image || partImage(imageKindForPart(rowData));
      const categories = ['Wheels','Engine','Electrical','Body Parts','Accessories'];
      return `
        <div class="part-row" data-part-id="${part.id}">
          <div class="part-image-tools">
            <button class="part-thumb" type="button" onclick="openPartImage(${part.id})" title="View ${escapeHTML(part.name)} image">
              <img src="${img}" alt="${escapeHTML(rowData.name)} preview">
            </button>
            <label class="part-image-btn" for="partImageInput${part.id}">${part.image ? 'CHANGE' : 'UPLOAD'}</label>
            <input class="part-image-file" id="partImageInput${part.id}" type="file" accept="image/jpeg,image/png,image/webp" onchange="savePartImage(${part.id}, this)">
            ${part.image ? `<button class="part-image-remove" type="button" onclick="removePartImage(${part.id})">REMOVE</button>` : ''}
          </div>
          <div>
            ${isEditing ? `
              <input class="part-name-input" type="text" value="${escapeHTML(rowData.name)}" oninput="updateDraftField('name', this.value)" aria-label="Part name">
              <input class="part-brand-input" type="text" value="${escapeHTML(rowData.brand)}" oninput="updateDraftField('brand', this.value)" aria-label="Part brand">
              <textarea class="part-desc-input" oninput="updateDraftField('description', this.value)" aria-label="Part description">${escapeHTML(rowData.description || '')}</textarea>
            ` : `
              <div class="part-name-display">${escapeHTML(rowData.name)}</div>
              <div class="part-brand-display">${escapeHTML(rowData.brand)}</div>
              <div class="part-desc-display">${escapeHTML(rowData.description || 'No description added.')}</div>
            `}
          </div>
          ${isEditing ? `
            <select class="part-cat-select" onchange="updateDraftField('category', this.value)" aria-label="Part category">
              ${categories.map(cat => `<option value="${cat}" ${rowData.category === cat ? 'selected' : ''}>${cat}</option>`).join('')}
            </select>
            <input class="part-price" type="number" min="0" step="100" value="${Number(rowData.price || 0)}" oninput="updateDraftField('price', this.value)" aria-label="${escapeHTML(rowData.name)} price">
            <input class="part-qty" type="number" min="1" step="1" value="${Number(rowData.qty || 1)}" oninput="updateDraftField('qty', this.value)" aria-label="${escapeHTML(rowData.name)} quantity">
          ` : `
            <span class="part-cat">${escapeHTML(rowData.category)}</span>
            <div class="part-meta-display">${formatPeso(rowData.price)}</div>
            <div class="part-meta-display">${Number(rowData.qty || 1)}</div>
          `}
          <div class="part-line-total">${formatPeso(lineTotal)}</div>
          <div class="part-actions">
            ${isEditing ? `
              <button class="part-save" type="button" onclick="savePartEdit(${part.id})">SAVE</button>
              <button class="part-cancel" type="button" onclick="cancelPartEdit()">CANCEL</button>
            ` : `
              <button class="part-edit" type="button" onclick="startPartEdit(${part.id})">EDIT</button>
              <button class="part-remove" type="button" onclick="removePart(${part.id})">REMOVE</button>
            `}
          </div>
        </div>
      `;
    }).join('');
  }
  updateBuildTotal();
}

function updateBuildTotal() {
  const total = getActiveParts().reduce((sum, part) => sum + (Number(part.price || 0) * Number(part.qty || 0)), 0);
  if (buildGrandTotalEl) buildGrandTotalEl.textContent = formatPeso(total);
}

function startPartEdit(id) {
  const part = getActiveParts().find(item => item.id === id);
  if (!part) return;
  editingPartId = id;
  editingDraft = {...part};
  renderParts();
  setTimeout(() => {
    const input = document.querySelector(`.part-row[data-part-id="${id}"] .part-name-input`);
    if (input) {
      input.focus();
      input.select();
    }
  }, 40);
}

function updateDraftField(field, value) {
  if (!editingDraft) return;
  if (field === 'price') {
    editingDraft.price = Math.max(0, Number(value || 0));
  } else if (field === 'qty') {
    editingDraft.qty = Math.max(1, parseInt(value || 1, 10));
  } else {
    editingDraft[field] = value;
  }
  const row = document.querySelector(`.part-row[data-part-id="${editingPartId}"]`);
  const totalEl = row ? row.querySelector('.part-line-total') : null;
  if (totalEl) totalEl.textContent = formatPeso(Number(editingDraft.price || 0) * Number(editingDraft.qty || 0));
}

function savePartImage(id, input) {
  const part = getActiveParts().find(item => item.id === id);
  const file = input.files && input.files[0];
  if (!part || !file) return;
  readPartImage(file, imageData => {
    part.image = imageData;
    if (editingPartId === id && editingDraft) editingDraft.image = imageData;
    const row = document.querySelector(`.part-row[data-part-id="${id}"]`);
    const img = row ? row.querySelector('.part-thumb img') : null;
    if (img) img.src = imageData;
    savePartsState();
    renderParts();
  });
}

function removePartImage(id) {
  const part = getActiveParts().find(item => item.id === id);
  if (!part) return;
  delete part.image;
  if (editingPartId === id && editingDraft) delete editingDraft.image;
  savePartsState();
  renderParts();
}

function savePartEdit(id) {
  const parts = getActiveParts();
  const index = parts.findIndex(item => item.id === id);
  if (index === -1 || !editingDraft) return;
  parts[index] = {
    ...editingDraft,
    name: String(editingDraft.name || '').trim() || 'Untitled Part',
    brand: String(editingDraft.brand || '').trim() || 'Custom Garage',
    description: String(editingDraft.description || '').trim(),
    price: Math.max(0, Number(editingDraft.price || 0)),
    qty: Math.max(1, parseInt(editingDraft.qty || 1, 10))
  };
  editingPartId = null;
  editingDraft = null;
  savePartsState();
  renderParts();
}

function cancelPartEdit() {
  editingPartId = null;
  editingDraft = null;
  renderParts();
}

function removePart(id) {
  if (editingPartId === id) {
    editingPartId = null;
    editingDraft = null;
  }
  conceptPartsCache[activeBuildKey] = getActiveParts().filter(part => part.id !== id);
  savePartsState();
  renderParts();
}

function openPartImage(id) {
  const part = getActiveParts().find(item => item.id === id);
  if (!part) return;
  const img = part.image || partImage(imageKindForPart(part));
  const modal = document.getElementById('partImageModal');
  const preview = document.getElementById('partImagePreview');
  const title = document.getElementById('partImageTitle');
  if (preview) preview.src = img;
  if (title) title.textContent = part.name + ' - ' + part.brand;
  if (modal) modal.classList.add('show');
}

function closePartImage() {
  const modal = document.getElementById('partImageModal');
  if (modal) modal.classList.remove('show');
}

function togglePartForm() {
  if (partAddPanel) partAddPanel.classList.toggle('show');
}

function addPart() {
  const name = document.getElementById('newPartName').value.trim();
  const brand = document.getElementById('newPartBrand').value.trim() || 'Custom Garage';
  const category = document.getElementById('newPartCategory').value;
  const price = Math.max(0, Number(document.getElementById('newPartPrice').value || 0));
  const qty = Math.max(1, parseInt(document.getElementById('newPartQty').value || 1, 10));
  const description = document.getElementById('newPartDesc').value.trim();
  if (!name) {
    document.getElementById('newPartName').focus();
    return;
  }
  getActiveParts().push({id:++partIdSeq, name, brand, category, price, qty, description, image: pendingPartImage});
  document.getElementById('newPartName').value = '';
  document.getElementById('newPartBrand').value = '';
  document.getElementById('newPartPrice').value = '';
  document.getElementById('newPartQty').value = '1';
  document.getElementById('newPartDesc').value = '';
  document.getElementById('newPartImage').value = '';
  pendingPartImage = '';
  savePartsState();
  renderParts();
}

function setPartFilter(category, btn) {
  activePartFilter = category;
  document.querySelectorAll('.part-tab').forEach(tab => tab.classList.remove('active'));
  if (btn) btn.classList.add('active');
  renderParts();
}

function updateBuildConcept(data) {
  if (!data) return;
  activeBuildKey = String(data.bike_id || data.bikeId || 'default');
  activeBuildName = (data.bike_name || data.bikeName || 'Selected') + ' ' + (data.model || 'Motorcycle');
  editingPartId = null;
  editingDraft = null;
  renderParts();
}

/* ── UPDATE HERO ── */
function updateHero(catId) {
  const data = CAT_HEROES[catId] || CAT_HEROES[0];
  if (!data) return;
  const img = (data.img && data.img.trim()) ? data.img : FALLBACK_IMG;
  if (heroBikeImg) {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = img;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 180);
  }
  if (heroPrice)  heroPrice.textContent  = '₱' + Number(data.price).toLocaleString();
  if (heroModel)  heroModel.textContent  = data.bike_name + ' ' + data.model;
  if (heroDesc)   heroDesc.textContent   = data.description || 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.';
  if (heroEditBtn) heroEditBtn.href      = 'edit.php?id=' + data.bike_id;
  if (varChooseLabel) varChooseLabel.textContent = 'Choose your ' + data.bike_name + ' :';
  if (heroWmRed)   heroWmRed.textContent   = data.bike_name.substring(0, 3);
  if (heroWmWhite) heroWmWhite.textContent = data.bike_name.substring(3);
  if (heroPill) {
    const s = data.stock_status;
    heroPill.className = 'stock-pill ' + (s === 'In Stock' ? 'green' : s === 'Low Stock' ? 'yellow' : 'red-pill');
    heroPill.innerHTML = '<span class="stock-dot"></span>' + s;
  }
  updateBuildConcept(data);
}

/* ── CATEGORY CLICK ── */
function setCat(catId, linkEl) {
  activeCat = catId;
  document.querySelectorAll('.cat-nav a[data-cat]').forEach(a => {
    a.classList.toggle('active', parseInt(a.dataset.cat) === catId);
  });
  if (labelEl) labelEl.textContent = CAT_LABELS[catId] || 'INVENTORY';
  updateHero(catId);
  applyFilters();
}

/* ── STOCK FILTER ── */
function setStockFilter(val, btn) {
  stockFilter = val;
  document.querySelectorAll('.filter-pill').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  applyFilters();
}

/* ── SEARCH INPUT ── */
document.getElementById('liveSearch').addEventListener('input', function() {
  searchTerm = this.value.trim().toLowerCase();
  clearBtn.classList.toggle('visible', searchTerm.length > 0);
  applyFilters();
});

function clearSearch() {
  const input = document.getElementById('liveSearch');
  input.value = ''; searchTerm = '';
  clearBtn.classList.remove('visible');
  applyFilters(); input.focus();
}

/* ── COMBINED FILTER ── */
function applyFilters() {
  let visible = 0;
  allCards.forEach(card => {
    const catMatch    = activeCat === 0 || parseInt(card.dataset.cat) === activeCat;
    const searchMatch = searchTerm === '' || (card.dataset.search || '').includes(searchTerm);
    const s = (card.dataset.stock || '').toLowerCase();
    const stockMatch  = stockFilter === 'all'
                     || (stockFilter === 'in'  && s === 'in stock')
                     || (stockFilter === 'low' && s === 'low stock')
                     || (stockFilter === 'out' && s === 'out of stock');
    const show = catMatch && searchMatch && stockMatch;
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  if (countEl) countEl.textContent = visible + ' bike' + (visible !== 1 ? 's' : '');
  if (gridEl)  gridEl.style.display  = visible === 0 ? 'none' : '';
  if (emptyEl) {
    emptyEl.style.display = visible === 0 ? 'block' : 'none';
    if (emptyMsg) {
      if (searchTerm)       emptyMsg.textContent = 'No results for "' + document.getElementById('liveSearch').value.trim() + '"';
      else if (activeCat)   emptyMsg.textContent = 'No bikes in this category yet.';
      else                  emptyMsg.textContent = 'Add your first bike to get started.';
    }
  }
}

/* ── DELETE MODAL ── */
function openDelete(id, name) {
  document.getElementById('deleteName').textContent = name;
  document.getElementById('deleteConfirmBtn').href = 'delete.php?id=' + id;
  document.getElementById('deleteModal').classList.add('show');
}
function closeDelete() {
  document.getElementById('deleteModal').classList.remove('show');
}
document.getElementById('deleteModal').addEventListener('click', function(e){ if(e.target===this) closeDelete(); });
document.getElementById('partImageModal').addEventListener('click', function(e){ if(e.target===this) closePartImage(); });

/* ── CARD PREVIEW → HERO ── */
function previewBike(card) {
  const d = card.dataset;
  const img = (d.img && d.img.trim()) ? d.img : FALLBACK_IMG;
  document.querySelectorAll('.bike-card').forEach(c => c.classList.remove('previewing'));
  card.classList.add('previewing');
  if (heroBikeImg) {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = img;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 150);
  }
  if (heroPrice)   heroPrice.textContent  = '\u20B1' + Number(d.price).toLocaleString();
  if (heroModel)   heroModel.textContent  = d.bikeName + ' ' + d.model;
  if (heroDesc)    heroDesc.textContent   = d.desc || 'Inspired by iconic design built for performance. The all-new model delivers style, power, and everyday comfort.';
  if (heroEditBtn) heroEditBtn.href       = 'edit.php?id=' + d.bikeId;
  if (varChooseLabel) varChooseLabel.textContent = 'Viewing: ' + d.bikeName;
  if (heroWmRed)   heroWmRed.textContent   = d.bikeName.substring(0, 3);
  if (heroWmWhite) heroWmWhite.textContent = d.bikeName.substring(3);
  if (heroPill) {
    const s = d.stock;
    heroPill.className = 'stock-pill ' + (s === 'In Stock' ? 'green' : s === 'Low Stock' ? 'yellow' : 'red-pill');
    heroPill.innerHTML = '<span class=\'stock-dot\'></span>' + s;
  }
  updateBuildConcept(d);
  document.querySelector('.hero').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ── VARIANT SWITCHER ── */
function selectVariant(el, imgSrc) {
  const row = el.closest('.variants-bottom');
  if (row) row.querySelectorAll('.var-item').forEach(v => v.classList.remove('active'));
  el.classList.add('active');
  if (heroBikeImg && imgSrc && imgSrc.trim() !== '') {
    heroBikeImg.style.opacity = '0';
    setTimeout(() => {
      heroBikeImg.src = imgSrc;
      heroBikeImg.onerror = function(){ this.src = FALLBACK_IMG; };
      heroBikeImg.style.opacity = '1';
    }, 150);
  }
}
renderParts();
</script>
</body>
</html>
