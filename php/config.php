<?php
/**
 * Configuration File
 * Central settings for the girlfriend surprise website
 */

return [
    // Site Information
    'site_name' => 'Our Special Surprise',
    'site_url' => 'http://localhost/girlfriend_surprise/',
    'timezone' => 'UTC',
    
    // Email Configuration
    'email' => [
        'from' => 'surprise@example.com',
        'from_name' => 'Your Surprise',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => 'your-email@gmail.com',
        'smtp_password' => 'your-app-password',
    ],
    
    // Database Configuration (Optional)
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'girlfriend_surprise',
    ],
    
    // Game Settings
    'games' => [
        'quiz_questions' => 5,
        'trivia_questions' => 8,
        'memory_pairs' => 8,
    ],
    
    // Romantic Messages
    'messages' => [
        'header' => 'Hi, My Love! 💕',
        'subtitle' => 'I created something special for you...',
        'footer' => 'Made with 💕 for you',
    ],
    
    // File Upload Settings
    'upload' => [
        'max_size' => 50000000, // 50MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg'],
        'upload_dir' => 'img/',
    ],
    
    // Security
    'security' => [
        'enable_cors' => true,
        'allowed_origins' => ['http://localhost', 'https://youromain.com'],
    ],
];
?>
