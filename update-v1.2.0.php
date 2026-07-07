<?php
// Profess0rPay Manual Database Updater to v1.2.0
if (!file_exists(__DIR__ . '/pp-config.php')) {
    exit('Configuration file missing. Please install the script first.');
}

require __DIR__ . '/pp-config.php';

try {
    $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Profess0rPay v1.2.0 Database Update</h2>";
    echo "<ul>";

    // 1. Add favicon to brands
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$db_prefix}brands` LIKE 'favicon'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `{$db_prefix}brands` ADD COLUMN `favicon` VARCHAR(255) DEFAULT '--' AFTER `logo`");
        echo "<li>Added 'favicon' column to brands table.</li>";
    }

    // 2. Insert payment-link-default-logo
    $stmt = $pdo->query("SELECT * FROM `{$db_prefix}env` WHERE option_name = 'payment-link-default-logo'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO `{$db_prefix}env` (`brand_id`, `option_name`, `value`, `created_date`) VALUES ('both', 'payment-link-default-logo', '--', NOW())");
        echo "<li>Inserted 'payment-link-default-logo' setting.</li>";
    }

    // 3. Insert admin-path
    $stmt = $pdo->query("SELECT * FROM `{$db_prefix}env` WHERE option_name = 'admin-path'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO `{$db_prefix}env` (`brand_id`, `option_name`, `value`, `created_date`) VALUES ('both', 'admin-path', 'admin', NOW())");
        echo "<li>Inserted 'admin-path' setting.</li>";
    }
    
    // 4. Create update_logs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `{$db_prefix}update_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `version` VARCHAR(50) NOT NULL,
        `status` VARCHAR(50) NOT NULL,
        `log` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<li>Created 'update_logs' table.</li>";

    // 5. Create migrations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `{$db_prefix}migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL,
        `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<li>Created 'migrations' table.</li>";

    // 6. Set version
    $stmt = $pdo->query("SELECT * FROM `{$db_prefix}env` WHERE option_name = 'pp_version'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO `{$db_prefix}env` (`brand_id`, `option_name`, `value`, `created_date`) VALUES ('both', 'pp_version', '1.2.0', NOW())");
    } else {
        $pdo->exec("UPDATE `{$db_prefix}env` SET `value` = '1.2.0' WHERE option_name = 'pp_version'");
    }
    echo "<li>Updated system version to v1.2.0.</li>";

    echo "</ul>";
    echo "<h3 style='color: green;'>Update Successful! Your database is now compatible with v1.2.0.</h3>";
    echo "<p style='color: red; font-weight: bold;'>Security Warning: Please delete this file (update-v1.2.0.php) from your server immediately!</p>";
    echo "<a href='/'>Go to Homepage</a>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
?>
