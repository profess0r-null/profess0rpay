<?php
$files = scandir(__DIR__);
$deleted = 0;
echo "<h3>Cleanup Started...</h3>";

foreach($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    // Check if it's a file (not a folder) AND has a backslash (\) in the name
    if (!is_dir($file) && strpos($file, '\\') !== false) {
        if (unlink($file)) {
            echo "Deleted: " . htmlspecialchars($file) . "<br>";
            $deleted++;
        } else {
            echo "<span style='color:red'>Failed to delete: " . htmlspecialchars($file) . "</span><br>";
        }
    }
}

echo "<h3><b>Total deleted: $deleted files.</b></h3>";
echo "<b>Cleanup complete! You can now delete this cleanup.php file from your cPanel.</b>";
?>
