<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

class Profess0rPayUpdater {
    private $githubRepo = 'profess0r-null/profess0rpay';
    private $basePath;
    private $tmpPath;
    private $ignoreList = [
        'pp-config.php',
        '.env',
        'pp-media/',
        'pp-content/tmp/',
        'pp-content/backups/',
        'update.lock',
        'update_debug.txt'
    ];

    public function __construct() {
        $this->basePath = realpath(__DIR__ . '/../../');
        $this->tmpPath = $this->basePath . '/pp-content/tmp';

        if (!is_dir($this->tmpPath)) mkdir($this->tmpPath, 0755, true);
    }

    public function checkPreflight() {
        $errors = [];
        if (version_compare(PHP_VERSION, '8.0.0', '<')) $errors[] = "PHP 8.0+ is required.";
        if (!class_exists('ZipArchive')) $errors[] = "ZipArchive PHP extension is missing.";
        if (!function_exists('curl_init')) $errors[] = "cURL PHP extension is missing.";
        if (!is_writable($this->basePath)) $errors[] = "Root directory is not writable.";
        if (!is_writable($this->tmpPath)) $errors[] = "pp-content/tmp/ directory is not writable.";
        return $errors;
    }

    public function lock() {
        if (file_exists($this->basePath . '/update.lock')) {
            throw new \Exception('An update is already in progress.');
        }
        file_put_contents($this->basePath . '/update.lock', time());
    }

    public function unlock() {
        if (file_exists($this->basePath . '/update.lock')) {
            @unlink($this->basePath . '/update.lock');
        }
    }

    public function getLatestRelease() {
        $url = "https://api.github.com/repos/{$this->githubRepo}/releases/latest";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Profess0rPay-Updater');
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    // Step 1: Download
    public function stepDownload($url) {
        $tmpFile = $this->tmpPath . '/update.zip';
        $fp = fopen($tmpFile, 'w+');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Profess0rPay-Updater');
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if ($httpCode >= 400) {
            @unlink($tmpFile);
            throw new \Exception("Failed to download update file. HTTP Code: $httpCode");
        }
        return true;
    }

    // Step 2: Extract
    public function stepExtract() {
        $zipFile = $this->tmpPath . '/update.zip';
        if (!file_exists($zipFile)) {
            throw new \Exception("Update zip file not found.");
        }

        $extractPath = $this->tmpPath . '/extract';
        if (is_dir($extractPath)) $this->deleteDir($extractPath);
        mkdir($extractPath, 0755, true);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new \Exception("Failed to extract update zip.");
        }
        return true;
    }

    // Step 3: Install (Copy files)
    public function stepInstall() {
        $extractPath = $this->tmpPath . '/extract';
        if (!is_dir($extractPath)) {
            throw new \Exception("Extraction folder not found.");
        }

        $files = array_diff(scandir($extractPath), array('.','..'));
        $sourceDir = $extractPath;
        if (count($files) === 1) {
            $firstItem = reset($files);
            if (is_dir($extractPath . '/' . $firstItem)) {
                $sourceDir = $extractPath . '/' . $firstItem;
            }
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $subPath = $iterator->getSubPathName();
            $skip = false;
            foreach ($this->ignoreList as $ignore) {
                if (str_starts_with(str_replace('\\', '/', $subPath), trim($ignore, '/'))) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $destPath = $this->basePath . '/' . $subPath;
            if ($item->isDir()) {
                if (!is_dir($destPath)) mkdir($destPath, 0755, true);
            } else {
                copy($item, $destPath);
            }
        }
        return true;
    }

    // Step 4: Cleanup
    public function stepCleanup() {
        $extractPath = $this->tmpPath . '/extract';
        $zipFile = $this->tmpPath . '/update.zip';

        if (is_dir($extractPath)) $this->deleteDir($extractPath);
        if (file_exists($zipFile)) @unlink($zipFile);
        $this->unlock();
        return true;
    }

    private function deleteDir($dirPath) {
        if (!is_dir($dirPath)) return;
        $files = array_diff(scandir($dirPath), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dirPath/$file")) ? $this->deleteDir("$dirPath/$file") : @unlink("$dirPath/$file");
        }
        @rmdir($dirPath);
    }
}
