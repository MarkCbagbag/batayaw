<?php
/**
 * Data Logger
 * Track game scores and user interactions
 */

header('Content-Type: application/json');

$request_method = $_SERVER['REQUEST_METHOD'];
$request_data = json_decode(file_get_contents('php://input'), true);

$data_dir = __DIR__ . '/../data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

try {
    if ($request_method === 'POST') {
        // Log game score
        $game_name = sanitize($request_data['game'] ?? '');
        $score = (int)($request_data['score'] ?? 0);
        $time_taken = (int)($request_data['time'] ?? 0);
        
        if (empty($game_name)) {
            throw new Exception('Game name required');
        }
        
        $log_entry = [
            'game' => $game_name,
            'score' => $score,
            'time' => $time_taken,
            'timestamp' => date('Y-m-d H:i:s'),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100)
        ];
        
        // Save log
        $log_file = $data_dir . '/game_logs.json';
        $logs = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : [];
        $logs[] = $log_entry;
        
        file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'message' => 'Score logged successfully! 🎉'
        ]);
        
    } elseif ($request_method === 'GET') {
        // Get statistics
        $log_file = $data_dir . '/game_logs.json';
        
        if (!file_exists($log_file)) {
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_games' => 0,
                    'games' => []
                ]
            ]);
            return;
        }
        
        $logs = json_decode(file_get_contents($log_file), true) ?? [];
        
        // Calculate statistics
        $stats = [];
        $total_time = 0;
        $total_score = 0;
        $game_count = [];
        
        foreach ($logs as $log) {
            $game = $log['game'];
            $total_time += $log['time'];
            $total_score += $log['score'];
            
            if (!isset($game_count[$game])) {
                $game_count[$game] = 0;
            }
            $game_count[$game]++;
        }
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'total_games_played' => count($logs),
                'total_time_minutes' => round($total_time / 60, 2),
                'average_score' => count($logs) > 0 ? round($total_score / count($logs), 2) : 0,
                'breakdown_by_game' => $game_count
            ]
        ]);
        
    } elseif ($request_method === 'DELETE') {
        // Clear logs
        $log_file = $data_dir . '/game_logs.json';
        file_put_contents($log_file, json_encode([]));
        
        echo json_encode([
            'success' => true,
            'message' => 'Logs cleared successfully!'
        ]);
        
    } else {
        throw new Exception('Invalid request method');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>
