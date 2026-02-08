<?php
/**
 * API Endpoint
 * Central API for all operations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Load config
$config = require_once 'config.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_path = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$endpoint = (!empty($request_path[0]) ? $request_path[0] : null) ?? $_GET['endpoint'] ?? 'status';

try {
    // Route requests
    switch ($endpoint) {
        case 'status':
            getSystemStatus();
            break;
            
        case 'stats':
            getSystemStats();
            break;
            
        case 'health':
            healthCheck();
            break;
            
        case 'info':
            getSystemInfo();
            break;
            
        default:
            throw new Exception('Unknown endpoint: ' . $endpoint);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function getSystemStatus() {
    $data_dir = __DIR__ . '/../data';
    
    $status = [
        'database' => file_exists($data_dir),
        'writable' => is_writable($data_dir),
        'messages' => countFile($data_dir . '/messages.json'),
        'game_logs' => countFile($data_dir . '/game_logs.json'),
        'form_submissions' => countFile($data_dir . '/form_submissions.json'),
        'email_logs' => countFile($data_dir . '/email_log.json')
    ];
    
    echo json_encode([
        'success' => true,
        'status' => $status
    ]);
}

function getSystemStats() {
    $data_dir = __DIR__ . '/../data';
    
    // Get all stats
    $messages = file_exists($data_dir . '/messages.json') ? 
        json_decode(file_get_contents($data_dir . '/messages.json'), true) : [];
    
    $game_logs = file_exists($data_dir . '/game_logs.json') ? 
        json_decode(file_get_contents($data_dir . '/game_logs.json'), true) : [];
    
    $form_submissions = file_exists($data_dir . '/form_submissions.json') ? 
        json_decode(file_get_contents($data_dir . '/form_submissions.json'), true) : [];
    
    $stats = [
        'total_messages' => count($messages),
        'total_games_played' => count($game_logs),
        'total_submissions' => count($form_submissions),
        'games_breakdown' => countByGame($game_logs),
        'submission_types' => countByType($form_submissions)
    ];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

function healthCheck() {
    $checks = [
        'php_version' => phpversion(),
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'database_dir' => is_writable(__DIR__ . '/../data'),
        'media_dir' => is_writable(__DIR__ . '/../img'),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode([
        'success' => true,
        'health' => $checks
    ]);
}

function getSystemInfo() {
    echo json_encode([
        'success' => true,
        'info' => [
            'name' => 'Girlfriend Surprise Website',
            'version' => '1.0.0',
            'version_date' => '2026-02-08',
            'php_version' => phpversion(),
            'features' => [
                'Love Quiz Game',
                'Memory Match Game',
                'Scratch Card Game',
                'Spin the Wheel',
                'Love Trivia',
                'Fortune Teller',
                'Love Calculator',
                'Message Storage',
                'Email Sending',
                'Game Logging',
                'Form Handling'
            ]
        ]
    ]);
}

function countFile($file) {
    if (!file_exists($file)) return 0;
    $data = json_decode(file_get_contents($file), true);
    return count($data ?? []);
}

function countByGame($logs) {
    $count = [];
    foreach ($logs as $log) {
        $game = $log['game'] ?? 'unknown';
        $count[$game] = ($count[$game] ?? 0) + 1;
    }
    return $count;
}

function countByType($submissions) {
    $count = [];
    foreach ($submissions as $sub) {
        $type = $sub['type'] ?? 'unknown';
        $count[$type] = ($count[$type] ?? 0) + 1;
    }
    return $count;
}
?>
