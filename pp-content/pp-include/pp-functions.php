<?php
    declare(strict_types=1);

    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (date_default_timezone_get() !== 'UTC') {
        date_default_timezone_set('UTC');
    }

    $pp_functions_loaded = true;
    
    function pp_site_url($type = "Full") {
        // Detect protocol — also handles reverse proxies (cPanel, Cloudflare, load balancers)
        // that don't set $_SERVER['HTTPS'] but use X-Forwarded-Proto or CF-Visitor instead
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || ($_SERVER['SERVER_PORT'] ?? 80) == 443
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
                || (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], '"https"') !== false)
                || (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) === 'on');

        $protocol = $isHttps ? "https://" : "http://";

        // Full host with subdomain
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Request URI (path after domain)
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Extract main domain
        $hostParts = explode('.', $host);
        $numParts = count($hostParts);

        if ($numParts >= 2) {
            // Handles domains like example.com or sub.example.com
            $mainDomain = $hostParts[$numParts - 2] . '.' . $hostParts[$numParts - 1];
        } else {
            $mainDomain = $host; // fallback
        }

        switch (strtolower($type)) {
            case "fulldomain":
                return $protocol.$host; // subdomain + main domain
            case "maindomain":
                return $mainDomain; // main domain only
            case "full":
            default:
                return $protocol . $host . $requestUri; // full URL
        }
    }

    function getAdminPath($url) {
        // Remove query string
        $url = explode('?', $url)[0];

        // Find position of admin/
        $pos = strpos($url, 'admin/');
        if ($pos === false) return ''; // admin/ not found

        // Get everything after admin/
        $path = substr($url, $pos + strlen('admin/'));

        // Remove leading/trailing slashes
        $path = trim($path, '/');

        return $path;
    }

    function getAuthorizationHeader() {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $val) {
                if (strtolower($key) === 'authorization') {
                    if (stripos(trim($val), 'Bearer ') === 0) {
                        return substr(trim($val), 7);
                    }
                }
                if (strtolower($key) === 'mhs-profess0rpay-api-key') {
                    return trim($val);
                }
                if (strtolower($key) === 'mhs-piprapay-api-key') {
                    return trim($val);
                }
            }
        }
    
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = trim($_SERVER['HTTP_AUTHORIZATION']);
            if (stripos($auth, 'Bearer ') === 0) {
                return substr($auth, 7);
            }
        }

        foreach ($_SERVER as $key => $value) {
            if (stripos($key, 'HTTP_MHS_PROFESS0RPAY_API_KEY') !== false || stripos($key, 'HTTP_MHS_PIPRAPAY_API_KEY') !== false) {
                return trim($value);
            }
        }
    
        return null;
    }

    function connectDatabase() {
        global $db_host, $db_port, $db_user, $db_pass, $db_name;
        $db_port = $db_port ?? 3306; // fallback

    try {
            // Build DSN
            $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

            // Create PDO instance
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Throw exceptions on error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch associative arrays
                PDO::ATTR_EMULATE_PREPARES => false               // Use native prepared statements
            ]);

            return $pdo;
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    function timeAgo($datetime) {
        global $global_response_brand;

        // Determine user timezone or default to Dhaka
        $userTimezone = (!empty($global_response_brand['response'][0]['timezone']) && $global_response_brand['response'][0]['timezone'] !== '--')
            ? $global_response_brand['response'][0]['timezone']
            : 'Asia/Dhaka';

        // Create DateTime objects in the user's timezone
        $tz = new DateTimeZone($userTimezone);

        // Convert the input datetime (assumed UTC) to user's timezone
        $past = new DateTime($datetime, new DateTimeZone('UTC'));
        $past->setTimezone($tz);

        // Get current time in user's timezone
        $now = new DateTime('now', $tz);

        // Calculate difference
        $diff = $now->diff($past);

        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        } elseif ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        } elseif ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    function getCurrentDatetime($format = 'Y-m-d H:i:s') {
        $currentDatetime = new DateTime();

        return $currentDatetime->format($format);
    }   

    function getUserDeviceInfo() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
        if (preg_match('/mobile/i', $userAgent)) {
            $deviceType = "Mobile";
        } elseif (preg_match('/tablet/i', $userAgent)) {
            $deviceType = "Tablet";
        } else {
            $deviceType = "Desktop";
        }
    
        if (preg_match('/Windows/i', $userAgent)) {
            $os = "Windows";
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = "Mac OS";
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = "Linux";
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = "Android";
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = "iOS";
        } else {
            $os = "Unknown OS";
        }
    
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = "Internet Explorer";
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = "Firefox";
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = "Chrome";
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = "Safari";
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = "Opera";
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = "Edge";
        } else {
            $browser = "Unknown Browser";
        }
    
        return [
            'ip_address' => $ipAddress,
            'device' => $deviceType,
            'os' => $os,
            'browser' => $browser
        ];
    }

    // Set a cookie securely (supports all panels)
    function setsCookie($cookieName, $cookieValue, $days = 365) {
        $expiryTime = time() + ($days * 24 * 60 * 60);
    
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    
        setcookie($cookieName, $cookieValue, [
            'expires' => $expiryTime,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax', // Use 'None' if cross-domain needed (and use HTTPS)
        ]);
    }
    
    // Get the value of a cookie
    function getCookie($cookieName) {
        return $_COOKIE[$cookieName] ?? null;
    }
    
    // Logout: clear all cookies and destroy session
    function logoutCookie() {
        // Expire all cookies
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    
        // Clear session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    function escape_string($value) {
        if (!is_string($value)) {
            return $value;
        }
        $search = array('\\', "\0", "\n", "\r", "'", '"', "\x1a");
        $replace = array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z');
        return str_replace($search, $replace, $value);
    }   

    function getData($tableName, $coloum_name, $type = "* FROM", $params = []) {
        $pdo = connectDatabase(); // PDO connection

        // Build SQL
        $sql = "SELECT $type `$tableName` $coloum_name";

        try {
            $stmt = $pdo->prepare($sql); // prepare statement

            // Bind parameters if any
            foreach ($params as $key => $value) {
                // Detect integer for proper PDO type
                $pdoType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $pdoType);
            }

            $stmt->execute(); // execute

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                foreach ($row as $col => $val) {
                    if (is_null($val)) {
                        $row[$col] = '--';
                    }
                }
            }

            if ($data) {
                return json_encode(['status' => true, 'response' => $data]);
            } else {
                return json_encode(['status' => false, 'response' => []]);
            }

        } catch (PDOException $e) {
            error_log("getData PDO Error: " . $e->getMessage());
            return json_encode(['status' => false, 'response' => []]);
        }
    }

    function insertData($tableName, $columns, $values) {
        $pdo = connectDatabase(); 

        try {
            $stmtColumns = $pdo->prepare("SHOW COLUMNS FROM `$tableName`");
            $stmtColumns->execute();
            $tableCols = $stmtColumns->fetchAll(PDO::FETCH_ASSOC);

            $finalColumns = [];
            $finalValues = [];
            $placeholders = [];

            $userData = array_combine($columns, $values);

            foreach ($tableCols as $col) {
                $colName = $col['Field'];

                if (strpos(strtolower($col['Extra']), 'auto_increment') !== false && !isset($userData[$colName])) {
                    continue;
                }

                $finalColumns[] = $colName;
                $placeholders[] = ":val_$colName";

                if (isset($userData[$colName])) {
                    $finalValues[$colName] = $userData[$colName];
                } else {
                    if ($col['Default'] !== null) {
                        $finalValues[$colName] = $col['Default'];
                    } else {
                        $finalValues[$colName] = "--";
                    }
                }
            }

            $sql = "INSERT INTO `$tableName` (" . implode(", ", $finalColumns) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $pdo->prepare($sql);

            foreach ($finalValues as $colName => $val) {
                $stmt->bindValue(":val_$colName", $val);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Insert failed: " . $e->getMessage());
            return false;
        }
    }

    function updateData($tableName, $columns, $values, $condition) {
        $pdo = connectDatabase(); 

        $setClauses = [];
        foreach ($columns as $index => $col) {
            $setClauses[] = "$col = :val$index";
        }
        $setString = implode(", ", $setClauses);

        $sql = "UPDATE `$tableName` SET $setString WHERE $condition";

        try {
            $stmt = $pdo->prepare($sql);

            foreach ($values as $index => $value) {
                if ($value === "" || is_null($value)) {
                    $value = "--";
                }

                $stmt->bindValue(":val$index", $value);
            }

            return $stmt->execute(); 
        } catch (PDOException $e) {
            error_log("updateData PDO Error: " . $e->getMessage());
            return false;
        }
    }

    function deleteData($tableName, $condition) {
        $pdo = connectDatabase(); // PDO connection

        $sql = "DELETE FROM `$tableName` WHERE $condition";

        try {
            $stmt = $pdo->prepare($sql);
            return $stmt->execute(); // returns true/false
        } catch (PDOException $e) {
            error_log("deleteData PDO Error: " . $e->getMessage());
            return false;
        }
    }

    function limit_checker($tableName, $db_prefix) {
        $count = 1;

        if($tableName == "transactions"){
            $response_limit = json_decode(getData($db_prefix.'transaction',' WHERE status = "completed"'),true);
            if($response_limit['status'] == true){
                foreach($response_limit['response'] as $row){
                    $count = $count+1;
                }
            }
        }else{
            $response_limit = json_decode(getData($db_prefix.'domain',' '),true);
            if($response_limit['status'] == true){
                foreach($response_limit['response'] as $row){
                    $count = $count+1;
                }
            }
        }

        return $count; 
    }

    function generateStrongPassword($length = 8) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%&!';
        return substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
    }

    function generateItemID($length = 10, $maxLength = 10)
    {
        // Ensure length does not exceed max
        $length = ($length > $maxLength) ? $maxLength : $length;

        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= mt_rand(0, 9);
        }

        return $id;
    }

    function getNameChars(string $fullName, int $length = 2): string
    {
        $fullName = trim($fullName);

        if ($fullName === '' || $length <= 0) {
            return '';
        }

        // Split name by spaces (remove extra spaces)
        $parts = array_values(array_filter(explode(' ', $fullName)));

        // If multiple words, use first + last
        if (count($parts) > 1) {
            $first = $parts[0];
            $last  = end($parts);

            $result = strtoupper(
                substr($first, 0, 1) .
                substr($last, 0, max(0, $length - 1))
            );
        } else {
            // Single name
            $result = strtoupper(substr($parts[0], 0, $length));
        }

        return $result;
    }

    function moneyToInt(string $amount, int $decimals = 2): int {
        $amount = money_sanitize($amount);
        $multiplier = bcpow("10", (string)$decimals);
        return (int) bcmul($amount, $multiplier, 0);
    }

    function intToMoney(int $amount, int $decimals = 2): string {
        $divisor = bcpow("10", (string)$decimals);
        return bcdiv((string)$amount, $divisor, $decimals);
    }

    function money_sanitize(string|int|float|null $value): string {
        if (is_numeric($value)) {
            return (string)$value;
        }
        return "0";
    }

    function money_add($a, $b, int $scale = 8): string {
        $a = money_sanitize($a);
        $b = money_sanitize($b);
        return bcadd($a, $b, $scale);
    }

    function money_sub($a, $b, int $scale = 8): string {
        $a = money_sanitize($a);
        $b = money_sanitize($b);
        return bcsub($a, $b, $scale);
    }

    function money_mul($a, $b, int $scale = 8): string {
        $a = money_sanitize($a);
        $b = money_sanitize($b);
        return bcmul($a, $b, $scale);
    }

    function money_div($a, $b, int $scale = 8): string {
        $a = money_sanitize($a);
        $b = money_sanitize($b);
        if (bccomp($b, '0', $scale) === 0) {
            return "0";
        }
        return bcdiv($a, $b, $scale);
    }

    function money_round($amount, int $decimals = 2): string {
        $amount = money_sanitize($amount);
        $factor = bcpow('10', (string)($decimals + 1));
        $tmp = bcmul($amount, $factor, 0);
        $tmp = bcdiv($tmp, '10', 0); 
        return bcdiv($tmp, bcpow('10', (string)$decimals), $decimals);
    }

    function pp_get_gateway_options($gateway_id = '', $brand_id = ''){
        global $db_prefix;

        $options = [];

        if ($gateway_id === '' || $brand_id === '') {
            return $options;
        }

        $params = [ ':gateway_id' => $gateway_id, ':brand_id' => $brand_id ];
        $response_gateways_parameter = json_decode(getData($db_prefix.'gateways_parameter','WHERE gateway_id = :gateway_id AND brand_id = :brand_id', '* FROM', $params),true);

        if ($response_gateways_parameter['status'] == true) {
            foreach($response_gateways_parameter['response'] as $field){
                $value = $field['value'];

                if(!empty($field['multiple']) && !empty($value)){
                    $value = is_array($value) ? $value : json_decode($value, true);
                }

                $options[$field['option_name']] = $value;
            }
        }

        return $options;
    }

    function pp_bkash_tokenized_refund($transaction = [], $refund = []){
        $gateway_id = $transaction['gateway_id'] ?? '';
        $brand_id = $transaction['brand_id'] ?? '';

        if ($gateway_id === '' || $brand_id === '') {
            return [
                'status' => false,
                'message' => 'Gateway or brand not found.',
            ];
        }

        $options = $refund['options'] ?? pp_get_gateway_options($gateway_id, $brand_id);

        if (empty($options)) {
            return [
                'status' => false,
                'message' => 'bKash configuration is missing.',
            ];
        }

        if (isset($options['auto_refund']) && $options['auto_refund'] === 'off') {
            return [
                'status' => false,
                'message' => 'Auto refund is disabled for this gateway.',
            ];
        }

        $gateway_path = __DIR__ . '/../pp-modules/pp-gateways/bkash-api-tokenized/class.php';
        if (!file_exists($gateway_path)) {
            return [
                'status' => false,
                'message' => 'bKash gateway not installed.',
            ];
        }

        require_once $gateway_path;

        if (!class_exists('BkashApiTokenizedGateway')) {
            return [
                'status' => false,
                'message' => 'bKash gateway class not found.',
            ];
        }

        $gateway = new BkashApiTokenizedGateway();

        if (!method_exists($gateway, 'refund')) {
            return [
                'status' => false,
                'message' => 'Refund not supported by this gateway.',
            ];
        }

        $payload = [
            'transaction' => $transaction,
            'options' => $options,
            'refund' => $refund,
        ];

        return $gateway->refund($payload);
    }


    function verifyPaymentTolerance(string $checkout, string $paid, string $tolerance): bool{
        $checkout  = money_round($checkout);
        $paid      = money_round($paid);
        $tolerance = money_round($tolerance);

        if (bccomp($checkout, "0", 8) <= 0 || bccomp($paid, "0", 8) <= 0) {
            return false;
        }

        // max allowed = checkout + tolerance
        $maxAllowed = money_add($checkout, $tolerance);

        return (
            bccomp($paid, $checkout, 8) >= 0 &&
            bccomp($paid, $maxAllowed, 8) <= 0
        );
    }

    function dateformat($date, $format = 'd/m/Y') {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    function convertUTCtoUserTZ($utc_time, $user_tz = 'UTC', $format = 'Y-m-d H:i:s') {
        $dt = new DateTime($utc_time, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone($user_tz));
        return $dt->format($format);
    }

    function isExpired($expires_at){
        if (empty($expires_at) || $expires_at === '--') {
            return false; 
        }

        $timestamp = strtotime($expires_at);

        if ($timestamp === false) {
            return true;
        }

        if (preg_match('/^\d{1,4}[-\/]\d{1,2}[-\/]\d{1,4}$/', $expires_at)) {
            $timestamp = strtotime(date('Y-m-d 23:59:59', $timestamp));
        }

        return time() > $timestamp;
    }

    function getParam(array $params, string $key): ?string {
        if (!isset($params[$key]) || !is_string($params[$key])) {
            return null;
        }

        $value = trim($params[$key]);
        if ($value === '') return null;

        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $value)) {
            return null;
        }

        return escape_string($value);
    }

    function getDomainValue($input) {
        $input = trim($input);

        if ($input === '') {
            return false;
        }

        if (!preg_match('#^https?://#i', $input)) {
            $input = 'http://' . $input;
        }

        $host = parse_url($input, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        $host = preg_replace('/^www\./i', '', $host);

        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            if (!preg_match('/^(?!-)(?:[a-z0-9-]{1,63}\.)+[a-z]{2,}$/i', $host)) {
                return false;
            }
        }

        return strtolower($host);
    }

    function sendIPN(string $url, array $payload): int {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $json,
            CURLOPT_HTTPHEADER      => [
                'Content-Type: application/json',
                'Connection: close'
            ],
            CURLOPT_RETURNTRANSFER  => false,
            CURLOPT_HEADER          => false,
            CURLOPT_CONNECTTIMEOUT  => 3,
            CURLOPT_TIMEOUT         => 5,
            CURLOPT_FORBID_REUSE    => true,
            CURLOPT_NOSIGNAL        => true,
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_WRITEFUNCTION   => function($ch, $data) { return strlen($data); },
        ]);

        $result = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === false) {
            $httpCode = 0; 
        }

        curl_close($ch);

        return $httpCode;
    }

    function sendIPNMulti(array $jobs): array{
        $mh = curl_multi_init();
        $handles = [];
        $results = [];

        foreach ($jobs as $job) {
            $json = json_encode($job['payload'], JSON_UNESCAPED_UNICODE);

            $ch = curl_init($job['url']);
            curl_setopt_array($ch, [
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => $json,
                CURLOPT_HTTPHEADER      => [
                    'Content-Type: application/json',
                    'Connection: close'
                ],
                CURLOPT_RETURNTRANSFER  => false,
                CURLOPT_CONNECTTIMEOUT  => 3,
                CURLOPT_TIMEOUT         => 5,
                CURLOPT_FORBID_REUSE    => true,
                CURLOPT_NOSIGNAL        => true,
                CURLOPT_SSL_VERIFYPEER  => true,
                CURLOPT_SSL_VERIFYHOST  => 2,
                CURLOPT_WRITEFUNCTION   => fn($ch, $data) => strlen($data),
            ]);

            curl_multi_add_handle($mh, $ch);
            $handles[(int)$ch] = [
                'handle' => $ch,
                'id'     => $job['id']
            ];
        }

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach ($handles as $item) {
            $ch = $item['handle'];
            $id = $item['id'];

            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code === 0) {
                $code = 0;
            }

            $results[$id] = $code;

            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);

        return $results; 
    }

    function senderWhitelist(?string $sender = null, ?string $providerKey = null, string $mode = 'provider', ?string $providerName = null) {
        $providers = [
            'bkash' => [
                'name'     => 'bKash',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['bkash'],
            ],
            'nagad' => [
                'name'     => 'Nagad',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['nagad'],
            ],
            'rocket' => [
                'name'     => 'Rocket',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['16216'],
            ],
            'upay' => [
                'name'     => 'Upay',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['upay'],
            ],
            'tap' => [
                'name'     => 'Tap',
                'currency' => 'USD',
                'balance_verify' => 'true',
                'senders'  => ['tap.'],
            ],
            'cellfin' => [
                'name'     => 'Cellfin',
                'currency' => 'BDT',
                'balance_verify' => 'false',
                'senders'  => ['ibbl .'],
            ],
            'okwallet' => [
                'name'     => 'Ok Wallet',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['01847-348685'],
            ],
            'mcash' => [
                'name'     => 'mCash',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['16259'],
            ],
            'pathaopay' => [
                'name'     => 'Pathao Pay',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['pathaopay'],
            ],
            'telecash' => [
                'name'     => 'TeleCash',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['telecash'],
            ],            
            'ipay' => [
                'name'     => 'Ipay',
                'currency' => 'BDT',
                'balance_verify' => 'true',
                'senders'  => ['09638-900800'],
            ],
        ];

        if ($mode === 'senders') {
            $allSenders = [];
            foreach ($providers as $provider) {
                $allSenders = array_merge($allSenders, $provider['senders']);
            }
            $allSenders = array_values(array_unique($allSenders));
            return $allSenders;
        }

        if ($sender !== null) {
            $sender = strtolower(trim($sender));
            foreach ($providers as $key => $provider) {
                foreach ($provider['senders'] as $s) {
                    if (strtolower($s) === $sender) {
                        return [
                            'provider_key'   => $key,
                            'name'           => $provider['name'],
                            'currency'       => $provider['currency'],
                            'balance_verify'       => $provider['balance_verify'],
                            'sender'         => $sender,
                        ];
                    }
                }
            }
            return false; 
        }

        if ($providerKey !== null) {
            return $providers[$providerKey] ?? false;
        }

        if ($providerName !== null) {
            $providerName = strtolower(trim($providerName));
            foreach ($providers as $key => $provider) {
                if (strtolower($provider['name']) === $providerName) {
                    return [
                        'provider_key' => $key,
                        'name'         => $provider['name'],
                        'currency'     => $provider['currency'],
                        'balance_verify'     => $provider['balance_verify'],
                        'senders'      => $provider['senders'],
                    ];
                }
            }
            return false;
        }

        return $providers;
    }


    function MFSMessageVerified(string $mfs, string $message){
        $message = trim(preg_replace('/\s+/', ' ', $message));

        $formats = [
            'bkash' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/You have received Tk ([\d,.]+) from (\d+)\.(?:\s*Ref[:\-]?\s*(\S+))? Fee Tk ([\d,.]+)\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'ref', 'fee', 'balance', 'trxid', 'datetime']
                ],
                [
                    'type'     => 'Personal',
                    'priority' => 90,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Fee Tk ([\d,.]+)\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'fee', 'balance', 'trxid', 'datetime']
                ],
                [
                    'type'     => 'Merchant',
                    'priority' => 80,
                    'pattern'  => '/You have received payment Tk ([\d,.]+) from (\d+)\.(?:\s*Ref[:\-]?\s*(\S+))? Fee Tk ([\d,.]+)\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'ref', 'fee', 'balance', 'trxid', 'datetime']
                ],

                /*
                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'nagad' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/Money Received[\s\.]*Amount:\s*(?:Tk\s*)?([\d,]+(?:\.\d+)?)[\s\.]*Sender:\s*(\d+)[\s\.]*(?:Ref[:\-]?\s*(\S+)[\s\.]*)?T(?:xn|rx)I[dD]:\s*([A-Z0-9]+)[\s\.]*Balance:\s*(?:Tk\s*)?([\d,]+(?:\.\d+)?)[\s\.]*([\w\/:\-\s]+)/i',
                    'map'      => ['amount', 'sender', 'ref', 'trxid', 'balance', 'datetime']
                ],
                [
                    'type'     => 'Personal',
                    'priority' => 90,
                    'pattern'  => '/Cash In Received[\s\.]*Amount:\s*(?:Tk\s*)?([\d,]+(?:\.\d+)?)[\s\.]*Uddokta:\s*(\d+)[\s\.]*T(?:xn|rx)I[dD]:\s*([A-Z0-9]+)[\s\.]*Balance:\s*(?:Tk\s*)?([\d,]+(?:\.\d+)?)[\s\.]*([\w\/:\-\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'balance', 'datetime']
                ],
                // 🔹 FALLBACK ROBUST PATTERNS
                [
                    'type'     => 'Personal',
                    'priority' => 80,
                    'pattern'  => '/(?:Money|Cash In) Received.*?Amount.*?(?:Tk)?\s*([\d,]+(?:\.\d+)?).*?(?:Sender|Uddokta).*?(\d+).*?T(?:xn|rx)I[dD].*?([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'rocket' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/Tk([\d,.]+) received from A\/C:([*\d]+) Fee:Tk([\d,.]+)\, Your A\/C Balance: Tk([\d,.]+) TxnId:([A-Z0-9]+)(?: Date:([\w\-:\s]+))?/i',
                    'map'      => ['amount', 'sender', 'fee', 'balance', 'trxid', 'datetime']
                ],
                [
                    'type'     => 'Personal',
                    'priority' => 90,
                    'pattern'  => '/Cash in Tk([\d,.]+) from ([*\d]+) is successful\. Fee Tk([\d,.]+)\. Balance Tk([\d,.]+)\. TxnId:([A-Z0-9]+)(?: Date:([\w\-:\s]+))?/i',
                    'map'      => ['amount', 'sender', 'fee', 'balance', 'trxid', 'datetime']
                ],
                // 🔹 FALLBACK ROBUST PATTERNS
                [
                    'type'     => 'Personal',
                    'priority' => 80,
                    'pattern'  => '/(?:received|Cash in).*?Tk\s*([\d,.]+).*?(?:from|A\/C:).*?([*\d]+).*?T(?:xn|rx)I[dD]:.*?([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'trxid']
                ],
            ],
            'upay' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/Tk\. ([\d,.]+) has been received from (\d+)\.(?:\s*Ref[:\-]?\s*(\S+))? Balance Tk\. ([\d,.]+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)\./i',
                    'map'      => ['amount', 'sender', 'ref', 'balance', 'trxid', 'datetime']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'tap' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/Received Tk ([\d,.]+) from (\d+)\. Balance Tk\. ([\d,.]+)\. TxID: ([A-Z0-9]+)\./i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'cellfin' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/Islami Bank CellFin Received ([\d,.]+) Tk From CellFin: (\d+) To CellFin: (\d+) TrxId: ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'receiver', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'okwallet' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/\(OK Wallet\) Successfully received Tk ([\d,.]+) from A\/C (\d+)\.(?:\s*Ref[:\-]?\s*(\S+))? Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'ref', 'balance', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'mcash' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/IBBL mCash You have received Tk: ([\d,.]+) From: (\d+)(?:\s*Reference:\s*(\S*))? Balance Tk: ([\d,.]+) TrxID: ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'ref', 'balance', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],
            'pathaopay' => [
                // 🔹 PERSONAL (Most specific first)
                [
                    'type'     => 'Personal',
                    'priority' => 100,
                    'pattern'  => '/You have received BDT ([\d,.]+) from (\+?\d+)\. Balance BDT ([\d,.]+) TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],

                /*
                [
                    'type'     => 'Merchant',
                    'priority' => 70,
                    'pattern'  => '/received a payment of Tk ([\d,.]+) from (\d+)\. TrxID ([A-Z0-9]+) at ([\d\/:\s]+)/i',
                    'map'      => ['amount', 'sender', 'trxid', 'datetime']
                ],

                // 🔹 AGENT
                [
                    'type'     => 'Agent',
                    'priority' => 60,
                    'pattern'  => '/Cash In Tk ([\d,.]+) from (\d+) successful\. Balance Tk ([\d,.]+)\. TrxID ([A-Z0-9]+)/i',
                    'map'      => ['amount', 'sender', 'balance', 'trxid']
                ],*/
            ],




        ];

        if (!isset($formats[strtolower($mfs)])) {
            return false;
        }

        // 🔥 Sort by priority (DESC)
        usort($formats[strtolower($mfs)], fn($a, $b) => $b['priority'] <=> $a['priority']);

        foreach ($formats[strtolower($mfs)] as $format) {
            if (preg_match($format['pattern'], $message, $matches)) {

                $data = [
                    'mfs'  => strtolower($mfs),
                    'type' => $format['type'],
                    'raw'  => $message,
                ];

                // Map values safely
                foreach ($format['map'] as $i => $key) {
                    $data[$key] = $matches[$i + 1] ?? null;
                }

                // Normalize numbers
                foreach (['amount', 'balance', 'fee'] as $field) {
                    if (isset($data[$field]) && $data[$field] !== null) {
                        $data[$field] = str_replace(',', '', $data[$field]);
                    }
                }

                return $data;
            }
        }

        return false;
    }

    function reconcileByLongestChain($device_id, $sender_key, $type){
        global $db_prefix;

        $resBalance = json_decode(getData($db_prefix.'balance_verification', 'WHERE device_id="'.$device_id.'" AND sender_key="'.$sender_key.'" AND type="'.$type.'"'),true);

        $canonicalBalanceInt = 0;

        if (!empty($resBalance['response'][0]['current_balance'])) {
            $canonicalBalanceInt = moneyToInt($resBalance['response'][0]['current_balance']);
        }

        $res = json_decode(getData($db_prefix.'sms_data','WHERE device_id="'.$device_id.'" AND sender_key="'.$sender_key.'" AND type="'.$type.'" AND status IN ("approved","awaiting-review") AND source IN ("app") ORDER BY id ASC'),true);

        $smsList = $res['response'] ?? [];
        if (count($smsList) < 1) return;

        foreach ($smsList as &$s) {
            $amountInt  = moneyToInt($s['amount'] ?? "0");
            $balanceInt = moneyToInt($s['balance'] ?? "0");

            if ($amountInt <= 0 || $balanceInt <= 0) continue;

            $s['amount_int']  = $amountInt;
            $s['balance_int'] = $balanceInt;

            $s['prev'] = $balanceInt - $amountInt;
            $s['bal']  = $balanceInt;
        }
        unset($s);

        $next = [];

        foreach ($smsList as $s) {
            if (!isset($s['prev'])) continue;
            $next[$s['prev']][] = $s;
        }

        $bestChain = [];
        $queue = [$canonicalBalanceInt];

        while (!empty($queue)) {

            $current = array_shift($queue);

            if (!isset($next[$current])) continue;

            foreach ($next[$current] as $sms) {
                $chain = [];
                $tempCurrent = $current;
                $tempNext = $next;

                while (isset($tempNext[$tempCurrent]) && count($tempNext[$tempCurrent]) > 0) {

                    $smsInChain = array_shift($tempNext[$tempCurrent]);

                    $chain[] = $smsInChain;
                    $tempCurrent = $smsInChain['bal'];
                }

                if (count($chain) > count($bestChain)) {
                    $bestChain = $chain;
                }
            }
        }

        if (count($bestChain) < 1) return;

        $idsToApprove = array_column($bestChain, 'id');

        if (!empty($idsToApprove)) {

            updateData($db_prefix.'sms_data',['status','reason','updated_date'],['approved','--',getCurrentDatetime('Y-m-d H:i:s')],'id IN ('.implode(',', $idsToApprove).')');
        }

        $last = end($bestChain);
        $finalBalanceInt = $last['bal'];

        $finalBalance = intToMoney($finalBalanceInt, 2);

        updateData($db_prefix.'balance_verification', ['current_balance','updated_date'], [$finalBalance, getCurrentDatetime('Y-m-d H:i:s')], 'device_id="'.$device_id.'" AND sender_key="'.$sender_key.'" AND type="'.$type.'"');
    }

    function permissionSchema(){
        $permissionSchema = [
            'resources' => [
                'customers' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'transaction' => [
                    'edit'      => true,
                    'delete'    => true,
                    'approve'   => true,
                    'cancel'   => true,
                    'refund'    => true,
                    'send_ipn'  => true
                ],
                'invoice' => [
                    'create'    => true,
                    'edit'      => true,
                    'delete'    => true
                ],
                'payment_link' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'gateways' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'addons' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'brand_settings' => [
                    'view' => true,
                    'edit'   => true
                ],
                'api_settings' => [
                    'view' => true,
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'theme_settings' => [
                    'view' => true,
                    'edit'   => true
                ],
                'faq_settings' => [
                    'view' => true,
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'currency_settings' => [
                    'view' => true,
                    'sync_rate' => true,
                    'import'   => true,
                    'edit'   => true
                ],
                'sms_data' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'device' => [
                    'connect' => true,
                    'delete'  => true,
                    'balance_verification_for'  => true
                ],
                'brands' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'staff' => [
                    'create' => true,
                    'edit'   => true,
                    'delete' => true,
                    'assign_brand_to' => true,
                    'edit_permission' => true,
                    'view_permission_list' => true,
                    'delete_permission_of' => true
                ],
                'domains' => [
                    'whitelist' => true,
                    'edit'   => true,
                    'delete' => true
                ],
                'system_settings' => [
                    'manage_general' => true,
                    'manage_cron' => true,
                    'manage_update'   => true,
                    'manage_import'   => true
                ],
            ],
            'pages' => [
                'dashboard' => true,
                'reports' => true,
                'customers' => true,
                'transaction' => true,
                'invoice' => true,
                'payment_link' => true,
                'gateways' => true,
                'addons' => true,
                'brand_settings' => true,
                'sms_data' => true,
                'device' => true,
                'brands' => true,
                'staff_management' => true,
                'domains' => true,
                'system_settings' => true,
            ]
        ];

        return $permissionSchema ?? [];
    }

    function countPermissions($tabKey, $tabData) {
        $count = 0;

        if ($tabKey === 'resources') {
            foreach ($tabData as $module => $actions) {
                $count += count($actions);
            }
        }

        if ($tabKey === 'pages') {
            $count = count($tabData);
        }

        return $count;
    }
    function hasPermission($permissions, $module, $action = 'view', $adminType = 'staff') {
        if ($adminType === 'admin') {
            return true;
        }

        return isset($permissions['resources'][$module][$action])
            && $permissions['resources'][$module][$action] === true;
    }

    function canAccessPage($permissions, $page, $adminType = 'staff') {
        if ($adminType === 'admin') {
            return true;
        }

        return !empty($permissions['pages'][$page]);
    }

    function get_env($option_name, $brand_id = 'both') {
        global $db_prefix;

        $option_name = escape_string($option_name);
        $brand_id = escape_string($brand_id);

        $params = [ ':brand_id' => $brand_id, ':option_name' => $option_name ];

        $response_env = json_decode(getData($db_prefix.'env','WHERE brand_id = :brand_id AND option_name = :option_name', '* FROM', $params),true);
        if($response_env['status'] == true){
            $value = $response_env['response'][0]['value'];

            if($value == '--'){
                $value = '';
            }
        }else{
            $columns = ['brand_id', 'option_name', 'value', 'created_date', 'updated_date'];
            $values = [$brand_id, $option_name, '--', getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

            insertData($db_prefix.'env', $columns, $values);

            $value = '';
        }

        return $value;
    }

    function set_env($option_name, $value, $brand_id = 'both') {
        global $db_prefix;

        $option_name = escape_string($option_name);
        $value = escape_string($value);
        $brand_id = escape_string($brand_id);

        $params = [ ':brand_id' => $brand_id, ':option_name' => $option_name ];

        $response_env = json_decode(getData($db_prefix.'env','WHERE brand_id = :brand_id AND option_name = :option_name', '* FROM', $params),true);
        if($response_env['status'] == true){
            $columns = ['brand_id', 'value', 'updated_date'];
            $values = [$brand_id, $value, getCurrentDatetime('Y-m-d H:i:s')];
            $condition = "id = '".$response_env['response'][0]['id']."'"; 
            
            updateData($db_prefix.'env', $columns, $values, $condition);
        }else{
            $columns = ['brand_id', 'option_name', 'value', 'created_date', 'updated_date'];
            $values = [$brand_id, $option_name, $value, getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

            insertData($db_prefix.'env', $columns, $values);
        }

        return $value;
    }

    function generateRandomFilename($extension) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        for ($i = 0; $i < 30; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString . "." . $extension;
    }

    function uploadImage($file, $max_file_size) {
        if (!is_dir(__DIR__.'/../../pp-media/storage')) {
            if (mkdir(__DIR__.'/../../pp-media/storage', 0755, true)) {
                $upload_directory = __DIR__ . '/../../pp-media/storage/';
            } else {
                return json_encode(['status' => false, 'message' => 'Failed to create folder!']);
            }
        }else{
            $upload_directory = __DIR__ . '/../../pp-media/storage/';
        }

        // ─────────── VALIDATION ───────────
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return json_encode(['status' => false, 'message' => 'No file uploaded or upload failed.']);
        }
    
        if ($file['size'] > $max_file_size) {
            return json_encode(['status' => false, 'message' => 'File size exceeds maximum allowed.']);
        }
    
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_info          = pathinfo($file['name']);
        $file_extension     = strtolower($file_info['extension']);
    
        if (!in_array($file_extension, $allowed_extensions)) {
            return json_encode(['status' => false, 'message' => 'Only JPG, PNG, GIF, and WEBP files are allowed.']);
        }
    
        // ─────────── FILE NAME ───────────
        $random_filename = generateRandomFilename($file_extension);
        $full_path       = $upload_directory . $random_filename;
    
        // ─────────── TRY IMAGICK ───────────
        try {
            if (!extension_loaded('imagick')) {
                throw new Exception('Imagick extension not installed.');
            }
    
            $img = new Imagick($file['tmp_name']);
    
            $hasAlpha = $img->getImageAlphaChannel();
    
            if ($hasAlpha && Imagick::queryFormats('WEBP')) {
                $img->setImageFormat('webp');
                $img->setOption('webp:lossless', 'true');
                $img->setImageCompressionQuality(85);
                $random_filename = generateRandomFilename('webp');
            } elseif (!$hasAlpha && Imagick::queryFormats('JPEG')) {
                $img->setImageFormat('jpeg');
                $img->setImageCompression(Imagick::COMPRESSION_JPEG);
                $img->setImageCompressionQuality(75);
                $random_filename = generateRandomFilename('jpg');
            } else {
                throw new Exception('Required format not supported by Imagick.');
            }
    
            $full_path = $upload_directory . $random_filename;
    
            $img->stripImage();
            $img->writeImage($full_path);
            $img->clear();
            $img->destroy();
    
            return json_encode(['status' => true, 'file' => $random_filename]);
    
        } catch (Exception $e) {
            // ───── FALLBACK: MOVE FILE DIRECTLY ─────
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                return json_encode([
                    'status' => true,
                    'file'   => $random_filename,
                    'note'   => 'Imagick not used. File uploaded without processing.'
                ]);
            } else {
                return json_encode(['status' => false, 'message' => 'File upload failed without Imagick: ' . $e->getMessage()]);
            }
        }
    }
    
    function deleteImage($file) {
        // Define the local image directory path
        $upload_directory = __DIR__ . '/../../pp-media/storage/'; // Update path if different
    
        // Sanitize the filename to prevent directory traversal attacks
        $filename = basename($file);
        $full_path = $upload_directory . $filename;
    
        // Check if the file exists
        if (!file_exists($full_path)) {
            return json_encode(["status" => false, "message" => "File not found."]);
        }
    
        // Attempt to delete the file
        if (unlink($full_path)) {
            return json_encode(["status" => true, "message" => "File deleted successfully!"]);
        } else {
            return json_encode(["status" => false, "message" => "Error deleting file."]);
        }
    }


    function deleteFolder($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? deleteFolder($path) : unlink($path);
        }
        rmdir($dir);
    }

    function copyFolder($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while(false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $srcPath = $src . '/' . $file;
                $dstPath = $dst . '/' . $file;

                if (is_dir($srcPath)) {
                    copyFolder($srcPath, $dstPath);
                } else {
                    copy($srcPath, $dstPath);
                }
            }
        }
        closedir($dir);
    }

    function zipFolder($source, $zipFile) {
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source)
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $zip->addFile($file, substr($file, strlen($source) + 1));
            }
        }
        $zip->close();
    }

    function runSql($file) {
        $pdo = connectDatabase();

        if (!file_exists($file)) {
            throw new Exception("SQL file not found");
        }

        $sql = file_get_contents($file);

        try {
            $pdo->beginTransaction();

            // Split SQL safely
            $queries = array_filter(array_map('trim', explode(";\n", $sql)));

            foreach ($queries as $query) {
                if ($query !== '') {
                    $pdo->exec($query);
                }
            }

            $pdo->commit();
            return true;

        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Update SQL failed: ' . $e->getMessage());
            throw new Exception('Database update failed');
        }
    }

    function backupDatabasePDO($backupPath) {
        $pdo = connectDatabase();
        $pdo->exec("SET NAMES utf8mb4");

        $fh = fopen($backupPath, 'w');

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {

            $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM)[1];
            fwrite($fh, "DROP TABLE IF EXISTS `$table`;\n$create;\n\n");

            $stmt = $pdo->query("SELECT * FROM `$table`", PDO::FETCH_ASSOC);
            foreach ($stmt as $row) {
                $vals = [];
                foreach ($row as $val) {
                    $vals[] = ($val === null) ? "NULL" : $pdo->quote($val);
                }
                fwrite($fh, "INSERT INTO `$table` VALUES (" . implode(',', $vals) . ");\n");
            }

            fwrite($fh, "\n");
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);
    }

    function extractUpdate($zipFile, $destination) {
        $zip = new ZipArchive;
        if ($zip->open($zipFile) !== true) {
            throw new Exception("Cannot open ZIP file");
        }

        // Detect top-level folder in zip
        $topFolder = '';
        if ($zip->numFiles > 0) {
            $firstFile = $zip->getNameIndex(0);
            $parts = explode('/', $firstFile);
            if (count($parts) > 1) $topFolder = $parts[0] . '/';
        }

        // Extract each file manually to remove top-level folder
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            // Remove top folder prefix
            if ($topFolder && str_starts_with($entry, $topFolder)) {
                $entryNew = substr($entry, strlen($topFolder));
            } else {
                $entryNew = $entry;
            }

            if ($entryNew === '') continue; // skip folder itself

            $targetPath = $destination . '/' . $entryNew;

            if (substr($entry, -1) === '/') { // folder
                @mkdir($targetPath, 0755, true);
            } else { // file
                @mkdir(dirname($targetPath), 0755, true);
                copy("zip://$zipFile#$entry", $targetPath);
            }
        }

        $zip->close();
    }

    function addQueryParams($url, $params = []) {
        // Parse existing URL
        $parsedUrl = parse_url($url);

        // Get existing query params (if any)
        $existingParams = [];
        if (!empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $existingParams);
        }

        // Merge new params
        $finalParams = array_merge($existingParams, $params);

        // Rebuild query string
        $queryString = http_build_query($finalParams);

        // Rebuild full URL
        $baseUrl =
            ($parsedUrl['scheme'] ?? '') . ($parsedUrl['scheme'] ? '://' : '') .
            ($parsedUrl['host'] ?? '') .
            ($parsedUrl['path'] ?? '');

        return $baseUrl . '?' . $queryString;
    }































    function pp_set_lang($lang){
        $_SESSION['ui_language'] = preg_replace('/[^a-z]/', '', $lang);
    }

    function pp_site_address(){
        global $site_url;

        return $site_url;
    }

    function pp_callback_url(){
        $url = pp_site_url();

        $separator = (parse_url($url, PHP_URL_QUERY) ? '&' : '?');
        $url .= $separator . 'pp_callback';

        return $url;
    }

    function pp_ipn_url($gatewayid){
        global $site_url;

        return $site_url.'ipn/'.$gatewayid;
    }

    function pp_check_transaction($ppid = ''){
        global $db_prefix;

        $params = [ ':ref' => $ppid ];

        $response_transaciton = json_decode(getData($db_prefix.'transaction','WHERE ref = :ref','* FROM',$params),true);

        if ($response_transaciton['status'] === true) {
            return true;
        }else{
            return false;
        }
    }

    function pp_check_transaction_id($trxid = ''){
        global $db_prefix;

        $params = [ ':trx_id' => $trxid ];

        $response_transaciton = json_decode(getData($db_prefix.'transaction','WHERE trx_id = :trx_id','* FROM',$params),true);

        if ($response_transaciton['status'] === true) {
            return true;
        }else{
            return false;
        }
    }

    function pp_set_transaction_status($transactionid, $status = '', $gateway_id = '', $trxid = '', $source_info = []){
        global $db_prefix;

        $params = [ ':ref' => $transactionid, ':status' => 'initiated' ];

        $response_transaciton = json_decode(getData($db_prefix.'transaction','WHERE ref = :ref AND status = :status','* FROM',$params),true);

        if ($response_transaciton['status'] === true) {
            if($status == "canceled"){
                $columns = ['status', 'updated_date'];
                $values = ['canceled', getCurrentDatetime('Y-m-d H:i:s')];
                $condition = 'id ="'.$response_transaciton['response'][0]['id'].'"'; 

                updateData($db_prefix.'transaction', $columns, $values, $condition);
                
                $params_canceled = [ ':ref' => $response_transaciton['response'][0]['ref'], ':status' => 'canceled' ];
                $response_transaction_canceled = json_decode(getData($db_prefix.'transaction','WHERE ref = :ref AND status = :status ', '* FROM', $params_canceled),true);
                
                if ($response_transaction_canceled['status'] === true && !empty($response_transaction_canceled['response'][0]['webhook_url']) && $response_transaction_canceled['response'][0]['webhook_url'] !== '--') {
                    $metadata = json_decode($response_transaction_canceled['response'][0]['metadata'], true) ?: [];
                    $customer_info = json_decode($response_transaction_canceled['response'][0]['customer_info'], true) ?: [];
                    $response_brand = json_decode(getData($db_prefix.'brands',' WHERE brand_id ="'.$response_transaction_canceled['response'][0]['brand_id'].'"'),true);
                    
                    $net = money_sub(money_add($response_transaction_canceled['response'][0]['amount'], $response_transaction_canceled['response'][0]['processing_fee']), $response_transaction_canceled['response'][0]['discount_amount']);
                    
                    $ipnData = [
                        "pp_id" => $response_transaction_canceled['response'][0]['ref'],
                        "full_name" => $customer_info['name'] ?? 'N/A',
                        "email_address" => $customer_info['email'] ?? 'N/A',
                        "mobile_number" => $customer_info['mobile'] ?? 'N/A',
                        "gateway" => '',
                        "amount" => money_round($response_transaction_canceled['response'][0]['amount']),
                        "fee" => money_round($response_transaction_canceled['response'][0]['processing_fee']),
                        "discount_amount" => money_round($response_transaction_canceled['response'][0]['discount_amount']),
                        "total" => money_round($net),
                        "local_net_amount" => money_round($response_transaction_canceled['response'][0]['local_net_amount']),
                        "currency" => $response_transaction_canceled['response'][0]['currency'],
                        "local_currency" => $response_transaction_canceled['response'][0]['local_currency'],
                        "metadata" => $metadata,
                        "sender" => '',
                        "transaction_id" => '',
                        "status" => 'canceled',
                        "date" => convertUTCtoUserTZ($response_transaction_canceled['response'][0]['created_date'], ($response_brand['response'][0]['timezone'] === '--' || $response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $response_brand['response'][0]['timezone'], "M d, Y h:i A")
                    ];

                    $payload = json_encode($ipnData, JSON_UNESCAPED_UNICODE);
                    $jobs = [['id' => rand(), 'url' => $response_transaction_canceled['response'][0]['webhook_url'], 'payload' => json_decode($payload, true)]];
                    $results = sendIPNMulti($jobs);
                    
                    foreach ($jobs as $job) {
                        $code = $results[$job['id']] ?? 0;
                        if($code !== 200){
                            $columns = ['ref', 'brand_id', 'payload', 'url', 'created_date', 'updated_date'];
                            $values = [rand(), $response_brand['response'][0]['brand_id'], $payload, $response_transaction_canceled['response'][0]['webhook_url'], getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];
                            insertData($db_prefix.'webhook_log', $columns, $values);
                        }
                    }
                }
                                                                
                return true;
            }

            if($status == "completed"){
                $final_source_info = '--';

                if (is_array($source_info) && !empty($source_info)) {
                    $valid = true;

                    foreach ($source_info as $item) {
                        if (
                            !is_array($item) ||
                            empty($item['label']) ||
                            empty($item['value'])
                        ) {
                            $valid = false;
                            break;
                        }
                    }

                    if ($valid) {
                        $final_source_info = json_encode($source_info, JSON_UNESCAPED_UNICODE);
                    }
                }

                $params = [ ':gateway_id' => $gateway_id, ':brand_id' => $response_transaciton['response'][0]['brand_id'] ];

                $response_gateway = json_decode(getData($db_prefix.'gateways','WHERE gateway_id = :gateway_id AND brand_id = :brand_id  AND status = "active"', '* FROM', $params),true);
                if($response_gateway['status'] == true){
                    $currencyRates = [];

                    $currencyRes = json_decode(getData($db_prefix.'currency', ' WHERE brand_id = "'.$response_gateway['response'][0]['brand_id'].'"'), true);

                    if (!empty($currencyRes['response'])) {
                        foreach ($currencyRes['response'] as $c) {
                            $currencyRates[$c['code']] = money_sanitize($c['rate']);
                        }
                    }

                    $txnAmount  = money_sanitize($response_transaciton['response'][0]['amount']);
                    $txnCurrency = $response_transaciton['response'][0]['currency'];
                    $gatewayCurrency = $response_gateway['response'][0]['currency'];

                    if ($txnCurrency === $gatewayCurrency ||
                        (($txnCurrency === 'USDT' || $txnCurrency === 'USD') && ($gatewayCurrency === 'USDT' || $gatewayCurrency === 'USD'))
                    ) {
                        $convertedAmount = $txnAmount;
                    } else {
                        if ($txnCurrency === 'USDT' || $txnCurrency === 'USD') {
                            $usdRate = $currencyRates['USD'] ?? ($currencyRates['USDT'] ?? '1');
                            if (isset($currencyRates[$gatewayCurrency]) && $gatewayCurrency !== 'BDT') {
                                $convertedAmount = money_div(money_mul($txnAmount, $usdRate), $currencyRates[$gatewayCurrency]);
                            } else {
                                $convertedAmount = money_mul($txnAmount, $usdRate);
                            }
                        } else {
                            if (isset($currencyRates[$gatewayCurrency])) {
                                $convertedAmount = money_div($txnAmount, $currencyRates[$gatewayCurrency]);
                            } else {
                                $convertedAmount = "0";
                            }
                        }
                    }

                    $fixed_discount = money_sanitize( $response_gateway['response'][0]['fixed_discount']);
                    $percentage_discount = money_sanitize($response_gateway['response'][0]['percentage_discount']);

                    $fixed_charge = money_sanitize($response_gateway['response'][0]['fixed_charge']);
                    $percentage_charge = money_sanitize($response_gateway['response'][0]['percentage_charge']);

                    $percentageDiscountAmount = money_div(money_mul($convertedAmount, $percentage_discount, 8), "100", 8);
                    $totalDiscount = money_add($fixed_discount, $percentageDiscountAmount, 8);

                    $percentageChargeAmount = money_div(money_mul($convertedAmount, $percentage_charge, 8), "100", 8);
                    $totalProcessingFee = money_add($fixed_charge, $percentageChargeAmount, 8);

                    $convertedAmount = money_add(money_sub($convertedAmount, $totalDiscount, 8), $totalProcessingFee, 8);

                    if ($txnCurrency !== $gatewayCurrency && isset($currencyRates[$gatewayCurrency])) {
                        $totalDiscount = money_mul($totalDiscount, $currencyRates[$gatewayCurrency]);
                        $totalProcessingFee = money_mul($totalProcessingFee, $currencyRates[$gatewayCurrency]);
                    }
                }else{
                    return false;
                }

                $columns = ['processing_fee', 'discount_amount', 'local_net_amount', 'local_currency', 'gateway_id', 'status', 'trx_id', 'source_info', 'updated_date'];
                $values = [$totalProcessingFee, $totalDiscount, $convertedAmount, $response_gateway['response'][0]['currency'], $gateway_id, 'completed', $trxid, $final_source_info, getCurrentDatetime('Y-m-d H:i:s')];
                $condition = 'id ="'.$response_transaciton['response'][0]['id'].'"'; 

                updateData($db_prefix.'transaction', $columns, $values, $condition);

                $params = [ ':ref' => $response_transaciton['response'][0]['ref'], ':status' => 'completed' ];

                $response_transaction = json_decode(getData($db_prefix.'transaction','WHERE ref = :ref AND status = :status ', '* FROM', $params),true);

                $metadata = json_decode($response_transaction['response'][0]['metadata'], true) ?: [];

                $response_gateway = json_decode(getData($db_prefix.'gateways',' WHERE brand_id ="'.$response_transaction['response'][0]['brand_id'].'" AND gateway_id = "'.$gateway_id.'"'),true);

                $gateway = $response_gateway['response'][0]['display'] ?? '';

                $customer_info = json_decode($response_transaction['response'][0]['customer_info'], true) ?: [];

                $response_brand = json_decode(getData($db_prefix.'brands',' WHERE brand_id ="'.$response_transaction['response'][0]['brand_id'].'"'),true);

                $net = money_sub(money_add($response_transaction['response'][0]['amount'], $response_transaction['response'][0]['processing_fee']), $response_transaction['response'][0]['discount_amount']);
                
                $all_transactions = [];

                $all_transactions[] = [
                    "pp_id" => $response_transaction['response'][0]['ref'],
                    "full_name" => $customer_info['name'] ?? 'N/A',
                    "email_address" => $customer_info['email'] ?? 'N/A',
                    "mobile_number" => $customer_info['mobile'] ?? 'N/A',
                    "gateway" => $gateway,
                    "amount" => money_round($response_transaction['response'][0]['amount']),
                    "fee" => money_round($response_transaction['response'][0]['processing_fee']),
                    "discount_amount" => money_round($response_transaction['response'][0]['discount_amount']),
                    "total" => money_round($net),
                    "local_net_amount" => money_round($response_transaction['response'][0]['local_net_amount']),
                    "currency" => $response_transaction['response'][0]['currency'],
                    "local_currency" => $response_transaction['response'][0]['local_currency'],
                    "metadata" => $metadata, // ← AS-IS
                    "sender" => $response_transaction['response'][0]['sender'],
                    "transaction_id" => $response_transaction['response'][0]['trx_id'],
                    "status" => $response_transaction['response'][0]['status'],
                    "date" => convertUTCtoUserTZ($response_transaction['response'][0]['created_date'], ($response_brand['response'][0]['timezone'] === '--' || $response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $response_brand['response'][0]['timezone'], "M d, Y h:i A")
                ];

                if($response_transaction['response'][0]['webhook_url'] == "" || $response_transaction['response'][0]['webhook_url'] == "--"){

                }else{
                    $ipnData = [
                        "pp_id" => $response_transaction['response'][0]['ref'],
                        "full_name" => $customer_info['name'] ?? 'N/A',
                        "email_address" => $customer_info['email'] ?? 'N/A',
                        "mobile_number" => $customer_info['mobile'] ?? 'N/A',
                        "gateway" => $gateway,
                        "amount" => money_round($response_transaction['response'][0]['amount']),
                        "fee" => money_round($response_transaction['response'][0]['processing_fee']),
                        "discount_amount" => money_round($response_transaction['response'][0]['discount_amount']),
                        "total" => money_round($net),
                        "local_net_amount" => money_round($response_transaction['response'][0]['local_net_amount']),
                        "currency" => $response_transaction['response'][0]['currency'],
                        "local_currency" => $response_transaction['response'][0]['local_currency'],
                        "metadata" => $metadata, // ← AS-IS
                        "sender" => $response_transaction['response'][0]['sender'],
                        "transaction_id" => $response_transaction['response'][0]['trx_id'],
                        "status" => $response_transaction['response'][0]['status'],
                        "date" => convertUTCtoUserTZ($response_transaction['response'][0]['created_date'], ($response_brand['response'][0]['timezone'] === '--' || $response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $response_brand['response'][0]['timezone'], "M d, Y h:i A")
                    ];

                    $payload = json_encode($ipnData, JSON_UNESCAPED_UNICODE);

                    $jobs = [[
                        'id'      => rand(),
                        'url'     => $response_transaction['response'][0]['webhook_url'],
                        'payload' => json_decode($payload, true),
                    ]];

                    $results = sendIPNMulti($jobs);

                    foreach ($jobs as $job) {
                        $code = $results[$job['id']] ?? 0;
                        $status = ($code === 200) ? 'completed' : 'pending';

                        if($status == 'completed'){

                        }else{
                            $columns = ['ref', 'brand_id', 'payload', 'url', 'created_date', 'updated_date'];
                            $values = [rand(), $response_brand['response'][0]['brand_id'], $payload, $response_transaction['response'][0]['webhook_url'], getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

                            insertData($db_prefix.'webhook_log', $columns, $values);
                        }
                    }
                }

                if (!empty($all_transactions)) {
                    if (function_exists('do_action')) {
                        do_action('transactions.updated', $all_transactions);
                    }
                }

                return true;
            }

        }else{
            return false;
        }
    }

    function pp_checkout_address($paymentid = ''){
        global $path_payment, $paymentID124123412;

        if($paymentid !== ""){
            $paymentID124123412 = $paymentid ?? '';
        }else{
           $paymentID124123412 = $paymentID124123412 ?? '';
        }

        return pp_site_address().$path_payment.'/'.$paymentID124123412;
    }

    function pp_hexToRgba($hex, $opacity = 1) {
        $hex = str_replace('#','',$hex);
        if(strlen($hex) == 3){
            $r = hexdec($hex[0] . $hex[0]);
            $g = hexdec($hex[1] . $hex[1]);
            $b = hexdec($hex[2] . $hex[2]);
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return "rgba($r,$g,$b,$opacity)";
    }

    function pp_assets($position = ''){
        global $site_url;

        if($position == "head"){
            echo '
                <link rel="stylesheet" href="'.$site_url.'assets/css/tabler.min.css?v=1.7" />
                <link rel="stylesheet" href="'.$site_url.'assets/css/choices.min.css">

                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-flags.min.css" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-payments.min.css" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-socials.min.css" />
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" />

                <style>
                    @import url("'.$site_url.'assets/css/inter.css");
                </style>
            ';
        }else{
            echo '
                <script src="'.$site_url.'assets/js/tabler.min.js"></script>
                <script src="'.$site_url.'assets/js/jquery-3.6.4.min.js"></script>
                <script src="'.$site_url.'assets/js/custom-toast.js?v=1.5"></script>
                <script src="'.$site_url.'assets/js/choices.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/hugerte@1/hugerte.min.js"></script>
                <script>
                    function pp_copy(text, msg = \'Copied!\', el = null) {
                        let origHtml = \'\';
                        if(el) {
                            origHtml = el.innerHTML;
                            el.innerHTML = \'<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>\';
                            setTimeout(() => { el.innerHTML = origHtml; }, 1500);
                        }

                        if (!text) {
                            if (typeof createToast === "function") {
                                createToast({
                                    title: "Error",
                                    description: "No content to copy",
                                    svg: \'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>\',
                                    timeout: 1500,
                                    top: 20
                                });
                            }
                            return;
                        }

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(function() {
                                if (typeof createToast === "function") {
                                    createToast({
                                        title: "Copied",
                                        description: msg,
                                        timeout: 1500,
                                        top: 20
                                    });
                                }
                            }).catch(function(err) {
                                console.error("Clipboard API failed:", err);
                                fallbackCopyTextToClipboard(text, msg);
                            });
                        } else {
                            fallbackCopyTextToClipboard(text, msg);
                        }
                    }

                    function fallbackCopyTextToClipboard(text, msg) {
                        const textarea = document.createElement("textarea");
                        textarea.value = text;
                        textarea.style.position = "fixed";
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            document.execCommand("copy");
                            if (typeof createToast === "function") {
                                createToast({
                                    title: "Copied",
                                    description: msg,
                                    timeout: 1500,
                                    top: 20
                                });
                            }
                        } catch (err) {
                            console.error("Copy failed:", err);
                        }
                        document.body.removeChild(textarea);
                    }
                </script>
            ';
        }
    }

    function pp_downloadReceiptPDF($data = []){

        if (!$data) {
            die('Invalid transaction');
        }

        $tx = $data['transaction'];
        $brand = $data['brand'];

        $amountPaid = money_add(money_sub($tx['amount'], $tx['discount_amount']), $tx['processing_fee']);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        if (!empty($brand['logo'])) {
            $pdf->Image($brand['logo'], 10, 10, 35);
        }

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(50, 12);
        $pdf->Cell(0, 8, $brand['name'], 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(50);
        $pdf->Cell(0, 6, $brand['address']['city'].', '.$brand['address']['country'], 0, 1);

        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');

        $status = strtoupper($tx['status']);

        $statusColors = [
            'COMPLETED' => [46,204,113],
            'PENDING'   => [241,196,15],
            'REFUNDED'  => [52,152,219],
            'CANCELED'  => [231,76,60],
        ];

        $color = $statusColors[$status] ?? [120,120,120];

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor($color[0], $color[1], $color[2]);
        $pdf->Cell(0, 8, 'STATUS: '.$status, 0, 1, 'C');
        $pdf->SetTextColor(0,0,0);

        $pdf->Ln(6);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, 'Amount Paid', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 22);
        $pdf->Cell(0, 12, money_round($amountPaid, 2), 0, 1, 'C');

        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, 'Local Net Amount: '.money_round($tx['local_net_amount'], 2).' '.$tx['local_currency'], 0, 1, 'C');

        $pdf->Ln(6);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(6);

        sectionTitle($pdf, 'Transaction Details');
        infoRow($pdf, 'Transaction Ref', $tx['ref']);
        infoRow($pdf, 'Payment Method', $tx['payment_method']);
        infoRow($pdf, 'Created Date', convertUTCtoUserTZ($tx['created_date'], ($brand['locale']['timezone'] === '--' || $brand['locale']['timezone'] === '') ? 'Asia/Dhaka' : $brand['locale']['timezone'], "M d, Y h:i A"));

        $pdf->Ln(3);
        sectionTitle($pdf, 'Customer Details');
        infoRow($pdf, 'Name', $tx['customer']['name']);
        infoRow($pdf, 'Email', $tx['customer']['email']);
        infoRow($pdf, 'Mobile', $tx['customer']['mobile']);

        $pdf->Ln(3);
        sectionTitle($pdf, 'Payment Breakdown');
        infoRow($pdf, 'Amount', money_round($tx['amount'], 2).' '.$tx['currency']);
        infoRow($pdf, 'Discount', money_round($tx['discount_amount'], 2).' '.$tx['currency']);
        infoRow($pdf, 'Processing Fee', money_round($tx['processing_fee'], 2).' '.$tx['currency']);


        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->Cell(0, 6, 'This is a system generated receipt.', 0, 1, 'C');

        $pdf->Output('D', 'Receipt-'.$tx['ref'].'.pdf');
    }

    function sectionTitle($pdf, $title)
    {
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 8, $title, 0, 1);
    }

    function infoRow($pdf, $label, $value)
    {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(60, 8, $label, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 8, $value, 0, 1);
    }

    function resolveModuleLanguage($brandLanguage, array $supportedLanguages)
    {
        if (!empty($_SESSION['ui_language'])) {
            $sessionLang = $_SESSION['ui_language'];
            if (isset($supportedLanguages[$sessionLang])) {
                return $sessionLang;
            }
        }

        if (isset($supportedLanguages[$brandLanguage])) {
            return $brandLanguage;
        }

        return array_key_first($supportedLanguages);
    }

    function buildLangArray(array $langText, ?string $language = 'en') {
        $lang = [];

        foreach ($langText as $key => $translations) {
            $lang[$key] = $translations[$language]
                ?? reset($translations);
        }

        return $lang;
    }

    function pp_gateways($tab = '', $data = []){
        global $db_prefix;

        $params = [ ':tab' => $tab, ':brand_id' => $data['brand']['id'] ];

        $response_gateway = json_decode(getData($db_prefix.'gateways','WHERE tab = :tab AND brand_id = :brand_id AND status = "active"','* FROM',$params),true);

        $gatewayList = [];

        if ($response_gateway['status'] === true) {
            $currencyRates = [];

            $currencyRes = json_decode(getData($db_prefix.'currency', ' WHERE brand_id = "'.$data['brand']['id'].'"'), true);

            if (!empty($currencyRes['response'])) {
                foreach ($currencyRes['response'] as $c) {
                    $currencyRates[$c['code']] =$c['rate'];
                }
            }

            $allowedGateways = [];
            if (isset($data['transaction']['source']) && $data['transaction']['source'] == 'payment-link') {
                if (isset($data['transaction']['metadata']['allowed_gateways']) && is_array($data['transaction']['metadata']['allowed_gateways'])) {
                    $allowedGateways = $data['transaction']['metadata']['allowed_gateways'];
                }
            }

            foreach ($response_gateway['response'] as $row) {
                if (!empty($allowedGateways) && !in_array($row['gateway_id'], $allowedGateways)) {
                    continue;
                }

                $txnAmount  = money_sanitize($data['transaction']['amount']);
                $txnCurrency = $data['transaction']['currency'];
                $gatewayCurrency = $row['currency'];

                if ($txnCurrency === $gatewayCurrency ||
                    (($txnCurrency === 'USDT' || $txnCurrency === 'USD') && ($gatewayCurrency === 'USDT' || $gatewayCurrency === 'USD'))
                ) {
                    $convertedAmount = $txnAmount;
                } else {
                    if ($txnCurrency === 'USDT' || $txnCurrency === 'USD') {
                        $usdRate = $currencyRates['USD'] ?? ($currencyRates['USDT'] ?? '1');
                        if (isset($currencyRates[$gatewayCurrency]) && $gatewayCurrency !== 'BDT') {
                            $convertedAmount = money_div(money_mul($txnAmount, $usdRate), $currencyRates[$gatewayCurrency]);
                        } else {
                            $convertedAmount = money_mul($txnAmount, $usdRate);
                        }
                    } else {
                        if (isset($currencyRates[$gatewayCurrency])) {
                            $convertedAmount = money_div($txnAmount, $currencyRates[$gatewayCurrency]);
                        } else {
                            $convertedAmount = "0";
                        }
                    }
                }

                $fixed_discount = money_sanitize( $response_gateway['response'][0]['fixed_discount']);
                $percentage_discount = money_sanitize($response_gateway['response'][0]['percentage_discount']);

                $fixed_charge = money_sanitize($response_gateway['response'][0]['fixed_charge']);
                $percentage_charge = money_sanitize($response_gateway['response'][0]['percentage_charge']);

                $percentageDiscountAmount = money_div(money_mul($convertedAmount, $percentage_discount, 8), "100", 8);
                $totalDiscount = money_add($fixed_discount, $percentageDiscountAmount, 8);

                $percentageChargeAmount = money_div(money_mul($convertedAmount, $percentage_charge, 8), "100", 8);
                $totalProcessingFee = money_add($fixed_charge, $percentageChargeAmount, 8);

                $convertedAmount = money_add(money_sub($convertedAmount, $totalDiscount, 8), $totalProcessingFee, 8);

                $min = money_sanitize($row['min_allow']);
                $max = money_sanitize($row['max_allow']);

                $hasNoMax = bccomp($max, '0', 2) <= 0 || $max === '' || $max === '--';

                $isAboveMin = bccomp(money_round($convertedAmount), $min, 2) >= 0;
                $isBelowMax = $hasNoMax ? true : (bccomp(money_round($convertedAmount), $max, 2) <= 0);

                if ($isAboveMin && $isBelowMax) {
                    $gatewayList[] = [
                        'gateway_id'           => $row['gateway_id'],
                        'slug'                 => $row['slug'],
                        'name'                 => $row['name'],
                        'display'              => $row['display'],
                        'logo'                 => $row['logo'],
                        'currency'             => $row['currency'],
                        'min_allow'            => money_round($row['min_allow']),
                        'max_allow'            => money_round($row['max_allow']),
                        'fixed_discount'       => money_round($row['fixed_discount']),
                        'percentage_discount'  => money_round($row['percentage_discount']),
                        'fixed_charge'         => money_round($row['fixed_charge']),
                        'percentage_charge'    => money_round($row['percentage_charge']),
                        'primary_color'        => $row['primary_color'],
                        'text_color'           => $row['text_color'],
                        'btn_color'            => $row['btn_color'],
                        'btn_text_color'       => $row['btn_text_color'],
                    ];
                }
            }

            usort($gatewayList, function ($a, $b) {
                $priority = [
                    'bkash' => 1,
                    'nagad' => 2,
                    'rocket' => 3
                ];

                $getPriority = function($slug) use ($priority) {
                    foreach ($priority as $key => $val) {
                        if (strpos(strtolower($slug), $key) !== false) {
                            return $val;
                        }
                    }
                    return 99;
                };

                $pA = $getPriority($a['slug']);
                $pB = $getPriority($b['slug']);

                if ($pA == $pB) {
                    return strcmp($a['name'], $b['name']);
                }
                return $pA - $pB;
            });

            return [
                'status'   => true,
                'gateway'  => $gatewayList
            ];
        }

        return [
            'status'  => false,
            'gateway' => []
        ];
    }

    function pp_gateway_info($gateway_id = '', $data = []){
        global $db_prefix;

        $params = [ ':gateway_id' => $gateway_id, ':brand_id' => $data['brand']['id'] ];

        $response_gateway = json_decode(getData($db_prefix.'gateways','WHERE gateway_id = :gateway_id AND brand_id = :brand_id AND status = "active"','* FROM',$params),true);

        if ($response_gateway['status'] === true) {
            $row = $response_gateway['response'][0];
            $currencyRates = [];

            $currencyRes = json_decode(getData($db_prefix.'currency', ' WHERE brand_id = "'.$data['brand']['id'].'"'), true);

            if (!empty($currencyRes['response'])) {
                foreach ($currencyRes['response'] as $c) {
                    $currencyRates[$c['code']] =$c['rate'];
                }
            }

            $txnAmount  = money_sanitize($data['transaction']['amount']);
            $txnCurrency = $data['transaction']['currency'];
            $gatewayCurrency = $row['currency'];

            if ($txnCurrency === $gatewayCurrency ||
                (($txnCurrency === 'USDT' || $txnCurrency === 'USD') && ($gatewayCurrency === 'USDT' || $gatewayCurrency === 'USD'))
            ) {
                $convertedAmount = $txnAmount;
            } else {
                if ($txnCurrency === 'USDT' || $txnCurrency === 'USD') {
                    $usdRate = $currencyRates['USD'] ?? ($currencyRates['USDT'] ?? '1');
                    if (isset($currencyRates[$gatewayCurrency]) && $gatewayCurrency !== 'BDT') {
                        $convertedAmount = money_div(money_mul($txnAmount, $usdRate), $currencyRates[$gatewayCurrency]);
                    } else {
                        $convertedAmount = money_mul($txnAmount, $usdRate);
                    }
                } else {
                    if (isset($currencyRates[$gatewayCurrency])) {
                        $convertedAmount = money_div($txnAmount, $currencyRates[$gatewayCurrency]);
                    } else {
                        $convertedAmount = $txnAmount;
                    }
                }
            }

            $fixed_discount = money_sanitize( $response_gateway['response'][0]['fixed_discount']);
            $percentage_discount = money_sanitize($response_gateway['response'][0]['percentage_discount']);

            $fixed_charge = money_sanitize($response_gateway['response'][0]['fixed_charge']);
            $percentage_charge = money_sanitize($response_gateway['response'][0]['percentage_charge']);

            $percentageDiscountAmount = money_div(money_mul($convertedAmount, $percentage_discount, 8), "100", 8);
            $totalDiscount = money_add($fixed_discount, $percentageDiscountAmount, 8);

            $percentageChargeAmount = money_div(money_mul($convertedAmount, $percentage_charge, 8), "100", 8);
            $totalProcessingFee = money_add($fixed_charge, $percentageChargeAmount, 8);

            $convertedAmount = money_add(money_sub($convertedAmount, $totalDiscount, 8), $totalProcessingFee, 8);

            $min = money_sanitize($row['min_allow']);
            $max = money_sanitize($row['max_allow']);

            $hasNoMax = bccomp($max, '0', 2) <= 0 || $max === '' || $max === '--';

            $isAboveMin = bccomp(money_round($convertedAmount), $min, 2) >= 0;
            $isBelowMax = $hasNoMax ? true : (bccomp(money_round($convertedAmount), $max, 2) <= 0);

            if ($isAboveMin && $isBelowMax) {
                if(file_exists(__DIR__.'/../pp-modules/pp-gateways/'.$row['slug'].'/class.php')){
                    require_once __DIR__.'/../pp-modules/pp-gateways/'.$row['slug'].'/class.php';

                    $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $row['slug']))) . 'Gateway';

                    $gateway = new $class();

                    $gateway_info = $gateway->info();

                    if (method_exists($gateway, 'supported_languages')) {
                        $supported_languages = $gateway->supported_languages();
                    }else{
                        $supported_languages = [];
                    }
                }else{
                    if($response_gateway['response'][0]['tab'] == 'bank'){
                        $supported_languages = [
                            'en' => 'English',
                            'bn' => 'বাংলা',
                            'hi' => 'हिन्दी',
                            'ur' => 'اردو',
                            'ar' => 'العربية',
                        ];
                    }else{
                        $supported_languages = [];
                    }
                }

                $gatewayList = [
                    'gateway_id'           => $row['gateway_id'],
                    'slug'                 => $row['slug'],
                    'name'                 => $row['name'],
                    'display'              => $row['display'],
                    'logo'                 => $row['logo'],
                    'currency'             => $row['currency'],
                    'min_allow'            => money_round($row['min_allow']),
                    'max_allow'            => money_round($row['max_allow']),
                    'fixed_discount'       => money_round($row['fixed_discount']),
                    'percentage_discount'  => money_round($row['percentage_discount']),
                    'fixed_charge'         => money_round($row['fixed_charge']),
                    'percentage_charge'    => money_round($row['percentage_charge']),
                    'primary_color'        => $row['primary_color'],
                    'text_color'           => $row['text_color'],
                    'btn_color'            => $row['btn_color'],
                    'btn_text_color'       => $row['btn_text_color'],
                    'tab'                  => $response_gateway['response'][0]['tab'],
                ];

                return [
                    'status'   => true,
                    'gateway'  => $gatewayList,
                    'supported_languages'  => $supported_languages
                ];
            }else{
                return [
                    'status'   => false,
                    'gateway'  => []
                ];
            }
        }

        return [
            'status'  => false,
            'gateway' => []
        ];
    }

    function pp_gateway_render($gateway_id = '', $data = []){
        global $db_prefix;

        unset($data['options'], $data['lang']);

        $params = [ ':gateway_id' => $gateway_id, ':brand_id' => $data['brand']['id'] ];

        $response_gateway = json_decode(getData($db_prefix.'gateways','WHERE gateway_id = :gateway_id AND brand_id = :brand_id  AND status = "active"', '* FROM', $params),true);
        if($response_gateway['status'] == true){

            $options = [];

            $params = [ ':gateway_id' => $gateway_id ];
            $response_gateways_parameter = json_decode(getData($db_prefix.'gateways_parameter','WHERE gateway_id = :gateway_id', '* FROM', $params),true);
            foreach($response_gateways_parameter['response'] as $field){
                $value = $field['value'];

                if(!empty($field['multiple']) && !empty($value)){
                    $value = is_array($value) ? $value : json_decode($value, true);
                }

                $options[$field['option_name']] = $value;
            }

            $data['options'] = $options;

            $gatewayInfo = [
                'gateway_id'     => $response_gateway['response'][0]['gateway_id'],
                'slug'     => $response_gateway['response'][0]['slug'],
                'name'     => $response_gateway['response'][0]['name'],
                'display'     => $response_gateway['response'][0]['display'],
                'logo'     => $response_gateway['response'][0]['logo'],
                'currency'     => $response_gateway['response'][0]['currency'],
                'min_allow'     => money_round($response_gateway['response'][0]['min_allow']),
                'max_allow'     => money_round($response_gateway['response'][0]['max_allow']),

                'fixed_discount'     => money_round($response_gateway['response'][0]['fixed_discount']),
                'percentage_discount'     => money_round($response_gateway['response'][0]['percentage_discount']),
                'fixed_charge'     => money_round($response_gateway['response'][0]['fixed_charge']),
                'percentage_charge'     => money_round($response_gateway['response'][0]['percentage_charge']),

                'primary_color'     => $response_gateway['response'][0]['primary_color'],
                'text_color'     => $response_gateway['response'][0]['text_color'],
                'btn_color'     => $response_gateway['response'][0]['btn_color'],
                'btn_text_color'     => $response_gateway['response'][0]['btn_text_color'],
            ];

            $data['gateway'] = $gatewayInfo;

            $currencyRates = [];

            $currencyRes = json_decode(getData($db_prefix.'currency', ' WHERE brand_id = "'.$response_gateway['response'][0]['brand_id'].'"'), true);

            if (!empty($currencyRes['response'])) {
                foreach ($currencyRes['response'] as $c) {
                    $currencyRates[$c['code']] =$c['rate'];
                }
            }

            $txnAmount  = money_sanitize($data['transaction']['amount']);
            $txnCurrency = $data['transaction']['currency'];
            $gatewayCurrency = $response_gateway['response'][0]['currency'];

            if ($txnCurrency === $gatewayCurrency ||
                (($txnCurrency === 'USDT' || $txnCurrency === 'USD') && ($gatewayCurrency === 'USDT' || $gatewayCurrency === 'USD'))
            ) {
                $convertedAmount = $txnAmount;
            } else {
                if ($txnCurrency === 'USDT' || $txnCurrency === 'USD') {
                    $usdRate = $currencyRates['USD'] ?? ($currencyRates['USDT'] ?? '1');
                    if (isset($currencyRates[$gatewayCurrency]) && $gatewayCurrency !== 'BDT') {
                        $convertedAmount = money_div(money_mul($txnAmount, $usdRate), $currencyRates[$gatewayCurrency]);
                    } else {
                        $convertedAmount = money_mul($txnAmount, $usdRate);
                    }
                } else {
                    if (isset($currencyRates[$gatewayCurrency])) {
                        $convertedAmount = money_div($txnAmount, $currencyRates[$gatewayCurrency]);
                    } else {
                        $convertedAmount = $txnAmount;
                    }
                }
            }

            $fixed_discount = money_sanitize($response_gateway['response'][0]['fixed_discount']);
            $percentage_discount = money_sanitize($response_gateway['response'][0]['percentage_discount']);

            $fixed_charge = money_sanitize($response_gateway['response'][0]['fixed_charge']);
            $percentage_charge = money_sanitize($response_gateway['response'][0]['percentage_charge']);

            $percentageDiscountAmount = money_div(money_mul($convertedAmount, $percentage_discount, 8), "100", 8);
            $totalDiscount = money_add($fixed_discount, $percentageDiscountAmount, 8);

            $percentageChargeAmount = money_div(money_mul($convertedAmount, $percentage_charge, 8), "100", 8);
            $totalProcessingFee = money_add($fixed_charge, $percentageChargeAmount, 8);

            $convertedAmount = money_add(money_sub($convertedAmount, $totalDiscount, 8), $totalProcessingFee, 8);

            if ($txnCurrency !== $gatewayCurrency && isset($currencyRates[$gatewayCurrency])) {
                $totalDiscount = money_mul($totalDiscount, $currencyRates[$gatewayCurrency], 8);
                $totalProcessingFee = money_mul($totalProcessingFee, $currencyRates[$gatewayCurrency], 8);
            }

            $data['transaction']['amount'] = money_round($txnAmount, 2);
            $data['transaction']['processing_fee'] = money_round($totalProcessingFee, 2);
            $data['transaction']['discount_amount'] = money_round($totalDiscount, 2);
            $data['transaction']['local_net_amount'] = money_round($convertedAmount, 2);
            $data['transaction']['local_currency'] = $gatewayCurrency;

            if(file_exists(__DIR__.'/../pp-modules/pp-gateways/'.$response_gateway['response'][0]['slug'].'/class.php')){
                require_once __DIR__.'/../pp-modules/pp-gateways/'.$response_gateway['response'][0]['slug'].'/class.php';

                $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $response_gateway['response'][0]['slug']))) . 'Gateway';

                $gateway = new $class();

                $gateway_info = $gateway->info();

                if (method_exists($gateway, 'supported_languages')) {
                    $supported_languages = $gateway->supported_languages();
                }else{
                    $supported_languages = [];
                }

                if (method_exists($gateway, 'lang_text')) {
                     $lang_text = $gateway->lang_text();
                }else{
                    $lang_text = [];
                }
            }else{
                if($response_gateway['response'][0]['tab'] == 'bank'){
                    $gateway = '';

                    $supported_languages = [
                        'en' => 'English',
                        'bn' => 'বাংলা',
                        'hi' => 'हिन्दी',
                        'ur' => 'اردو',
                        'ar' => 'العربية',
                    ];

                    $lang_text = [
                        'bank_step_bank_name' => [
                            'en' => 'Bank Name: {bank_name}',
                            'bn' => 'ব্যাংকের নাম: {bank_name}',
                            'hi' => 'बैंक का नाम: {bank_name}',
                            'ur' => 'بینک کا نام: {bank_name}',
                            'ar' => 'اسم البنك: {bank_name}',
                        ],

                        'bank_step_account_name' => [
                            'en' => 'Account Name: {account_holder_name}',
                            'bn' => 'অ্যাকাউন্টের নাম: {account_holder_name}',
                            'hi' => 'खाते का नाम: {account_holder_name}',
                            'ur' => 'اکاؤنٹ کا نام: {account_holder_name}',
                            'ar' => 'اسم الحساب: {account_holder_name}',
                        ],

                        'bank_step_account_number' => [
                            'en' => 'Account Number: {account_number}',
                            'bn' => 'অ্যাকাউন্ট নম্বর: {account_number}',
                            'hi' => 'खाता संख्या: {account_number}',
                            'ur' => 'اکاؤنٹ نمبر: {account_number}',
                            'ar' => 'رقم الحساب: {account_number}',
                        ],

                        'bank_step_branch_name' => [
                            'en' => 'Branch Name: {branch_name}',
                            'bn' => 'শাখার নাম: {branch_name}',
                            'hi' => 'शाखा का नाम: {branch_name}',
                            'ur' => 'برانچ کا نام: {branch_name}',
                            'ar' => 'اسم الفرع: {branch_name}',
                        ],

                        'bank_step_routing_number' => [
                            'en' => 'Routing Number: {routing_number}',
                            'bn' => 'রাউটিং নম্বর: {routing_number}',
                            'hi' => 'रूटिंग नंबर: {routing_number}',
                            'ur' => 'روٹنگ نمبر: {routing_number}',
                            'ar' => 'رقم التوجيه: {routing_number}',
                        ],

                        'bank_step_swift_code' => [
                            'en' => 'Swift Code: {swift_code}',
                            'bn' => 'সুইফট কোড: {swift_code}',
                            'hi' => 'स्विफ्ट कोड: {swift_code}',
                            'ur' => 'سوئفٹ کوڈ: {swift_code}',
                            'ar' => 'رمز السويفت: {swift_code}',
                        ],

                        'bank_step_amount' => [
                            'en' => 'Amount: {amount} {currency}',
                            'bn' => 'পরিমাণ: {amount} {currency}',
                            'hi' => 'राशि: {amount} {currency}',
                            'ur' => 'رقم: {amount} {currency}',
                            'ar' => 'المبلغ: {amount} {currency}',
                        ],

                        'bank_step_slip' => [
                            'en' => 'Upload the Payment Slip in the box below and press Submit',
                            'bn' => 'নিচের বক্সে পেমেন্ট স্লিপ আপলোড করুন এবং জমা দিন চাপুন।',
                            'hi' => 'नीचे दिए गए बॉक्स में भुगतान रसीद अपलोड करें और "सबमिट" दबाएँ।',
                            'ur' => 'نیچے دیے گئے باکس میں ادائیگی کی رسید اپ لوڈ کریں اور "Submit" دبائیں۔',
                            'ar' => 'قم برفع إيصال الدفع في المربع أدناه ثم اضغط على "إرسال".',
                        ],
                    ];

                    $instructions = [
                        [
                            'icon' => '',
                            'text' => 'bank_step_bank_name',
                            'copy' => true,
                            'value' => $data['options']['bank_name'],
                            'vars' => [
                                '{bank_name}' => $data['options']['bank_name']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_account_name',
                            'copy' => true,
                            'value' => $data['options']['account_holder_name'],
                            'vars' => [
                                '{account_holder_name}' => $data['options']['account_holder_name']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_account_number',
                            'copy' => true,
                            'value' => $data['options']['account_number'],
                            'vars' => [
                                '{account_number}' => $data['options']['account_number']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_branch_name',
                            'copy' => true,
                            'value' => $data['options']['branch_name'],
                            'vars' => [
                                '{branch_name}' => $data['options']['branch_name']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_routing_number',
                            'copy' => true,
                            'value' => $data['options']['routing_number'],
                            'vars' => [
                                '{routing_number}' => $data['options']['routing_number']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_swift_code',
                            'copy' => true,
                            'value' => $data['options']['swift_code'],
                            'vars' => [
                                '{swift_code}' => $data['options']['swift_code']
                            ]
                        ],                        
                        [
                            'icon' => '',
                            'text' => 'bank_step_amount',
                            'copy' => true,
                            'value' => $data['transaction']['local_net_amount'],
                            'vars' => [
                                '{amount}' => number_format((float)$data['transaction']['local_net_amount'], 2),
                                '{currency}' => $data['transaction']['local_currency']
                            ]
                        ],
                        [
                            'icon' => '',
                            'text' => 'bank_step_slip',
                            'copy' => false,
                        ],
                    ];

                    $gateway_info = [
                        'gateway_type'        => 'manual',
                        'verify_by'        => 'slip',
                    ];
                }else{
                    return false;
                }
            }

            $lang_text['verify'] = [
                'en' => 'Verify',
                'bn' => 'যাচাই করুন',
                'hi' => 'सत्यापित करें',
                'ur' => 'تصدیق کریں',
                'ar' => 'تحقق',
            ];
            
            $lang_text['transaction_id'] = [
                'en' => 'Transaction ID',
                'bn' => 'ট্রানজ্যাকশন আইডি',
                'hi' => 'लेन-देन आईडी',
                'ur' => 'لین دین آئی ڈی',
                'ar' => 'معرّف المعاملة',
            ];

            $lang_text['enter_transaction_id'] = [
                'en' => 'Enter transaction ID',
                'bn' => 'ট্রানজ্যাকশন আইডি লিখুন',
                'hi' => 'लेन-देन आईडी दर्ज करें',
                'ur' => 'لین دین آئی ڈی درج کریں',
                'ar' => 'أدخل معرّف المعاملة',
            ];
            
            $lang_text['upload_slip'] = [
                'en' => 'Upload Payment Slip',
                'bn' => 'পেমেন্ট স্লিপ আপলোড করুন',
                'hi' => 'भुगतान स्लिप अपलोड करें',
                'ur' => 'ادائیگی سلپ اپ لوڈ کریں',
                'ar' => 'ارفع إيصال الدفع',
            ];            
            
            $lang_text['mobile_number'] = [
                'en' => 'Mobile Number',
                'bn' => 'মোবাইল নম্বর',
                'hi' => 'मोबाइल नंबर',
                'ur' => 'موبائل نمبر',
                'ar' => 'رقم الجوال',
            ];

            $lang_text['submit'] = [
                'en' => 'Submit',
                'bn' => 'জমা দিন',
                'hi' => 'जमा करें',
                'ur' => 'جمع کریں',
                'ar' => 'إرسال',
            ];
            
            $language = resolveModuleLanguage($data['brand']['locale']['language'],$supported_languages);

            // Build $lang array for developer
            $lang = buildLangArray($lang_text, $language);

            $data['lang']    = $lang; // or whatever new value

            // If you also want to keep discount in sync (optional)
            //$data['transaction']['discount_amount'] = number_format((float)$data['transaction']['discount_amount'],2,'.','');

            if (is_callable([$gateway, 'instructions'])) {
                $instructions = $gateway->instructions($data);
            }

            if ($response_gateway['response'][0]['tab'] == 'mfs') {
                $response_qr_status = json_decode(getData($db_prefix.'gateways_parameter','WHERE gateway_id = :gateway_id AND brand_id = :brand_id AND option_name = "qr_code_status"', '* FROM', [':gateway_id' => $gateway_id, ':brand_id' => $data['brand']['id']]),true);
                $qr_status_val = $response_qr_status['response'][0]['value'] ?? 'enabled';
                if($qr_status_val == 'disabled'){
                    $data['options']['qr_code'] = '';
                }

                $primaryColor = $response_gateway['response'][0]['primary_color'];
                $textColor = $response_gateway['response'][0]['text_color'];
                $slug = $response_gateway['response'][0]['slug'];
                $g_logo = $response_gateway['response'][0]['logo'];
                // Dynamic setup based on slug
                $cartBg = '#fff3e0';
                $cartColor = $primaryColor;
                $step2img = '';
                $step2app = 'আপনার পেমেন্ট';
                $step2appEn = 'Payment';
                $step2action = 'সেন্ড মানি';
                $step3text = 'একাউন্ট নম্বর পেস্ট করুন এবং কাঙ্খিত এমাউন্ট সেন্ড মানি করুন।';
                $nagadBrandedHeader = false;
                $upayBrandedHeader = false;
                
                if ($slug == 'bkash-personal') {
                    $primaryColor = '#e2136e';
                    $step2img = 'assets/images/bkash_personal.jpg';
                    $step2app = 'বিকাশ';
                    $step2appEn = 'bKash';
                    $cartBg = '#f1f5f9';
                    $cartColor = '#64748b';
                    $g_logo = rtrim(pp_site_address(), '/') . '/assets/images/bkash.png';
                } elseif ($slug == 'nagad-personal') {
                    $primaryColor = '#ed1c24';
                    $step2img = 'assets/images/nagad_personal.jpg';
                    $step2app = 'নগদ';
                    $step2appEn = 'Nagad';
                    $cartBg = '#fff0f0';
                    $cartColor = '#ed1c24';
                    $g_logo = rtrim(pp_site_address(), '/') . '/assets/images/nagad.png';
                    $nagadBrandedHeader = true;
                } elseif ($slug == 'rocket-personal') {
                    $primaryColor = '#7b2382';
                    $step2img = 'assets/images/rocket_personal.jpg';
                    $step2app = 'রকেট';
                    $step2appEn = 'Rocket';
                    $cartBg = '#f4e7f7';
                    $cartColor = '#7b2382';
                } elseif ($slug == 'upay-personal' || strpos(strtolower($slug), 'upay') !== false) {
                    $primaryColor = '#FFD302';
                    $step2img = 'assets/images/upay_personal.jpg';
                    $step2app = 'উপায়';
                    $step2appEn = 'Upay';
                    $cartBg = '#fdfbd7';
                    $cartColor = '#FFD302';
                    $upayBrandedHeader = true;
                } elseif ($slug == 'cellfin-personal' || strpos(strtolower($slug), 'cellfin') !== false) {
                    $primaryColor = '#00803d';
                    $step2img = 'assets/images/cellfin_personal.jpg';
                    $step2app = 'সেলফিন';
                    $step2appEn = 'Cellfin';
                    $step2action = 'ফান্ড ট্রান্সফার';
                    $step3text = 'একাউন্ট নম্বর পেস্ট করুন এবং কাঙ্খিত এমাউন্ট ফান্ড ট্রান্সফার করুন।';
                    $cartBg = '#e6f3eb';
                    $cartColor = '#00803d';
                }
                
                $amount = money_round($data['transaction']['local_net_amount'], 2).' '.$data['transaction']['local_currency'];
                $charge = money_round($data['transaction']['processing_fee'], 2);
                $invoice = $data['transaction']['ref'];
                $personalNumber = $data['options']['mobile_number'] ?? '';
                $mobileLength = ($slug == 'rocket-personal') ? 12 : 11;
                
                $verification_method = $options['verification_method'] ?? 'trxid';

                $cartIconSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.1 9.2a2 2 0 0 0 1.9 1.4h9.2a2 2 0 0 0 1.9-1.4L22 6H7"></path><path d="M9 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path><path d="M20 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path></svg>';
                
                $shopName = htmlspecialchars($data['brand']['name']);
                if (empty($shopName) || strtolower($shopName) === 'default') {
                    $shopName = isset($data['site_name']) ? htmlspecialchars($data['site_name']) : 'Profess0r Shop';
                }
                
                echo '
                <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
                <style>
                /* ===== GLOBAL PAGE WRAPPER ===== */
                html, body {
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    background: #e8e8e8;
                    font-family: "Anek Bangla", "Inter", sans-serif;
                    -webkit-font-smoothing: antialiased;
                }
                .zini-page-wrapper {
                    min-height: 100vh;
                    background: #e8e8e8;
                    display: flex;
                    align-items: flex-start;
                    justify-content: center;
                    padding: 24px 16px;
                    box-sizing: border-box;
                }
                @media (max-width: 520px) {
                    .zini-page-wrapper {
                        padding: 16px 12px;
                        align-items: flex-start;
                    }
                }

                /* ===== CARD ===== */
                .zini-gateway-card {
                    background: #fff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
                    width: 100%;
                    max-width: 600px;
                    font-family: "Anek Bangla", "Inter", sans-serif;
                    display: flex;
                    flex-direction: column;
                }
                @media (max-width: 520px) {
                    .zini-gateway-card {
                        border-radius: 12px;
                    }
                }

                /* ===== CARD TOP (Header + Merchant) — never scrolls ===== */
                .zini-card-top {
                    flex-shrink: 0;
                }

                /* ===== STEP CONTAINERS ===== */
                #zini-step1, #zini-step2, #zini-step-success {
                    display: flex;
                    flex-direction: column;
                    flex: 1;
                }

                /* ===== PINK BODY — scrollable area ===== */
                .zini-pink-body {
                    background-color: '.$primaryColor.';
                    color: '.$textColor.';
                    position: relative;
                    padding: 24px 20px 28px;
                    flex: 1;
                }
                .zini-pink-body::-webkit-scrollbar { width: 4px; }
                .zini-pink-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.35); border-radius: 4px; }

                /* Step 1 pink body centered layout */
                .zini-step1-body {
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 72px 30px 118px;
                    min-height: 200px;
                }
                @media (max-width: 520px) {
                    .zini-step1-body {
                        padding: 40px 20px 80px;
                    }
                }

                /* ===== WHITE FOOTER — always visible, never scrolls ===== */
                .zini-white-footer {
                    background: #fff;
                    padding: 14px 20px 16px;
                    flex-shrink: 0;
                    border-top: 1px solid #f1f5f9;
                }

                /* ===== HEADER ===== */
                .zini-white-header {
                    padding: 16px 24px 12px;
                    background: #fff;
                }
                .zini-gateway-logo { text-align: center; }
                .zini-gateway-logo img { height: 54px; object-fit: contain; }

                /* ===== MERCHANT SECTION ===== */
                .zini-hr { display: block; width: 100%; height: 1px; background: #e5e7eb; }
                .zini-merchant-section {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 14px 24px 16px;
                    background: #fff;
                }
                .zini-shop-block { display: flex; align-items: center; gap: 12px; }
                .zini-shop-icon { width: 42px; height: 42px; border-radius: 50%; background: '.$cartBg.'; color: '.$cartColor.'; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .zini-shop-name { font-size: 15px; font-weight: 600; color: #1e293b; line-height: 1.2; }
                .zini-shop-inv { font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 4px; margin-top: 3px; }
                .zini-shop-inv span { display: inline-block; vertical-align: middle; word-break: break-all; max-width: 250px; }
                .zini-total-amount { font-size: 22px; font-weight: 600; color: #0f172a; white-space: nowrap; }

                /* Desktop Sizes for Logo & Merchant Section */
                @media (min-width: 521px) {
                    .zini-gateway-logo img { height: 84px; }
                    .zini-merchant-section { padding: 16px 32px 18px; }
                    .zini-shop-icon { width: 52px; height: 52px; }
                    .zini-shop-icon svg { width: 24px; height: 24px; }
                    .zini-shop-name { font-size: 18px; }
                    .zini-shop-inv { font-size: 13px; margin-top: 5px; }
                    .zini-total-amount { font-size: 28px; }
                }

                /* ===== NUMBER BOX ===== */
                .zini-number-box { border: 1.5px solid rgba(255,255,255,0.4); background: rgba(0,0,0,0.12); border-radius: 10px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
                .zini-number-box .title { font-size: 10px; text-transform: uppercase; margin-bottom: 4px; opacity: 0.85; letter-spacing: 1px; }
                .zini-number-box .number { font-size: 26px; font-weight: 700; letter-spacing: -0.5px; line-height: 1; }
                .zini-copy-btn { background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; border: none; }
                .zini-copy-btn:hover { background: rgba(255,255,255,0.35); }

                /* ===== STEPS ===== */
                .zini-steps { margin-bottom: 16px; }
                .zini-step { display: flex; gap: 10px; margin-bottom: 14px; align-items: flex-start; }
                .zini-step .circle { width: 24px; height: 24px; border-radius: 50%; background: #fff; color: '.$primaryColor.'; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
                .zini-step .text { font-size: 15.5px; font-weight: 500; line-height: 1.45; flex-grow: 1; font-family: "Anek Bangla", sans-serif; letter-spacing: -0.2px; }
                .zini-step img { width: 80%; max-width: 280px; object-fit: contain; margin: 10px auto 0; display: block; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transform: translateX(-17px); }

                /* ===== WAITING / TIMER ===== */
                .zini-waiting { text-align: center; margin-top: 18px; margin-bottom: 10px; }
                .zini-waiting-text { display: flex; justify-content: center; align-items: center; gap: 8px; font-size: 14.5px; font-weight: 500; margin-bottom: 8px; color: #fff; font-family: "Anek Bangla", sans-serif; }
                .zini-session { font-size: 13.5px; font-weight: 600; color: rgba(255,255,255,0.95); letter-spacing: 0.2px; }

                /* ===== BUTTONS ===== */
                .zini-form-actions { display: flex; gap: 10px; }
                .zini-form-btn { flex: 1; height: 46px; border: none; font-weight: 600; font-size: 14px; border-radius: 8px; cursor: pointer; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 6px; font-family: "Anek Bangla", "Inter", sans-serif; letter-spacing: 0.2px; }
                .zini-cancel-btn { background: #f1f5f9; color: #475569; }
                .zini-cancel-btn:hover { background: #e2e8f0; color: #1e293b; }
                .zini-verify-btn { background: #E0E0E0; color: #999; pointer-events: none; }
                .zini-verify-btn.active-btn { background: '.$primaryColor.'; color: '.$textColor.'; pointer-events: auto; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
                .zini-verify-btn.active-btn:hover { opacity: 0.92; }

                /* ===== STEP 1 INPUT ===== */
                .zini-step1-title {
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 16px;
                    color: #ffffff;
                    font-family: "Anek Bangla", sans-serif;
                }
                .zini-step1-input {
                    background: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 0 16px;
                    width: 100%;
                    max-width: 100%;
                    height: 56px;
                    text-align: center;
                    font-size: 20px;
                    font-weight: 600;
                    color: #1f2937;
                    margin: 0 0 14px 0;
                    display: block;
                    outline: none;
                    font-family: "Anek Bangla", "Inter", sans-serif;
                    letter-spacing: 1px;
                    box-sizing: border-box;
                }
                .zini-step1-input::placeholder { color: #9ca3af; letter-spacing: normal; font-weight: 600; font-size: 18px; }
                .zini-step1-terms {
                    font-size: 14px;
                    color: rgba(255, 255, 255, 0.9);
                    font-weight: 400;
                    font-family: "Anek Bangla", sans-serif;
                }
                .zini-step1-terms a { color: #fff; text-decoration: underline; font-weight: 600; }

                /* ===== MISC ===== */
                .zini-form input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; background: #f9f9f9; color: #333; border-radius: 8px; outline: none; text-align: center; font-size: 15px; margin-bottom: 12px; font-family: "Anek Bangla", sans-serif; box-sizing: border-box; }
                .zini-form input:focus { border-color: '.$primaryColor.'; background: #fff; }
                .zini-footer-contact { text-align: center; margin-top: 8px; font-size: 13px; color: '.$primaryColor.'; font-weight: 500; display: flex; justify-content: center; align-items: center; gap: 6px; }
                .zini-footer-copyright { text-align: center; margin-top: 2px; margin-bottom: 0; font-size: 11px; color: #aaa; }
                .d-none { display: none !important; }

                /* ===== SUCCESS TOAST ===== */
                #zini-success-toast {
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #e6f4ea;
                    color: #1e8e3e;
                    padding: 12px 20px;
                    border-radius: 8px;
                    font-weight: 500;
                    font-size: 14px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    opacity: 0;
                    transition: opacity 0.3s ease-in-out;
                    white-space: nowrap;
                }
                #zini-success-toast.show { opacity: 1; }

                /* ===== RESPONSIVE TWEAKS ===== */
                @media (max-width: 480px) {
                    .zini-white-header { padding: 14px 16px 10px; }
                    .zini-gateway-logo img { height: 60px; }
                    .zini-merchant-section { padding: 12px 16px 12px; }
                    .zini-shop-name { font-size: 13px; }
                    .zini-shop-inv { font-size: 10px; }
                    .zini-shop-inv span { max-width: calc(100vw - 200px); }
                    .zini-total-amount { font-size: 19px; }
                    .zini-shop-icon { width: 36px; height: 36px; }
                    .zini-pink-body { padding: 20px 16px 22px; }
                    .zini-step1-body { padding: 32px 16px 28px; }
                    .zini-step1-title { font-size: 15px; }
                    .zini-step1-input { height: 50px; font-size: 18px; }
                    .zini-step1-terms { font-size: 11px; }
                    .zini-number-box .number { font-size: 22px; }
                    .zini-form-btn { height: 44px; font-size: 13px; }
                }
                </style>
                <div class="zini-page-wrapper">
                    <div class="zini-gateway-card">
                        <div class="zini-card-top">
                    ';
                
                if ($nagadBrandedHeader) {
                    $netAmount = (floor((float)money_round($data['transaction']['local_net_amount'], 2)) == (float)money_round($data['transaction']['local_net_amount'], 2)) ? number_format((float)money_round($data['transaction']['local_net_amount'], 2), 0, '.', '') : money_round($data['transaction']['local_net_amount'], 2);
                    $rawAmt = money_round($data['transaction']['local_net_amount'], 0);
                    $currency = $data['transaction']['local_currency'];
                    $charge   = money_round($data['transaction']['processing_fee'], 2);
                    $invoiceShort = strlen($invoice) > 26 ? substr($invoice, 0, 26) . '...' : $invoice;
                    
                    // Inject CSS overrides for the entire card
                    echo '
                    <style>
                    /* Nagad Whole Card Styling */
                    .zini-gateway-card {
                        background: radial-gradient(circle at center, #ff1d23 0%, #d1171e 25%, #b31619 52%, #961A02 78%, #801800 100%) !important;
                        border: none !important;
                        color: #ffffff !important;
                        max-width: 400px !important;
                        margin: 0 auto !important;
                    }
                    /* Make body and footer transparent */
                    .zini-pink-body, .zini-white-footer {
                        background: transparent !important;
                        border: none !important;
                        box-shadow: none !important;
                    }
                    /* Top section resets */
                    .zini-card-top {
                        padding: 20px 28px 10px !important;
                        position: relative;
                        overflow: hidden;
                        text-align: center;
                        background: transparent !important;
                    }
                    /* Typography and Element Colors */
                    .zini-step1-title, .zini-number-box .title, .zini-steps .text, .zini-waiting-text, .zini-session {
                        color: #ffffff !important;
                    }
                    .zini-step1-terms {
                        color: rgba(255,255,255,0.8) !important;
                    }
                    .zini-step1-terms a {
                        color: #ffffff !important;
                        text-decoration: none !important;
                        font-weight: 600;
                    }
                    /* Input Fields */
                    .zini-step1-input, #default-trxid-input, #trxid-input {
                        background: #ffffff !important;
                        color: #ab171c !important;
                        border: none !important;
                        border-radius: 4px !important;
                        text-align: center;
                        font-weight: 600;
                        height: 44px;
                        width: 100% !important;
                        box-sizing: border-box !important;
                    }
                    /* Number Box */
                    .zini-number-box {
                        background: rgba(255,255,255,0.1) !important;
                        border: 1px solid rgba(255,255,255,0.2) !important;
                    }
                    .zini-number-box .number {
                        color: #ffffff !important;
                    }
                    .zini-number-box .zini-copy-btn {
                        background: #ed1c24 !important;
                        border: 2px solid rgba(255,255,255,0.3) !important;
                        color: #ffffff !important;
                    }
                    /* Steps Circles */
                    .zini-steps .circle {
                        background: #ffffff !important;
                        color: #ed1c24 !important;
                    }
                    /* Buttons */
                    .zini-form-btn.zini-cancel-btn {
                        background: transparent !important;
                        color: #ffffff !important;
                        border: 1px solid #ffffff !important;
                        opacity: 1 !important;
                    }
                    .zini-form-btn.zini-verify-btn, #step1-confirm-btn {
                        background: #ffffff !important;
                        color: #ab171c !important;
                        border: none !important;
                        opacity: 1 !important;
                    }
                    </style>
                    ';

                    echo '
                    <!-- Nagad Branded Header Content -->
                    <div style="position: relative; text-align: center; margin-top: 10px;">
                        <!-- Subtle bg orbs -->
                        <div style="position:absolute;top:-50px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.05);pointer-events:none;"></div>
                        <div style="position:absolute;bottom:-30px;left:-40px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none;"></div>

                        <!-- Cart icon (nagad_cart) -->
                        <div style="margin-bottom: 10px; position: relative; display: inline-block;">
                            <img src="'.rtrim(pp_site_address(), '/').'/assets/images/nagad_cart.png" alt="cart"
                                 style="width: 60px; height: 60px; object-fit: contain; filter: brightness(0) invert(1) opacity(0.85);">
                        </div>

                        <!-- Shop Name -->
                        <div style="font-size: 18px; font-weight: 700; color: #ffffff; font-family: \'Inter\', \'Anek Bangla\', sans-serif; letter-spacing: -0.2px; margin-bottom: 14px; line-height: 1.2;">
                            '.$shopName.'
                        </div>

                        <!-- Invoice Row -->
                        <div style="font-size: 12px; color: rgba(255,255,255,0.7); font-family: \'Inter\', sans-serif; margin-bottom: 14px; font-weight: 400;">
                            Invoice No: <span style="font-weight: 600; color: rgba(255,255,255,0.9);">'.$invoiceShort.'</span>
                        </div>

                        <!-- Amount Box -->
                        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.7); border-radius: 7px; padding: 10px 12px; margin-bottom: 0px; box-shadow: 0 6px 16px rgba(0,0,0,0.08); position: relative;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                <div>
                                    <div style="font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.6); margin-bottom: 4px; font-family: \'Inter\', sans-serif; font-weight: 600; text-align: left;">TOTAL AMOUNT:</div>
                                    <div style="font-size: 24px; font-weight: 700; color: #ffffff; font-family: \'Inter\', sans-serif; letter-spacing: -0.5px; line-height: 1; text-align: left;">
                                        '.$currency.' '.$netAmount.'
                                    </div>
                                </div>
                                <button onclick="pp_copy(\''.$rawAmt.'\')" type="button"
                                    style="width: 34px; height: 34px; border-radius: 4px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.16); display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: 0.2s; padding:0;"
                                    onmouseover="this.style.background=\'rgba(255,255,255,0.2)\';" onmouseout="this.style.background=\'rgba(255,255,255,0.1)\';">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="15px" width="15px" style="color: #ffffff;" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Charge row -->
                        <div style="font-size: 12px; color: rgba(255,255,255,0.65); font-family: \'Inter\', sans-serif; margin-top: 10px; text-align: left; font-weight: 400;">
                            Charge: &nbsp;'.$currency.' '.$charge.'
                        </div>
                    </div>
                    ';
                } elseif ($upayBrandedHeader) {
                    $currency = $data['transaction']['local_currency'];
                    $netAmount = (floor((float)money_round($data['transaction']['local_net_amount'], 2)) == (float)money_round($data['transaction']['local_net_amount'], 2)) ? number_format((float)money_round($data['transaction']['local_net_amount'], 2), 0, '.', '') : money_round($data['transaction']['local_net_amount'], 2);
                    
                    echo '
                    <style>
                    /* ===== UPAY PAGE BACKGROUND ===== */
                    body, .zini-page-wrapper {
                        background: linear-gradient(180deg, #fefbd8 0%, #fbeb7a 100%) !important;
                        min-height: 100vh;
                    }
                    /* ===== UPAY CARD ===== */
                    .zini-gateway-card {
                        background: #FFD302 !important;
                        border: none !important;
                        color: #111827 !important;
                        max-width: 400px !important;
                        margin: 0 auto !important;
                        border-radius: 22px !important;
                        overflow: hidden !important;
                    }
                    /* ===== CARD TOP (Yellow section with logo) ===== */
                    .zini-card-top {
                        background: #FFD302 !important;
                        padding: 30px 24px 20px !important;
                        text-align: center !important;
                        position: relative !important;
                    }
                    /* ===== CARD BODY (transparent on yellow) ===== */
                    .zini-pink-body {
                        background: transparent !important;
                        border: none !important;
                        box-shadow: none !important;
                        color: #33475F !important;
                        padding-top: 5px !important;
                    }
                    /* ===== COPY BUTTON (solid blue) ===== */
                    .zini-copy-btn {
                        background: #024ca1 !important;
                        color: #ffffff !important;
                    }
                    .zini-copy-btn:hover {
                        background: #2951a8 !important;
                    }
                    /* ===== QR BUTTON (solid blue) ===== */
                    button[onclick="showQrModal()"] {
                        background: #ffffff !important;
                        border: 1px solid #024ca1 !important;
                        color: #024ca1 !important;
                    }
                    button[onclick="showQrModal()"]:hover {
                        background: #f1f5f9 !important;
                    }
                    /* ===== WAITING & TRXBOX (dark text for yellow bg) ===== */
                    .zini-waiting-text { color: #33475F !important; }
                    .zini-session { color: #33475F !important; font-weight: 700 !important; }
                    #trxid-fallback-btn { background: rgba(51,71,95,0.15) !important; color: #33475F !important; font-weight: 700 !important; }
                    #trxid-fallback-btn:hover { background: rgba(51,71,95,0.25) !important; }
                    #trxid-input { color: #33475F !important; font-weight: 700 !important; }
                    /* ===== WHITE FOOTER ===== */
                    .zini-white-footer {
                        background: #ffffff !important;
                        border-radius: 0 0 22px 22px !important;
                        padding: 20px 24px 24px !important;
                        box-shadow: none !important;
                        border: none !important;
                    }
                    /* ===== INPUT FIELD ===== */
                    .zini-step1-input, #default-trxid-input, #trxid-input {
                        background: #EBEBEB !important;
                        color: #111827 !important;
                        border: none !important;
                        border-radius: 50px !important;
                        text-align: center !important;
                        font-weight: 500 !important;
                        font-size: 15px !important;
                        height: 50px !important;
                        width: 100% !important;
                        box-sizing: border-box !important;
                        outline: none !important;
                    }
                    .zini-step1-input::placeholder { color: rgba(17,24,39,0.45) !important; }
                    /* ===== TITLE ===== */
                    .zini-step1-title {
                        color: #024ca1 !important;
                        font-weight: 700 !important;
                        font-size: 15px !important;
                        margin-bottom: 14px !important;
                        text-align: center !important;
                    }
                    /* ===== TERMS ===== */
                    .zini-step1-terms {
                        color: #33475F !important;
                        font-weight: 600 !important;
                        font-size: 13px !important;
                        text-align: center !important;
                        margin-top: 18px !important;
                    }
                    .zini-step1-terms a { color: #024ca1 !important; font-weight: 700 !important; text-decoration: underline !important; }
                    /* ===== BUTTONS ===== */
                    .zini-form-actions {
                        display: grid !important;
                        grid-template-columns: 1fr 1fr !important;
                        gap: 12px !important;
                        padding: 0 !important;
                    }
                    .zini-form-btn.zini-cancel-btn {
                        background: #ffffff !important;
                        color: #2951a8 !important;
                        border: 1.5px solid #2951a8 !important;
                        border-radius: 50px !important;
                        opacity: 1 !important;
                        font-weight: 700 !important;
                        height: 46px !important;
                        font-size: 15px !important;
                    }
                    /* Confirm btn - INACTIVE (grey by default) */
                    .zini-form-btn.zini-verify-btn, #step1-confirm-btn {
                        background: #c9cdd1 !important;
                        color: #ffffff !important;
                        border: none !important;
                        border-radius: 50px !important;
                        opacity: 1 !important;
                        font-weight: 700 !important;
                        height: 46px !important;
                        font-size: 15px !important;
                        cursor: not-allowed !important;
                        transition: background 0.25s ease, box-shadow 0.25s ease !important;
                    }
                    /* Confirm btn - ACTIVE (blue when 11 digits entered) */
                    #step1-confirm-btn.active-btn, .zini-verify-btn.active-btn, #main-submit-btn.active-btn {
                        background: #024ca1 !important;
                        color: #ffffff !important;
                        cursor: pointer !important;
                        box-shadow: 0 4px 14px rgba(2, 76, 161, 0.4) !important;
                    }
                    #step1-confirm-btn.active-btn:hover, .zini-verify-btn.active-btn:hover, #main-submit-btn.active-btn:hover {
                        background: #023b80 !important;
                    }
                    /* ===== BOLD FONTS (like Zinipay) ===== */
                    .zini-step1-body *, .zini-pink-body * {
                        font-family: "Anek Bangla", "Inter", sans-serif !important;
                    }
                    /* ===== HIDE UNNEEDED ===== */
                    .zini-hr { display: none !important; }
                    .zini-step1-body { padding: 0px 20px 10px !important; }
                    /* ===== INPUT BOLD ===== */
                    #sender_mobile_input {
                        font-weight: 700 !important;
                        font-size: 18px !important;
                        letter-spacing: 1px !important;
                        color: #111827 !important;
                    }
                    </style>

                    <!-- ===== UPAY LOGO SECTION ===== -->
                    <div style="text-align: center; padding: 12px 24px 0px; position: relative; overflow: hidden;">

                        <!-- Simple SVG Logo -->
                        <div style="margin: 0 auto; display: flex; justify-content: center; align-items: center; width: 100%; max-width: 350px;">
                            <img src="'.rtrim(pp_site_address(), '/').'/assets/images/upay_logo.svg" alt="Upay" style="width: 100%; height: auto; object-fit: contain; display: block; border-radius: 20px;">
                        </div>

                        <!-- Info Box (Grey card) -->
                        <div style="background: #EBEBEB; border-radius: 16px; padding: 16px 20px 14px; margin-top: 20px; text-align: center; position: relative; z-index: 5;">
                            <div style="font-size: 14px; font-weight: 900; color: #111827; font-family: \'Inter\', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">
                                '.$shopName.'
                            </div>
                            <div style="font-size: 15px; font-weight: 800; color: #111827; margin-top: 5px; font-family: \'Inter\', sans-serif;">
                                Amount: <strong>BDT '.$netAmount.'</strong>
                            </div>
                        </div>
                    </div>
                    ';
                } else {
                    echo '
                    <div class="zini-white-header">
                        <div class="zini-gateway-logo">
                            <img src="'.$g_logo.'" alt="Gateway Logo">
                        </div>
                    </div>
                    <span class="zini-hr"></span>
                    <div class="zini-merchant-section">
                        <div class="zini-shop-block">
                            <div class="zini-shop-icon">
                                '.$cartIconSVG.'
                            </div>
                            <div>
                                <div class="zini-shop-name" style="font-size: 15px;">'.$shopName.'</div>
                                <div class="zini-shop-inv" style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap; min-width: 0;">
                                    <span style="display: flex; align-items: center; gap: 4px; min-width: 0; flex: 1;">
                                        <span style="white-space: nowrap; flex-shrink: 0;">Inv:</span>
                                        <span style="display: inline-block; min-width: 0; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: bottom;">'.$invoice.'</span>
                                    </span>
                                    <span style="cursor:pointer; color:'.$primaryColor.'; flex-shrink: 0; display: inline-flex; align-items: center; transform: translateY(-1.5px);" onclick="pp_copy(\''.$invoice.'\', \'Invoice Copied!\', this)">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="11px" width="11px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="zini-total-amount">
                            <span style="margin-right: 3px;">৳</span>'.(floor((float)money_round($data['transaction']['local_net_amount'], 2)) == (float)money_round($data['transaction']['local_net_amount'], 2) ? number_format((float)money_round($data['transaction']['local_net_amount'], 2), 0, '.', '') : money_round($data['transaction']['local_net_amount'], 2)).'
                        </div>
                    </div>
                    ';
                }
                
                echo '
                    </div>
                    
                    ';

                if ($verification_method === 'number_amount') {
                    // NUMBER & AMOUNT UI
                    echo '
                    <div id="zini-step1">
                        <div class="zini-pink-body zini-step1-body">
                            '.($upayBrandedHeader ? '
                            <div class="zini-step1-title" style="color: #33475F !important; font-weight: 800 !important; font-size: 15px !important; margin-bottom: 12px;">Enter Your upay Account Number</div>
                            ' : '
                            <div class="zini-step1-title">Your '.$step2appEn.' Account Number</div>
                            ').'
                            <input type="text" id="sender_mobile_input" class="zini-step1-input" placeholder="e.g 01XXXXXXXXX" autocomplete="off" maxlength="'.$mobileLength.'">
                            '.($upayBrandedHeader ? '
                            <div class="zini-step1-terms" style="margin-top: 24px; font-size: 15px; line-height: 1.4; font-weight: 800; color: #33475F;">By clicking on Confirm, you are agreeing<br>to the <a href="#" style="color: #024ca1; text-decoration: underline;">terms & conditions</a></div>
                            ' : '
                            <div class="zini-step1-terms" style="margin-top: 12px; font-size: 12.5px; line-height: 1.4; font-weight: 700;">By clicking/tapping "Proceed" you are agreeing to our <a href="#">Terms and Conditions</a></div>
                            ').'
                        </div>
                        <div class="zini-white-footer">
                            <div class="zini-form-actions">
                                <button class="zini-form-btn zini-cancel-btn" type="button" onclick="showCancelModal(true)">Close</button>
                                <button class="zini-form-btn zini-verify-btn" type="button" id="step1-confirm-btn">'.($upayBrandedHeader ? 'Confirm' : 'Proceed').'</button>
                            </div>
                            '.($nagadBrandedHeader ? '
                            <div style="text-align:center; margin-top: 20px;">
                                <img src="'.rtrim(pp_site_address(), '/').'/assets/images/nagad.png" alt="নগদ" style="width: 100px; height: auto; object-fit: contain; opacity: 0.85; filter: brightness(0) invert(1);">
                            </div>
                            ' : '').'
                            '.($upayBrandedHeader ? '
                            <div style="text-align:center; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 6px; color: #024ca1; font-weight: 700; font-family: \'Inter\', sans-serif;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                16268
                            </div>
                            ' : '').'
                        </div>
                    </div>

                    <div id="zini-step2" class="d-none">
                        <div class="zini-pink-body">
                            <div class="zini-number-box">
                                <div>
                                    <div class="title">PERSONAL NUMBER</div>
                                    <div class="number">'.$personalNumber.'</div>
                                </div>
                                <div class="zini-copy-btn" onclick="pp_copy(\''.$personalNumber.'\', \'Account Number Copied!\')">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="18px" width="18px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                                </div>
                            </div>
                            
                            '.(!empty($data['options']['qr_code']) ? '
                            <div style="text-align: center; margin-bottom: 20px;">
                                <button onclick="showQrModal()" type="button" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4); color: #fff; padding: 12px 24px; border-radius: 50px; cursor: pointer; font-family: \'Inter\', sans-serif; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.background=\'rgba(255,255,255,0.35)\'; this.style.transform=\'translateY(-2px)\';" onmouseout="this.style.background=\'rgba(255,255,255,0.25)\'; this.style.transform=\'translateY(0)\';">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 7l6 0" /><path d="M17 4l0 6" /></svg>
                                    Scan QR Code
                                </button>
                            </div>
                            ' : '').'
                            
                            <div class="zini-steps">
                                <div class="zini-step">
                                    <div class="circle">1</div>
                                    <div class="text">উপরের পার্সোনাল অ্যাকাউন্ট নম্বর কপি করুন</div>
                                </div>
                                <div class="zini-step">
                                    <div class="circle">2</div>
                                    <div class="text">'.$step2app.' অ্যাপ এ যান তারপর \''.$step2action.'\' নির্বাচন করুন
                                        '.($step2img ? '<img src="'.rtrim(pp_site_address(), '/').'/'.$step2img.'" alt="Demo">' : '').'
                                    </div>
                                </div>
                                <div class="zini-step">
                                    <div class="circle">3</div>
                                    <div class="text">'.$step3text.'</div>
                                </div>
                            </div>
                            
                            <div class="zini-waiting">
                                <div class="zini-waiting-text" id="waiting-text-container">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Waiting for payment...
                                </div>
                                <div class="zini-session">
                                    Session expires in <span id="countdown">10:00</span>
                                    <span id="trxid-fallback-btn" class="d-none" style="margin-left:10px; cursor:pointer; background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:4px; transition:0.2s;">Add Transaction ID</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="zini-white-footer">
                            <form id="auto-verify-form" class="payment-form-submit zini-form" method="POST" enctype="multipart/form-data" style="margin:0; padding:0; background:transparent; border-radius:0;">
                                <input type="hidden" name="action-v2" id="action-v2-field" value="transaction-verify-auto">
                                <input type="hidden" name="gateway-id" value="'.$data['gateway']['gateway_id'].'">
                                <input type="hidden" name="transaction-id" value="'.$data['transaction']['ref'].'">
                                <input type="hidden" name="mobile_number" id="hidden_mobile_number" value="">
                                
                                <div id="trxid-input-container" class="d-none">
                                    <input type="text" name="trxid" id="trxid-input" placeholder="Enter TrxID" autocomplete="off">
                                </div>

                                <div class="zini-form-actions">
                                    <button class="zini-form-btn zini-cancel-btn" type="button" onclick="showCancelModal(true)">Close</button>
                                    <button class="zini-form-btn zini-verify-btn active-btn" type="submit" id="main-submit-btn">I\'ve Paid - Check Status</button>
                                </div>
                            </form>
                            '.($nagadBrandedHeader ? '
                            <div style="text-align:center; margin-top: 20px;">
                                <img src="'.rtrim(pp_site_address(), '/').'/assets/images/nagad.png" alt="নগদ" style="width: 100px; height: auto; object-fit: contain; opacity: 0.85; filter: brightness(0) invert(1);">
                            </div>
                            ' : '').'
                        </div>
                    </div>
                    ';
                } else {
                    // DEFAULT TRXID UI
                    echo '
                    <div id="zini-step-default">
                    <div class="zini-pink-body">
                        <div class="zini-number-box">
                            <div>
                                <div class="title">PERSONAL NUMBER</div>
                                <div class="number">'.$personalNumber.'</div>
                            </div>
                            <div class="zini-copy-btn" onclick="pp_copy(\''.$personalNumber.'\', \'Account Number Copied!\')">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="18px" width="18px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                            </div>
                        </div>
                        
                        '.(!empty($data['options']['qr_code']) ? '
                        <div style="text-align: center; margin-bottom: 20px;">
                            <button onclick="showQrModal()" type="button" style="background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4); color: #fff; padding: 12px 24px; border-radius: 50px; cursor: pointer; font-family: \'Inter\', sans-serif; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onmouseover="this.style.background=\'rgba(255,255,255,0.35)\'; this.style.transform=\'translateY(-2px)\';" onmouseout="this.style.background=\'rgba(255,255,255,0.25)\'; this.style.transform=\'translateY(0)\';">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 7l6 0" /><path d="M17 4l0 6" /></svg>
                                Scan QR Code
                            </button>
                        </div>
                        ' : '').'
                        
                        <div class="zini-steps">
                            <div class="zini-step">
                                <div class="circle">1</div>
                                <div class="text">উপরের পার্সোনাল অ্যাকাউন্ট নম্বর কপি করুন</div>
                            </div>
                            <div class="zini-step">
                                <div class="circle">2</div>
                                <div class="text">'.$step2app.' অ্যাপ এ যান তারপর \''.$step2action.'\' নির্বাচন করুন
                                    '.($step2img ? '<img src="'.rtrim(pp_site_address(), '/').'/'.$step2img.'" alt="Demo">' : '').'
                                </div>
                            </div>
                            <div class="zini-step">
                                <div class="circle">3</div>
                                <div class="text">'.$step3text.'</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="zini-white-footer">
                        <form class="payment-form-submit zini-form" method="POST" enctype="multipart/form-data" style="margin:0; padding:0; background:transparent; border-radius:0;">
                            <input type="hidden" name="action-v2" value="transaction-verify">
                            <input type="hidden" name="gateway-id" value="'.$data['gateway']['gateway_id'].'">
                            <input type="hidden" name="transaction-id" value="'.$data['transaction']['ref'].'">
                            
                            <input type="text" name="trxid" id="default-trxid-input" placeholder="Enter TrxID" required autocomplete="off">
                            
                            <div class="zini-form-actions">
                                <button class="zini-form-btn zini-cancel-btn" type="button" onclick="showCancelModal(true)">Close</button>
                                <button class="zini-form-btn zini-verify-btn" type="submit" id="default-verify-btn" disabled>Verify</button>
                            </div>
                        </form>
                        '.($nagadBrandedHeader ? '
                        <div style="text-align:center; margin-top: 20px;">
                            <img src="'.rtrim(pp_site_address(), '/').'/assets/images/nagad.png" alt="নগদ" style="width: 100px; height: auto; object-fit: contain; opacity: 0.85; filter: brightness(0) invert(1);">
                        </div>
                        ' : '').'
                        '.($upayBrandedHeader ? '
                        <div style="text-align:center; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 6px; color: #024ca1; font-weight: 700; font-family: \'Inter\', sans-serif;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            16268
                        </div>
                        ' : '').'
                    </div>
                    </div>
                    ';
                }

                // SUCCESS SCREEN (Hidden by default)
                echo '
                <style>
                    @keyframes ziniPopIn {
                        0% { transform: scale(0.5); opacity: 0; }
                        70% { transform: scale(1.1); opacity: 1; }
                        100% { transform: scale(1); opacity: 1; }
                    }
                    @keyframes pulseRing {
                        0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
                        70% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
                        100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
                    }
                </style>
                <div id="zini-step-success" class="d-none">
                    <div class="zini-pink-body" style="text-align: center; padding-top: 40px; padding-bottom: 40px; min-height: 480px; display: flex; flex-direction: column; justify-content: center;">
                        <div style="background: #fff; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; animation: ziniPopIn 0.5s ease-out forwards, pulseRing 2s infinite 0.5s;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="'.$primaryColor.'" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        </div>
                        <h2 style="color: #fff; font-weight: 700; margin-bottom: 10px; font-size: 26px;">Payment Successful</h2>
                        <p style="color: rgba(255,255,255,0.95); font-size: 15px; margin-bottom: 30px;">Your payment of <strong>৳'.money_round($data['transaction']['local_net_amount'], 2).'</strong> has been confirmed.</p>
                        
                        <div style="background: rgba(0,0,0,0.1); border-radius: 8px; padding: 25px 20px; margin-bottom: 30px;">
                            <div style="color: #fff; font-size: 14px; margin-bottom: 15px;">Redirecting to merchant website in</div>
                            <div id="success-timer-val" style="color: #fff; font-size: 36px; font-weight: 700; margin-bottom: 15px;">8</div>
                            <div style="color: rgba(255,255,255,0.7); font-size: 12px; letter-spacing: 2px;">SECONDS</div>
                        </div>
                        
                        <div style="color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 20px;">Please do not close this page.</div>
                        <a href="javascript:void(0)" onclick="downloadReceiptImage()" style="display: inline-block; padding: 12px 24px; background: rgba(255,255,255,0.2); color: #fff; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 14px; transition: 0.3s; margin: 0 auto; border: 1px solid rgba(255,255,255,0.3);">Download Receipt</a>
                    </div>
                </div>';
                
                if (!empty($data['options']['qr_code'])) {
                    echo '
                    <div id="zini-qr-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
                        <div style="background:#fff; border-radius:12px; width:90%; max-width:350px; padding:20px; text-align:center; position:relative; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                            <div onclick="closeQrModal()" style="position:absolute; top:-12px; right:-12px; width:32px; height:32px; background:#e63946; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-weight:bold; font-size:20px; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
                                &times;
                            </div>
                            <h3 style="margin:0 0 15px; font-family:\'Inter\', sans-serif; font-size:18px; color:#333; font-weight:600;">Scan QR Code</h3>
                            <img src="'.$data['options']['qr_code'].'" alt="QR Code" style="width:100%; max-width:300px; border-radius:8px; display:block; margin:0 auto;">
                        </div>
                    </div>
                    ';
                }

                echo '</div></div>'; // End zini-gateway-card + zini-page-wrapper
                
                if ($nagadBrandedHeader) {
                    echo '
                    <style>
                    .zini-nagad-footer-logo { text-align: center; padding: 14px 0 10px; }
                    .zini-nagad-footer-logo img { height: 36px; object-fit: contain; opacity: 0.85; }
                    </style>
                    ';
                }
                
                global $site_url, $path_payment_link;
                
                $real_return_url = "";
                if (isset($data["transaction"]["raw_return_url"]) && $data["transaction"]["raw_return_url"] !== "--" && !empty($data["transaction"]["raw_return_url"])) {
                    $real_return_url = $data["transaction"]["raw_return_url"];
                } elseif (isset($data["transaction"]["return_url"]) && $data["transaction"]["return_url"] !== "--" && !empty($data["transaction"]["return_url"])) {
                    $real_return_url = $data["transaction"]["return_url"];
                } elseif (isset($data["brand"]["redirect_url"]) && $data["brand"]["redirect_url"] !== "--" && !empty($data["brand"]["redirect_url"])) {
                    $real_return_url = $data["brand"]["redirect_url"];
                }
                
                $final_return_url = !empty($real_return_url) ? $real_return_url : pp_checkout_address();
                
                $check_r = rtrim($final_return_url, '/');
                $check_s = rtrim($site_url, '/');
                if(empty($real_return_url) || $check_r == $check_s) {
                    if($data['transaction']['source'] == 'payment-link') {
                        $pID = '';
                        if(!empty($data['transaction']['metadata'])) {
                            $meta = json_decode($data['transaction']['metadata'], true);
                            $pID = $meta['paymentLink_id'] ?? '';
                        }
                        if($pID) $final_return_url = $site_url.$path_payment_link.'/'.$pID;
                    } elseif($data['transaction']['source'] == 'payment-link-default') {
                        $final_return_url = $site_url.$path_payment_link.'/default/'.$data['transaction']['brand_id'];
                    } elseif($data['transaction']['source'] == 'invoice') {
                        $final_return_url = $site_url.'invoice/'.$data['transaction']['ref'];
                    }
                }

                // Add the JS logic
                echo '
                <script data-cfasync="false">
                    function showQrModal() {
                        document.getElementById("zini-qr-modal").style.display = "flex";
                    }

                    function closeQrModal() {
                        document.getElementById("zini-qr-modal").style.display = "none";
                    }

                    window.downloadReceiptImage = function() {
                        const doCapture = () => {
                            const el = document.getElementById("zini-step-success");
                            const btn = el.querySelector("a[onclick=\'downloadReceiptImage()\']");
                            if (btn) btn.style.display = "none";
                            
                            html2canvas(el, {
                                backgroundColor: "'.($primaryColor ?? '#e63946').'",
                                useCORS: true,
                                scale: 2
                            }).then(canvas => {
                                if (btn) btn.style.display = "inline-block";
                                let link = document.createElement("a");
                                link.download = "receipt-'.$data['transaction']['ref'].'.png";
                                link.href = canvas.toDataURL("image/png");
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }).catch(err => {
                                if (btn) btn.style.display = "inline-block";
                                console.error("Receipt error:", err);
                                alert("Failed to generate receipt. Please try again.");
                            });
                        };

                        if (typeof html2canvas === "undefined") {
                            const script = document.createElement("script");
                            script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
                            script.onload = doCapture;
                            script.onerror = () => alert("Failed to load receipt generator. Please try again.");
                            document.head.appendChild(script);
                        } else {
                            doCapture();
                        }
                    };


                    document.addEventListener("DOMContentLoaded", function() {
                        const step1 = document.getElementById("zini-step1");
                        const step2 = document.getElementById("zini-step2");
                        const step1ConfirmBtn = document.getElementById("step1-confirm-btn");
                        const senderMobileInput = document.getElementById("sender_mobile_input");
                        const hiddenMobileNumber = document.getElementById("hidden_mobile_number");
                        
                        const countdownEl = document.getElementById("countdown");
                        const trxidFallbackBtn = document.getElementById("trxid-fallback-btn");
                        const trxidInputContainer = document.getElementById("trxid-input-container");
                        const trxidInput = document.getElementById("trxid-input");
                        const actionV2Field = document.getElementById("action-v2-field");
                        const waitingTextContainer = document.getElementById("waiting-text-container");
                        const mainSubmitBtn = document.getElementById("main-submit-btn");
                        
                        let pollInterval;
                        let isPolling = false;
                        let timerInterval;

                        // Step 1 to Step 2 Transition
                        if(senderMobileInput && step1ConfirmBtn) {
                            senderMobileInput.addEventListener("input", function() {
                                // Strip non-numeric characters
                                this.value = this.value.replace(/[^0-9]/g, "");
                                
                                if(this.value.length === '.$mobileLength.') {
                                    step1ConfirmBtn.classList.add("active-btn");
                                } else {
                                    step1ConfirmBtn.classList.remove("active-btn");
                                }
                            });
                        }

                        if(step1ConfirmBtn) {
                            step1ConfirmBtn.addEventListener("click", function() {
                                const mobile = senderMobileInput.value.trim();
                                if(mobile.length < '.$mobileLength.') {
                                    return;
                                }
                                hiddenMobileNumber.value = mobile;
                                step1.classList.add("d-none");
                                step2.classList.remove("d-none");
                                
                                startCountdown(10 * 60); // 10 minutes
                                startPolling();
                            });
                        }

                        function startCountdown(duration) {
                            let timer = duration, minutes, seconds;
                            timerInterval = setInterval(function () {
                                minutes = parseInt(timer / 60, 10);
                                seconds = parseInt(timer % 60, 10);

                                minutes = minutes < 10 ? "0" + minutes : minutes;
                                seconds = seconds < 10 ? "0" + seconds : seconds;

                                if(countdownEl) countdownEl.textContent = minutes + ":" + seconds;

                                // Show fallback TrxID button after 30 seconds
                                if (timer === 9 * 60 + 30) {
                                    if(trxidFallbackBtn) trxidFallbackBtn.classList.remove("d-none");
                                }

                                if (--timer < 0) {
                                    clearInterval(timerInterval);
                                    stopPolling();
                                    // Redirect to session expired or show error
                                    window.location.href = "'.pp_checkout_address().'"; 
                                }
                            }, 1000);
                        }

                        if(trxidFallbackBtn) {
                            trxidFallbackBtn.addEventListener("click", function() {
                                stopPolling();
                                trxidFallbackBtn.classList.add("d-none");
                                waitingTextContainer.innerHTML = "Enter Transaction ID below";
                                trxidInputContainer.classList.remove("d-none");
                                trxidInput.setAttribute("required", "required");
                                actionV2Field.removeAttribute("disabled");
                                actionV2Field.value = "transaction-verify"; // Switch to manual verify
                                
                                if(mainSubmitBtn) {
                                    mainSubmitBtn.innerHTML = "Verify";
                                    mainSubmitBtn.classList.remove("active-btn");
                                    mainSubmitBtn.setAttribute("disabled", "disabled");
                                }
                            });
                        }
                        
                        if(trxidInput && mainSubmitBtn) {
                            trxidInput.addEventListener("input", function() {
                                if(this.value.trim().length > 3) {
                                    mainSubmitBtn.classList.add("active-btn");
                                    mainSubmitBtn.removeAttribute("disabled");
                                } else {
                                    mainSubmitBtn.classList.remove("active-btn");
                                    mainSubmitBtn.setAttribute("disabled", "disabled");
                                }
                            });
                        }
                        
                        const defaultTrxidInput = document.getElementById("default-trxid-input");
                        const defaultVerifyBtn = document.getElementById("default-verify-btn");
                        if(defaultTrxidInput && defaultVerifyBtn) {
                            defaultTrxidInput.addEventListener("input", function() {
                                if(this.value.trim().length > 3) {
                                    defaultVerifyBtn.classList.add("active-btn");
                                    defaultVerifyBtn.removeAttribute("disabled");
                                } else {
                                    defaultVerifyBtn.classList.remove("active-btn");
                                    defaultVerifyBtn.setAttribute("disabled", "disabled");
                                }
                            });
                        }

                        function stopPolling() {
                            isPolling = false;
                            if(pollInterval) clearInterval(pollInterval);
                        }

                        function startPolling() {
                            isPolling = true;
                            pollInterval = setInterval(function() {
                                if(!isPolling) return;
                                
                                const form = document.getElementById("auto-verify-form");
                                const formData = new FormData(form);
                                formData.set("action-v2", "transaction-verify-auto");

                                fetch("", { 
                                    method: "POST",
                                    body: formData 
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if(data.status === "true" || data.status === "pending") {
                                        stopPolling();
                                        clearInterval(timerInterval);
                                        
                                        if (data.status === "pending") {
                                            window.location.href = data.redirect ? data.redirect : "'.$final_return_url.'";
                                            return;
                                        }

                                        // Show Success Animation UI
                                        const toast = document.getElementById("zini-success-toast");
                                        const successScreen = document.getElementById("zini-step-success");
                                        const successTimerVal = document.getElementById("success-timer-val");
                                        
                                        if (toast) toast.classList.add("show");
                                        if (document.getElementById("zini-step1")) document.getElementById("zini-step1").style.display = "none";
                                        if (document.getElementById("zini-step2")) document.getElementById("zini-step2").style.display = "none";
                                        if (document.getElementById("zini-step-default")) document.getElementById("zini-step-default").style.display = "none";
                                        if (successScreen) {
                                            successScreen.classList.remove("d-none");
                                            successScreen.style.display = "block";
                                        }
                                        window.scrollTo({ top: 0, behavior: "smooth" });
                                        
                                        let redirectTimer = 10;
                                        if(successTimerVal) successTimerVal.textContent = redirectTimer;
                                        
                                        const merchantUrl = "'.$real_return_url.'";
                                        let finalRedirect = data.redirect ? data.redirect : "'.$final_return_url.'";
                                        if (merchantUrl !== "" && merchantUrl !== "--") {
                                            finalRedirect = merchantUrl + (merchantUrl.includes("?") ? "&" : "?") + "pp_status=completed&transaction_ref='.$data['transaction']['ref'].'";
                                        }

                                        const redirectInterval = setInterval(() => {
                                            redirectTimer--;
                                            if(successTimerVal) successTimerVal.textContent = redirectTimer;
                                            if(redirectTimer <= 0) {
                                                clearInterval(redirectInterval);
                                                window.location.href = finalRedirect;
                                            }
                                        }, 1000);
                                    }
                                })
                                .catch(error => console.error("Error:", error));
                            }, 5000); // Check every 5 seconds
                        }

                        // Manual Form Submit Handling
                        let autoVerifyClicks = 0;
                        let trxidVerifyAttempts = 0;
                        const formSubmits = document.querySelectorAll(".payment-form-submit");
                        formSubmits.forEach(formSubmit => {
                            formSubmit.addEventListener("submit", function(e) {
                                e.preventDefault();
                                stopPolling();
                                
                                const formData = new FormData(this);
                                const actionType = formData.get("action-v2");

                                if (actionType === "transaction-verify-auto") {
                                    autoVerifyClicks++;
                                    if (autoVerifyClicks >= 2) {
                                        if(typeof createToast === "function") {
                                            createToast({
                                                title: "Action Required",
                                                description: "Please click \"Verify with TrxID instead\" below and enter your TrxID.",
                                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                                timeout: 4000,
                                                top: 20
                                            });
                                        }
                                        const fallbackBtn = document.getElementById("trxid-fallback-btn");
                                        if(fallbackBtn) fallbackBtn.classList.remove("d-none");
                                        return; // Stop the request so we do not get 2 popups
                                    }
                                } else {
                                    trxidVerifyAttempts++;
                                    // On 2nd+ attempt with TrxID, force pending status
                                    if (trxidVerifyAttempts >= 2) {
                                        formData.set("force_pending", "true");
                                    }
                                }

                                const submitBtn = this.querySelector(".zini-verify-btn");
                                const originalText = submitBtn ? submitBtn.innerHTML : "Verify";
                                if(submitBtn) submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

                                fetch("", { 
                                    method: "POST",
                                    body: formData 
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if(submitBtn) submitBtn.innerHTML = originalText;
                                    if(data.status === "true" || data.status === "pending") {
                                        stopPolling();
                                        clearInterval(timerInterval);

                                        if (data.status === "pending") {
                                            window.location.href = data.redirect ? data.redirect : "'.$final_return_url.'";
                                            return;
                                        }

                                        // Show Success Animation UI
                                        const successScreen = document.getElementById("zini-step-success");
                                        const successTimerVal = document.getElementById("success-timer-val");
                                        
                                        if (document.getElementById("zini-step1")) document.getElementById("zini-step1").style.display = "none";
                                        if (document.getElementById("zini-step2")) document.getElementById("zini-step2").style.display = "none";
                                        if (document.getElementById("zini-step-default")) document.getElementById("zini-step-default").style.display = "none";
                                        if (successScreen) {
                                            successScreen.classList.remove("d-none");
                                            successScreen.style.display = "block";
                                        }
                                        window.scrollTo({ top: 0, behavior: "smooth" });
                                        
                                        ';

                                        echo '
                                        let redirectTimer = 8;
                                        if(successTimerVal) successTimerVal.textContent = redirectTimer;
                                        
                                        const merchantUrl = "'.$real_return_url.'";
                                        let finalRedirect = data.redirect ? data.redirect : "'.$final_return_url.'";
                                        if (merchantUrl !== "" && merchantUrl !== "--") {
                                            finalRedirect = merchantUrl + (merchantUrl.includes("?") ? "&" : "?") + "pp_status=completed&transaction_ref='.$data['transaction']['ref'].'";
                                        }


                                        const redirectInterval = setInterval(() => {
                                            redirectTimer--;
                                            if(successTimerVal) successTimerVal.textContent = redirectTimer;
                                            if(redirectTimer <= 0) {
                                                clearInterval(redirectInterval);
                                                window.location.href = finalRedirect;
                                            }
                                        }, 1000);
                                        
                                    } else {
                                        // Show error toast
                                        if(typeof createToast === "function") {
                                            createToast({
                                                title: data.title || "Verification Failed",
                                                description: data.message || "Could not verify. Try again.",
                                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                                timeout: 3000,
                                                top: 20
                                            });
                                        } else {
                                            alert(data.message);
                                        }
                                        
                                        if(data.visible_number) {
                                            const senderMobileBox = document.getElementById("sender-mobile-box");
                                            if(senderMobileBox) senderMobileBox.classList.remove("d-none");
                                        }
                                        
                                        const autoForm = document.getElementById("auto-verify-form");
                                        if(autoForm && (!trxidInputContainer || trxidInputContainer.classList.contains("d-none"))) {
                                            startPolling(); // resume polling if auto verify failed
                                        }
                                    }
                                })
                                .catch(error => {
                                    if(submitBtn) submitBtn.innerHTML = originalText;
                                    console.error("Error:", error);
                                    if(typeof createToast === "function") {
                                        createToast({
                                            title: "Connection Error",
                                            description: "Could not connect to server. Please try again.",
                                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                            timeout: 3000,
                                            top: 20
                                        });
                                    }
                                });
                            });
                        });
                    });
                </script>
                ';
            } elseif (isset($gateway_info['gateway_type']) && $gateway_info['gateway_type'] == 'manual') {
                $primaryColor = $response_gateway['response'][0]['primary_color'] ?? '#0284c7';
                $textColor = '#ffffff'; // Force white text to avoid black on black
                $g_logo = $response_gateway['response'][0]['logo'];
                $raw_amount = money_round($data['transaction']['local_net_amount'], 0);
                $amount = $raw_amount.' '.$data['transaction']['local_currency'];
                $amount_copy_btn = '<div class="zini-copy-btn" onclick="pp_copy(\''.htmlspecialchars($raw_amount, ENT_QUOTES).'\', \'Amount Copied!\', this)" title="Copy"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="14px" width="14px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg></div>';
                $g_name = $response_gateway['response'][0]['display'];
                $g_type_label = ($response_gateway['response'][0]['tab'] == 'bank') ? 'Bank Transfer / NPSB' : 'Manual Payment';
                
                echo '
                <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                <style>
                .zini-manual-card {
                    background: transparent; margin: 0 auto; width: 100%; max-width: 500px; font-family: "Anek Bangla", "Inter", sans-serif;
                }
                .zini-manual-header { 
                    display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
                    padding: 8px 0; border-bottom: 1px solid #f1f5f9;
                }
                .zini-manual-header-left { display: flex; align-items: center; gap: 12px; }
                .zini-manual-header-left img { height: 44px; object-fit: contain; border-radius: 4px; }
                .zini-manual-title { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.2; }
                .zini-manual-subtitle { font-size: 13px; color: #64748b; margin: 0; font-weight: 500; margin-top: 2px; }
                .zini-manual-amount { font-size: 20px; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 8px; }
                
                .zini-instruction-box { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 10px; padding: 12px 16px; margin-bottom: 24px; }
                .zini-instruction-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
                .zini-instruction-item:last-child { border-bottom: none; }
                .zini-instruction-label { color: #64748b; font-weight: 500; font-size: 13px; }
                .zini-instruction-value { color: #0f172a; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px; text-align: right; }
                .zini-copy-btn { color: #64748b; cursor: pointer; padding: 6px 8px; border-radius: 6px; transition: 0.2s; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; }
                .zini-copy-btn:hover { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }
                
                .zini-form-group { margin-bottom: 16px; }
                .zini-form-control { width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; box-sizing: border-box; background: #fff; font-weight: 500; }
                .zini-form-control::placeholder { color: #94a3b8; }
                .zini-form-control:focus { border-color: '.$primaryColor.'; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1); }
                
                .custom-file-upload { display: flex; align-items: center; justify-content: center; width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: #fff; cursor: pointer; color: #475569; font-weight: 500; transition: 0.2s; text-align: center; margin-bottom: 16px; }
                .custom-file-upload:hover { border-color: #cbd5e1; background: #f8fafc; }
                
                .zini-btn-submit { width: 100%; padding: 16px; background: #10b981; color: #ffffff; border: none; border-radius: 50px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 8px; }
                .zini-btn-submit:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                </style>
                
                <div class="zini-manual-card">
                    <div class="zini-manual-header">
                        <div class="zini-manual-header-left">
                            <img src="'.$g_logo.'" alt="Logo">
                            <div>
                                <h3 class="zini-manual-title">'.$g_name.'</h3>
                                <p class="zini-manual-subtitle">'.$g_type_label.'</p>
                            </div>
                        </div>
                        <div class="zini-manual-amount"><span>'.$amount.'</span>'.$amount_copy_btn.'</div>
                    </div>
                    
                    <div class="zini-manual-body">
                        <div class="zini-instruction-box">
                ';
                
                if (!empty($instructions)) {
                    foreach ($instructions as $ins) {
                        $label = $lang[$ins['text']] ?? $ins['text'];
                        $val = $ins['value'] ?? '';
                        
                        if (!empty($ins['vars'])) {
                            foreach ($ins['vars'] as $k => $v) {
                                $label = str_replace($k, $v, $label);
                            }
                        }
                        // Skip empty values, placeholders, and unwanted fields
                        if (empty($val) || $val == '--' || in_array($ins['text'], ['bank_step_slip', 'bank_step_bank_name', 'bank_step_amount'])) {
                            continue;
                        }
                        
                        $parts = explode(':', $label, 2);
                        $clean_label = trim($parts[0]);
                        
                        $copyHtml = '';
                        // Only show copy button for specific fields
                        $allow_copy = in_array($ins['text'], ['bank_step_account_name', 'bank_step_account_number', 'bank_step_branch_name', 'bank_step_routing_number']);
                        if (!empty($ins['copy']) && $allow_copy) {
                            $copyHtml = '<div class="zini-copy-btn" onclick="pp_copy(\''.htmlspecialchars($val, ENT_QUOTES).'\', \''.htmlspecialchars($clean_label, ENT_QUOTES).' Copied!\')" title="Copy"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="14px" width="14px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg></div>';
                        }
                        
                        echo '
                        <div class="zini-instruction-item">
                            <span class="zini-instruction-label">'.$clean_label.'</span>
                            <span class="zini-instruction-value">'.$val.' '.$copyHtml.'</span>
                        </div>';
                    }
                }
                
                echo '
                        </div>
                        
                        <form id="zini-manual-form" class="zini-form" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">
                            <input type="hidden" name="action-v2" value="transaction-verify">
                            <input type="hidden" name="gateway-id" value="'.$response_gateway['response'][0]['gateway_id'].'">
                            <input type="hidden" name="transaction-id" value="'.$data['transaction']['ref'].'">
                ';
                
                if (isset($gateway_info['verify_by']) && $gateway_info['verify_by'] == 'trxid') {
                    echo '
                            <div class="zini-form-group">
                                <input type="text" name="trxid" class="zini-form-control" placeholder="'.$lang['enter_transaction_id'].'" required>
                            </div>
                    ';
                } elseif (isset($gateway_info['verify_by']) && $gateway_info['verify_by'] == 'slip') {
                    echo '
                            <label class="custom-file-upload">
                                <input type="file" name="slip" accept="image/*" style="display: none;" onchange="this.nextElementSibling.innerHTML = \'&#10003; File Selected: \' + this.files[0].name;" required>
                                <span>Payment Slip / Screenshot</span>
                            </label>
                            
                            <div class="zini-form-group">
                                <input type="text" name="trxid" class="zini-form-control" placeholder="Transaction Reference / Bank Reference ID">
                            </div>
                            <div class="zini-form-group">
                                <input type="text" name="optional_info" class="zini-form-control" placeholder="Optional info">
                            </div>
                    ';
                }
                
                echo '
                            <button type="submit" class="zini-btn-submit">Submit Bank Payment</button>
                        </form>
                    </div>
                </div>
                
                <script>
                document.getElementById("zini-manual-form").addEventListener("submit", function(e) {
                    e.preventDefault();
                    const btn = this.querySelector(".zini-btn-submit");
                    const originalText = btn.innerHTML;
                    btn.innerHTML = "Processing...";
                    btn.style.opacity = "0.7";
                    btn.style.pointerEvents = "none";
                    
                    const formData = new FormData(this);
                    fetch("", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.status === "pending" || data.status === "true") {
                            window.location.reload();
                        } else {
                            if (typeof createToast === "function") {
                                createToast({
                                    title: data.title || "Request Failed",
                                    description: data.message || "Could not process request.",
                                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                    timeout: 3000,
                                    top: 20
                                });
                            } else {
                                alert(data.message || "Request failed.");
                            }
                            btn.innerHTML = originalText;
                            btn.style.opacity = "1";
                            btn.style.pointerEvents = "auto";
                        }
                    })
                    .catch(err => {
                        alert("Network error. Please try again.");
                        btn.innerHTML = originalText;
                        btn.style.opacity = "1";
                        btn.style.pointerEvents = "auto";
                    });
                });
                </script>
                ';
            } elseif (isset($gateway) && is_object($gateway) && method_exists($gateway, 'process_payment')) {
                $gateway->process_payment($data);
            }
        }
    }

    function add_filter(string $hook, callable $callback, int $priority = 10)
    {
        $GLOBALS['__filters'][$hook][$priority][] = $callback;
    }

    function apply_filters(string $hook, $value, ...$args)
    {
        if (empty($GLOBALS['__filters'][$hook])) {
            return $value;
        }

        ksort($GLOBALS['__filters'][$hook]);

        foreach ($GLOBALS['__filters'][$hook] as $callbacks) {
            foreach ($callbacks as $callback) {
                try {
                    $value = call_user_func($callback, $value, ...$args);
                } catch (Throwable $e) {
                    error_log('Filter error ['.$hook.']: '.$e->getMessage());
                }
            }
        }

        return $value;
    }

    /*
    add_filter('invoice.total', function ($total, $invoice) {
        return $total + 10;
    });
    add_action('invoice.updated', function ($invoice) {
        error_log('Wallet credited for invoice '.$invoice['id']);
    });
    */

    class DB
    {
        protected static ?PDO $pdo = null;

        protected static function pdo(): PDO
        {
            if (!self::$pdo) {
                self::$pdo = connectDatabase(); // your existing function
            }
            return self::$pdo;
        }

        public static function table(string $table): QueryBuilder
        {
            global $db_prefix;
            return new QueryBuilder($db_prefix . $table, self::pdo());
        }

        /* ========================
        TRANSACTIONS
        ======================== */

        public static function beginTransaction(): void
        {
            self::pdo()->beginTransaction();
        }

        public static function commit(): void
        {
            self::pdo()->commit();
        }

        public static function rollBack(): void
        {
            self::pdo()->rollBack();
        }
    }
    class QueryBuilder
    {
        protected PDO $pdo;
        protected string $table;

        protected array $wheres = [];
        protected array $bindings = [];
        protected array $orders = [];

        protected ?int $limit = null;
        protected ?int $offset = null;

        public function __construct(string $table, PDO $pdo)
        {
            $this->table = $table;
            $this->pdo   = $pdo;
        }

        /* ========================
        WHERE
        ======================== */

        public function where(string $column, $operator, $value = null): self
        {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }

            $this->wheres[] = ['AND', "$column $operator ?"];
            $this->bindings[] = $value;

            return $this;
        }

        public function orWhere(string $column, $operator, $value = null): self
        {
            if ($value === null) {
                $value = $operator;
                $operator = '=';
            }

            $this->wheres[] = ['OR', "$column $operator ?"];
            $this->bindings[] = $value;

            return $this;
        }

        public function whereIn(string $column, array $values): self
        {
            $placeholders = implode(',', array_fill(0, count($values), '?'));

            $this->wheres[] = ['AND', "$column IN ($placeholders)"];
            $this->bindings = array_merge($this->bindings, $values);

            return $this;
        }

        /* ========================
        ORDER / LIMIT
        ======================== */

        public function orderBy(string $column, string $direction = 'ASC'): self
        {
            $this->orders[] = "$column " . strtoupper($direction);
            return $this;
        }

        public function limit(int $limit): self
        {
            $this->limit = $limit;
            return $this;
        }

        public function offset(int $offset): self
        {
            $this->offset = $offset;
            return $this;
        }

        /* ========================
        READ
        ======================== */

        public function get(): array
        {
            $sql = $this->buildSelect();
            $rows = $this->run($sql, true, true);
            $this->reset();

            return $rows;
        }

        public function first(): ?object
        {
            $this->limit = 1;
            $sql = $this->buildSelect();

            $row = $this->run($sql, true, false);
            $this->reset();

            return $row ?: null;
        }

        public function count(): int
        {
            $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
            $sql .= $this->compileWhere();

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->bindings);

            $this->reset();
            return (int) $stmt->fetch(PDO::FETCH_OBJ)->total;
        }

        public function exists(): bool
        {
            return $this->count() > 0;
        }

        /* ========================
        INSERT
        ======================== */

        public function insert(array $data): bool
        {
            $columns = array_keys($data);
            $placeholders = implode(',', array_fill(0, count($columns), '?'));

            $sql = "INSERT INTO {$this->table} (" .
                implode(',', $columns) .
                ") VALUES ($placeholders)";

            return $this->run($sql, false, false, array_values($data));
        }

        /* ========================
        UPDATE
        ======================== */

        public function update(array $data): bool
        {
            $sets = [];

            foreach ($data as $col => $val) {
                $sets[] = "$col = ?";
                $this->bindings[] = $val;
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
            $sql .= $this->compileWhere();

            $result = $this->run($sql, false, false);
            $this->reset();

            return $result;
        }

        /* ========================
        DELETE
        ======================== */

        public function delete(): bool
        {
            $sql = "DELETE FROM {$this->table}";
            $sql .= $this->compileWhere();

            $result = $this->run($sql, false, false);
            $this->reset();

            return $result;
        }

        /* ========================
        INTERNAL
        ======================== */

        protected function buildSelect(): string
        {
            $sql = "SELECT * FROM {$this->table}";
            $sql .= $this->compileWhere();

            if ($this->orders) {
                $sql .= ' ORDER BY ' . implode(', ', $this->orders);
            }

            if ($this->limit !== null) {
                $sql .= " LIMIT {$this->limit}";
            }

            if ($this->offset !== null) {
                $sql .= " OFFSET {$this->offset}";
            }

            return $sql;
        }

        protected function compileWhere(): string
        {
            if (!$this->wheres) {
                return '';
            }

            $sql = ' WHERE ';
            foreach ($this->wheres as $i => [$type, $condition]) {
                $sql .= ($i === 0 ? '' : " $type ") . $condition;
            }

            return $sql;
        }

        protected function run(
            string $sql,
            bool $fetch,
            bool $fetchAll,
            array $bindings = []
        ) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindings ?: $this->bindings);

            if (!$fetch) {
                return true;
            }

            return $fetchAll
                ? $stmt->fetchAll(PDO::FETCH_OBJ)
                : $stmt->fetch(PDO::FETCH_OBJ);
        }

        protected function reset(): void
        {
            $this->wheres = [];
            $this->bindings = [];
            $this->orders = [];
            $this->limit = null;
            $this->offset = null;
        }
    }
if (!function_exists('pp_renderFormFields')) {
    function pp_renderFormFields($type, $data = []) {
        $html = '';

        // Add essential hidden fields for form submission
        $html .= '<input type="hidden" name="action-v2" value="' . htmlspecialchars($type) . '">';
        $html .= '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'] ?? '') . '">';
        
        // Set itemid based on type - using correct data keys from index.php
        if ($type === 'payment-link-default') {
            // Default payment link: brand id is in $data['brand']['id']
            $html .= '<input type="hidden" name="itemid" value="' . htmlspecialchars($data['brand']['id'] ?? '') . '">';
        } elseif ($type === 'payment-link') {
            // Custom payment link: link id is in $data['paymentLink']['pid']
            $html .= '<input type="hidden" name="itemid" value="' . htmlspecialchars($data['paymentLink']['pid'] ?? '') . '">';
        } elseif ($type === 'invoice') {
            $html .= '<input type="hidden" name="itemid" value="' . htmlspecialchars($data['invoice']['iid'] ?? '') . '">';
        }

        // Amount field - always show
        $isReadonly = false;
        $amountValue = '';

        if ($type === 'payment-link') {
            if (isset($data['paymentLink']['total']) && floatval($data['paymentLink']['total']) > 0) {
                $isReadonly = true;
                $amountValue = $data['paymentLink']['total'];
            }
        }

        // Standard customer fields removed as per request. Only custom fields will be rendered.

        $html .= '<div class="mb-3">';
        $html .= '<label class="form-label" style="text-align: left; display: block; font-weight: 500;">' . ($data['lang']['amount'] ?? 'Amount') . ' <span class="text-danger">*</span></label>';
        $html .= '<div class="input-group input-group-lg">';
        $currency = $data['paymentLink']['currency'] ?? ($data['brand']['locale']['currency'] ?? 'BDT');
        $html .= '<span class="input-group-text" style="border-radius: 8px 0 0 8px;">' . htmlspecialchars($currency) . '</span>';
        
        $readonlyAttr = $isReadonly ? 'readonly' : '';
        $valueAttr = $amountValue !== '' ? 'value="' . htmlspecialchars($amountValue) . '"' : '';
        
        $html .= '<input type="text" inputmode="text" pattern="^\$?\d+(\.\d+)?\$?$" title="Enter amount, e.g. 500 or 5$" name="amount" class="form-control form-control-lg" required ' . $readonlyAttr . ' ' . $valueAttr . ' style="border-radius: 0 8px 8px 0; background-color: ' . ($isReadonly ? '#f3f4f6' : '#fff') . ';">';
        $html .= '</div>';
        $html .= '</div>';

        // Custom fields created by admin (only for payment-link type)
        // The data key is 'fields' (set in index.php line 1664), NOT 'customFields'
        if ($type === 'payment-link' && isset($data['paymentLink']['fields']) && is_array($data['paymentLink']['fields'])) {
            foreach ($data['paymentLink']['fields'] as $field) {
                $fieldName = $field['name'] ?? '';
                $fieldLabel = $field['label'] ?? $fieldName;
                $fieldType = $field['type'] ?? 'text';
                $isRequired = (isset($field['required']) && $field['required'] === 'true') ? 'required' : '';
                $asterisk = (isset($field['required']) && $field['required'] === 'true') ? ' <span class="text-danger">*</span>' : '';
                
                $html .= '<div class="mb-3">';
                $html .= '<label class="form-label" style="text-align: left; display: block; font-weight: 500;">' . htmlspecialchars($fieldLabel) . $asterisk . '</label>';
                
                if ($fieldType === 'text') {
                    $html .= '<input type="text" name="' . htmlspecialchars($fieldName) . '" class="form-control form-control-lg" ' . $isRequired . ' style="border-radius: 8px;">';
                } elseif ($fieldType === 'textarea') {
                    $html .= '<textarea name="' . htmlspecialchars($fieldName) . '" class="form-control form-control-lg" rows="3" ' . $isRequired . ' style="border-radius: 8px;"></textarea>';
                } elseif ($fieldType === 'select') {
                    $html .= '<select name="' . htmlspecialchars($fieldName) . '" class="form-select form-select-lg" ' . $isRequired . ' style="border-radius: 8px;">';
                    $html .= '<option value="">Select option</option>';
                    if (!empty($field['options'])) {
                        foreach ($field['options'] as $opt) {
                            $html .= '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                        }
                    }
                    $html .= '</select>';
                } elseif ($fieldType === 'checkbox') {
                    if (!empty($field['options'])) {
                        foreach ($field['options'] as $opt) {
                            $html .= '<div class="form-check">';
                            $html .= '<input class="form-check-input" type="checkbox" name="' . htmlspecialchars($fieldName) . '[]" value="' . htmlspecialchars($opt) . '">';
                            $html .= '<label class="form-check-label">' . htmlspecialchars($opt) . '</label>';
                            $html .= '</div>';
                        }
                    }
                } elseif ($fieldType === 'radio') {
                    if (!empty($field['options'])) {
                        foreach ($field['options'] as $opt) {
                            $html .= '<div class="form-check">';
                            $html .= '<input class="form-check-input" type="radio" name="' . htmlspecialchars($fieldName) . '" value="' . htmlspecialchars($opt) . '" ' . $isRequired . '>';
                            $html .= '<label class="form-check-label">' . htmlspecialchars($opt) . '</label>';
                            $html .= '</div>';
                        }
                    }
                } elseif ($fieldType === 'file') {
                    $html .= '<input type="file" name="' . htmlspecialchars($fieldName) . '" class="form-control form-control-lg" ' . $isRequired . ' style="border-radius: 8px;">';
                }
                $html .= '</div>';
            }
        }

        // Invoice custom fields use 'customFields' key
        if ($type === 'invoice' && isset($data['invoice']['customFields']) && is_array($data['invoice']['customFields'])) {
            foreach ($data['invoice']['customFields'] as $field) {
                $required = (isset($field['required']) && $field['required'] === 'true') ? 'required' : '';
                $asterisk = (isset($field['required']) && $field['required'] === 'true') ? ' <span class="text-danger">*</span>' : '';
                $html .= '<div class="mb-3">';
                $html .= '<label class="form-label" style="text-align: left; display: block; font-weight: 500;">' . htmlspecialchars($field['fieldName'] ?? '') . $asterisk . '</label>';
                
                $fType = $field['formType'] ?? 'text';
                $fId = $field['fieldID'] ?? '';
                if ($fType === 'text') {
                    $html .= '<input type="text" name="customFields[' . $fId . ']" class="form-control form-control-lg" ' . $required . ' style="border-radius: 8px;">';
                } elseif ($fType === 'textarea') {
                    $html .= '<textarea name="customFields[' . $fId . ']" class="form-control form-control-lg" rows="3" ' . $required . ' style="border-radius: 8px;"></textarea>';
                } elseif ($fType === 'select') {
                    $html .= '<select name="customFields[' . $fId . ']" class="form-select form-select-lg" ' . $required . ' style="border-radius: 8px;">';
                    $html .= '<option value="">Select option</option>';
                    if (!empty($field['options'])) {
                        foreach ($field['options'] as $opt) {
                            $html .= '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                        }
                    }
                    $html .= '</select>';
                } elseif ($fType === 'file') {
                    $html .= '<input type="file" name="customFields[' . $fId . ']" class="form-control form-control-lg" ' . $required . ' style="border-radius: 8px;">';
                }
                $html .= '</div>';
            }
        }

        echo $html;
    }
}


