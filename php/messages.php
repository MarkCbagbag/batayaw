<?php
/**
 * Message Handler
 * Store and retrieve love messages (MySQL with JSON fallback)
 */

header('Content-Type: application/json');

// Try to use database, fallback to JSON
require_once 'db.php';
require_once 'config.php';
$db = getDB();
$useDB = ($db !== null);

// Database simulation using JSON file (fallback)
$messages_file = __DIR__ . '/../data/messages.json';

// Create data directory if it doesn't exist
$data_dir = __DIR__ . '/../data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

// Ensure messages file exists (for fallback)
if (!file_exists($messages_file)) {
    file_put_contents($messages_file, json_encode([]));
}

$request_method = $_SERVER['REQUEST_METHOD'];
$request_data = json_decode(file_get_contents('php://input'), true);

try {
    if ($request_method === 'POST') {
        $msg = sanitize($request_data['message'] ?? '');
        $sender = sanitize($request_data['sender'] ?? 'Anonymous');
        $emoji = $request_data['emoji'] ?? '💌';
        
        if (empty($msg)) {
            throw new Exception('Message cannot be empty');
        }
        
        if ($useDB) {
            // Save to MySQL
            $id = $db->insert(
                'INSERT INTO messages (sender, message, emoji) VALUES (?, ?, ?)',
                [$sender, $msg, $emoji]
            );
            echo json_encode([
                'success' => true,
                'message' => 'Message saved successfully! 💕',
                'data' => ['id' => $id, 'sender' => $sender, 'message' => $msg, 'emoji' => $emoji]
            ]);
        } else {
            // Fallback to JSON file
            $message = [
                'id' => time(),
                'message' => $msg,
                'sender' => $sender,
                'timestamp' => date('Y-m-d H:i:s'),
                'emoji' => $emoji
            ];
            $messages = json_decode(file_get_contents($messages_file), true) ?? [];
            $messages[] = $message;
            if (file_put_contents($messages_file, json_encode($messages, JSON_PRETTY_PRINT))) {
                echo json_encode(['success' => true, 'message' => 'Message saved successfully! 💕', 'data' => $message]);
            } else {
                throw new Exception('Failed to save message');
            }
        }
        
    } elseif ($request_method === 'GET') {
        if ($useDB) {
            // Fetch from MySQL
            $rows = $db->fetchAll('SELECT id, sender, message, emoji, created_at FROM messages ORDER BY created_at DESC');
            $messages = [];
            foreach ($rows as $row) {
                $messages[] = [
                    'id' => $row['id'],
                    'sender' => $row['sender'],
                    'message' => $row['message'],
                    'emoji' => $row['emoji'],
                    'created_at' => $row['created_at']
                ];
            }
            echo json_encode(['success' => true, 'count' => count($messages), 'messages' => $messages]);
        } else {
            // Fallback to JSON
            $messages = json_decode(file_get_contents($messages_file), true) ?? [];
            echo json_encode(['success' => true, 'count' => count($messages), 'messages' => array_reverse($messages)]);
        }
        
    } elseif ($request_method === 'DELETE') {
        $id = $request_data['id'] ?? null;
        if (!$id) throw new Exception('Message ID required');

        if ($useDB) {
            // Delete from MySQL
            $db->delete('DELETE FROM messages WHERE id = ?', [$id]);
            echo json_encode(['success' => true, 'message' => 'Message deleted successfully!']);
        } else {
            // Fallback to JSON
            $messages = json_decode(file_get_contents($messages_file), true) ?? [];
            $messages = array_filter($messages, function($msg) use ($id) {
                return $msg['id'] != $id;
            });
            file_put_contents($messages_file, json_encode(array_values($messages), JSON_PRETTY_PRINT));
            echo json_encode(['success' => true, 'message' => 'Message deleted successfully!']);
        }
        
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
