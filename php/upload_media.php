<?php
/**
 * Simple media upload handler
 * Accepts multipart/form-data file uploads and saves into img/ folder
 */

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
    exit;
}

if (empty($_FILES['media'])) {
    http_response_code(400);
    $err = isset($_SERVER['CONTENT_LENGTH']) ? 'No file uploaded (check php.ini upload_max_filesize/post_max_size).' : 'No file uploaded.';
    echo json_encode(['success' => false, 'error' => $err, 'debug' => ['content_length' => $_SERVER['CONTENT_LENGTH'] ?? null]]);
    exit;
}

$file = $_FILES['media'];

// Basic validation
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    $code = $file['error'];
    $msg = 'Upload error code: ' . $code;
    // Provide helpful hints for common PHP upload errors
    if ($code === UPLOAD_ERR_INI_SIZE || $code === UPLOAD_ERR_FORM_SIZE) {
        $msg .= ' (file too large; check upload_max_filesize/post_max_size in php.ini)';
    } elseif ($code === UPLOAD_ERR_PARTIAL) {
        $msg .= ' (file only partially uploaded)';
    } elseif ($code === UPLOAD_ERR_NO_FILE) {
        $msg .= ' (no file was uploaded)';
    }
    echo json_encode(['success' => false, 'error' => $msg, 'debug' => $file]);
    exit;
}

$allowedExt = ['jpg','jpeg','png','gif','webp','mp4','webm','ogg'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExt)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File type not allowed', 'debug' => ['ext' => $ext]]);
    exit;
}

$targetDir = __DIR__ . '/../img/';
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create img directory', 'debug' => ['targetDir' => $targetDir]]);
        exit;
    }
}

// Avoid overwriting: prefix timestamp
$safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($file['name']));
$targetPath = $targetDir . time() . '_' . $safeName;

if (!is_uploaded_file($file['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Temporary file not found or invalid upload', 'debug' => $file]);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Try an alternative copy as a fallback
    if (!copy($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to move or copy uploaded file', 'debug' => ['tmp_name' => $file['tmp_name'], 'target' => $targetPath]]);
        exit;
    }
}

// Attempt to set permissive permissions so the web server can serve the file
@chmod($targetPath, 0644);

echo json_encode(['success' => true, 'message' => 'File uploaded', 'path' => 'img/' . basename($targetPath), 'debug' => ['targetPath' => $targetPath]]);

?>
