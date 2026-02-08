<?php
/**
 * Automatic Media Loader
 * Scans the img/ folder and returns all photos and videos
 */

header('Content-Type: application/json');

$imgFolder = '../img/';
$mediaFiles = [];

// Allowed file extensions
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'];

// Check if folder exists
if (!is_dir($imgFolder)) {
    echo json_encode(['error' => 'Image folder not found']);
    exit;
}

// Scan folder for files
$files = scandir($imgFolder);

foreach ($files as $file) {
    // Skip hidden files and parent directory
    if (strpos($file, '.') === 0) {
        continue;
    }
    
    // Get file extension
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    // Check if extension is allowed
    if (!in_array($extension, $allowedExtensions)) {
        continue;
    }
    
    // Determine media type
    $type = 'image';
    if (in_array($extension, ['mp4', 'webm', 'ogg', 'avi', 'mov'])) {
        $type = 'video';
    }
    
    // Add to media files
    $mediaFiles[] = [
        'path' => 'img/' . $file,
        'type' => $type,
        'name' => $file
    ];
}

// Sort files alphabetically
usort($mediaFiles, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Return JSON
echo json_encode($mediaFiles);
?>
