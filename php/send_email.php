<?php
/**
 * Email Sender
 * Send romantic messages via email
 */

header('Content-Type: application/json');

$config = require_once 'config.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_data = json_decode(file_get_contents('php://input'), true);

try {
    if ($request_method !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }
    
    $to = sanitize($request_data['to'] ?? '');
    $subject = sanitize($request_data['subject'] ?? '');
    $message = sanitize($request_data['message'] ?? '');
    
    // Validation
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    if (empty($subject) || empty($message)) {
        throw new Exception('Subject and message required');
    }
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . $config['email']['from_name'] . " <" . $config['email']['from'] . ">" . "\r\n";
    
    // Email body
    $email_body = "
        <html>
            <head>
                <style>
                    body { font-family: 'Poppins', Arial; background: linear-gradient(135deg, #667eea, #764ba2); padding: 20px; }
                    .container { background: white; padding: 30px; border-radius: 15px; max-width: 600px; margin: 0 auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
                    h2 { color: #ff6b9d; }
                    .message { background: linear-gradient(135deg, #fff5f7, #fff9fb); padding: 20px; border-left: 5px solid #ff6b9d; border-radius: 10px; margin: 20px 0; }
                    .footer { text-align: center; color: #999; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>💕 Special Message For You 💕</h2>
                    <div class='message'>
                        " . nl2br($message) . "
                    </div>
                    <div class='footer'>
                        <p>Sent with love 💕</p>
                    </div>
                </div>
            </body>
        </html>
    ";
    
    // Send email
    // Note: In a real environment, use PHPMailer or similar
    $mail_sent = mail($to, $subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Log the email
        logEmail([
            'to' => $to,
            'subject' => $subject,
            'status' => 'sent',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully! 💌'
        ]);
    } else {
        throw new Exception('Failed to send email. Please try again.');
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

function logEmail($data) {
    $log_file = __DIR__ . '/../data/email_log.json';
    
    // Create file if doesn't exist
    if (!file_exists($log_file)) {
        file_put_contents($log_file, json_encode([]));
    }
    
    $logs = json_decode(file_get_contents($log_file), true) ?? [];
    $logs[] = $data;
    
    file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT));
}
?>
