<?php
/**
 * Form Handler
 * Process and validate form submissions
 */

header('Content-Type: application/json');

$config = require_once 'config.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_data = json_decode(file_get_contents('php://input'), true);

$data_dir = __DIR__ . '/../data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

try {
    if ($request_method !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }
    
    $form_type = sanitize($request_data['type'] ?? '');
    
    if (empty($form_type)) {
        throw new Exception('Form type required');
    }
    
    // Route to appropriate handler
    switch ($form_type) {
        case 'contact':
            handleContactForm($request_data);
            break;
            
        case 'love_message':
            handleLoveMessageForm($request_data);
            break;
            
        case 'suggestion':
            handleSuggestionForm($request_data);
            break;
            
        case 'feedback':
            handleFeedbackForm($request_data);
            break;
            
        default:
            throw new Exception('Unknown form type');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function handleContactForm($data) {
    $name = sanitize($data['name'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $message = sanitize($data['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception('All fields required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    $entry = [
        'type' => 'contact',
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    saveFormSubmission($entry);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting us! 💕'
    ]);
}

function handleLoveMessageForm($data) {
    $from = sanitize($data['from'] ?? '');
    $message = sanitize($data['message'] ?? '');
    $emoji = $data['emoji'] ?? '💕';
    
    if (empty($from) || empty($message)) {
        throw new Exception('All fields required');
    }
    
    $entry = [
        'type' => 'love_message',
        'from' => $from,
        'message' => $message,
        'emoji' => $emoji,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    saveFormSubmission($entry);
    
    echo json_encode([
        'success' => true,
        'message' => 'Love message saved! 💕'
    ]);
}

function handleSuggestionForm($data) {
    $suggestion = sanitize($data['suggestion'] ?? '');
    $rating = (int)($data['rating'] ?? 5);
    
    if (empty($suggestion)) {
        throw new Exception('Suggestion required');
    }
    
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5');
    }
    
    $entry = [
        'type' => 'suggestion',
        'suggestion' => $suggestion,
        'rating' => $rating,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    saveFormSubmission($entry);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your suggestion! ✨'
    ]);
}

function handleFeedbackForm($data) {
    $feedback = sanitize($data['feedback'] ?? '');
    $category = sanitize($data['category'] ?? 'general');
    
    if (empty($feedback)) {
        throw new Exception('Feedback required');
    }
    
    $entry = [
        'type' => 'feedback',
        'feedback' => $feedback,
        'category' => $category,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    saveFormSubmission($entry);
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! 💕'
    ]);
}

function saveFormSubmission($entry) {
    $data_dir = __DIR__ . '/../data';
    $file = $data_dir . '/form_submissions.json';
    
    $submissions = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $submissions[] = $entry;
    
    if (!file_put_contents($file, json_encode($submissions, JSON_PRETTY_PRINT))) {
        throw new Exception('Failed to save submission');
    }
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
?>
