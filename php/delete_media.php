<?php
/**
 * Delete media file in img/ folder
 * Expects JSON body: { "name": "filename.ext" }
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];

// Accept either single name or array of names
$names = [];
if (!empty($body['names']) && is_array($body['names'])) {
    $names = $body['names'];
} elseif (!empty($body['name'])) {
    $names = [$body['name']];
}

if (empty($names)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing file name(s)']);
    exit;
}

$allowedExt = ['jpg','jpeg','png','gif','webp','mp4','webm','ogg'];
$results = ['deleted' => [], 'failed' => []];

foreach ($names as $n) {
    $name = basename($n);
    if ($name !== $n) {
        $results['failed'][] = ['name' => $n, 'error' => 'Invalid file name'];
        continue;
    }

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        $results['failed'][] = ['name' => $name, 'error' => 'File type not allowed'];
        continue;
    }

    $filePath = __DIR__ . '/../img/' . $name;
    if (!file_exists($filePath)) {
        $results['failed'][] = ['name' => $name, 'error' => 'File not found'];
        continue;
    }

    if (!is_writable($filePath)) @chmod($filePath, 0644);

    if (!@unlink($filePath)) {
        $results['failed'][] = ['name' => $name, 'error' => 'Failed to delete'];
        continue;
    }

    $results['deleted'][] = $name;
}

echo json_encode(['success' => true, 'results' => $results]);

?>
