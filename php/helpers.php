<?php
/**
 * Utilities & Helpers
 * Common functions used across the application
 */

/**
 * Sanitize user input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate unique ID
 */
function generateId() {
    return uniqid('id_', true);
}

/**
 * Get configuration
 */
function getConfig($key = null) {
    static $config = null;
    
    if ($config === null) {
        $config = require_once 'config.php';
    }
    
    if ($key === null) {
        return $config;
    }
    
    return $config[$key] ?? null;
}

/**
 * Get data directory
 */
function getDataDir() {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

/**
 * Write to log file
 */
function writeLog($message, $level = 'INFO') {
    $log_file = getDataDir() . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Get romantic emoji
 */
function getRandomEmoji() {
    $emojis = ['❤️', '💕', '💖', '💗', '💝', '💘', '💞', '💓', '✨', '🌹'];
    return $emojis[array_rand($emojis)];
}

/**
 * Format date for display
 */
function formatDate($date_string, $format = 'M d, Y - H:i') {
    return date($format, strtotime($date_string));
}

/**
 * Send JSON response
 */
function sendResponse($success, $message = '', $data = []) {
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
    exit;
}

/**
 * Send error response
 */
function sendError($error, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $error
    ]);
    exit;
}

/**
 * Check if file exists and is readable
 */
function safeRead($file) {
    if (!file_exists($file) || !is_readable($file)) {
        return null;
    }
    return file_get_contents($file);
}

/**
 * Safe JSON decode
 */
function safeJsonDecode($json, $assoc = true) {
    $data = json_decode($json, $assoc);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    return $data;
}

/**
 * Get database connection (placeholder for future DB implementation)
 */
function getDatabase() {
    // TODO: Implement actual database connection
    return null;
}

/**
 * Create backup of data
 */
function backupData() {
    $data_dir = getDataDir();
    $backup_dir = getDataDir() . '/backups';
    
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $backup_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.zip';
    
    // Create backup (requires zip extension)
    if (extension_loaded('zip')) {
        $zip = new ZipArchive();
        if ($zip->open($backup_file, ZipArchive::CREATE) === true) {
            $files = glob($data_dir . '/*.json');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            return $backup_file;
        }
    }
    
    return null;
}

/**
 * Generate template/placeholder data
 */
function generateSampleData() {
    return [
        'messages' => [
            [
                'id' => time(),
                'message' => 'You make me the happiest person alive! 💕',
                'sender' => 'Your Love',
                'timestamp' => date('Y-m-d H:i:s'),
                'emoji' => '💕'
            ]
        ],
        'games' => [
            'quiz' => ['played' => 0, 'best_score' => 0],
            'memory' => ['played' => 0, 'best_time' => 0],
            'scratch' => ['played' => 0],
            'wheel' => ['played' => 0],
            'trivia' => ['played' => 0, 'best_score' => 0],
            'fortune' => ['played' => 0]
        ]
    ];
}

/**
 * Verify CSRF token (if implemented)
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
        return false;
    }
    return true;
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>
