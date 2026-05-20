<?php
// ============================================================
//  BIKE CONCEPT VAULT — Database Connection (db.php)
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bike_concept_vault');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// ── Helper: run CREATE TABLE if not exists ──────────────────
function initDatabase(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            category_id   INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(50)  NOT NULL,
            country_code  VARCHAR(10)  NOT NULL,
            flag_emoji    VARCHAR(10),
            description   TEXT,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS bikes (
            bike_id     INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            bike_name   VARCHAR(100) NOT NULL,
            model       VARCHAR(100) NOT NULL,
            edition     VARCHAR(100),
            price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            description TEXT,
            image_url   VARCHAR(255),
            is_featured TINYINT(1) DEFAULT 0,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(category_id)
                ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS bike_variants (
            variant_id  INT AUTO_INCREMENT PRIMARY KEY,
            bike_id     INT NOT NULL,
            color_name  VARCHAR(50)  NOT NULL,
            color_hex   VARCHAR(7),
            image_url   VARCHAR(255),
            is_default  TINYINT(1) DEFAULT 0,
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bike_id) REFERENCES bikes(bike_id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS inventory (
            inventory_id INT AUTO_INCREMENT PRIMARY KEY,
            variant_id   INT NOT NULL,
            stock_qty    INT NOT NULL DEFAULT 0,
            stock_status ENUM('In Stock','Low Stock','Out of Stock') NOT NULL DEFAULT 'In Stock',
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (variant_id) REFERENCES bike_variants(variant_id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS bike_parts (
            part_id     INT AUTO_INCREMENT PRIMARY KEY,
            bike_id     INT NOT NULL,
            part_type   ENUM('Accessory','Part') NOT NULL DEFAULT 'Accessory',
            category    VARCHAR(40) NOT NULL DEFAULT 'Accessories',
            part_name   VARCHAR(120) NOT NULL,
            brand       VARCHAR(100),
            description TEXT,
            price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            quantity    INT NOT NULL DEFAULT 1,
            image_url   VARCHAR(255),
            created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (bike_id) REFERENCES bikes(bike_id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Keep older local databases compatible with the current app schema.
    $variantColumns = $pdo->query("SHOW COLUMNS FROM bike_variants")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('color_hex', $variantColumns, true)) {
        $pdo->exec("ALTER TABLE bike_variants ADD color_hex VARCHAR(7) NULL AFTER color_name");
    }

    // Seed categories if empty
    $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO categories (category_name, country_code, flag_emoji, description) VALUES
            ('Indonesian', 'INDO',  '🇮🇩', 'Motorcycles popular in the Indonesian market'),
            ('Malaysian',  'MALAY', '🇲🇾', 'Motorcycles popular in the Malaysian market'),
            ('Thai',       'THAI',  '🇹🇭', 'Motorcycles popular in the Thai market'),
            ('Filipino',   'PINOY', '🇵🇭', 'Motorcycles popular in the Philippine market'),
            ('Vans',       'VANS',  '',    'Special edition Vans collaboration bikes');
        ");
    }
}

initDatabase($pdo);
