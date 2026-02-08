<?php
/**
 * Admin Dashboard
 * View and manage all application data
 */

require_once 'config.php';
require_once 'helpers.php';
require_once 'database.php';

// Set headers
header('Content-Type: text/html; charset=utf-8');

// Simple auth (should be enhanced in production)
session_start();

$action = $_GET['action'] ?? 'dashboard';
$section = $_GET['section'] ?? '';
$page = $_GET['page'] ?? 1;

// Build data path
$data_dir = getDataDir();

function renderHeader() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Girlfriend Surprise - Admin Dashboard</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
                overflow: hidden;
            }
            
            .header {
                background: linear-gradient(135deg, #ff6b9d 0%, #feca57 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 32px;
                margin-bottom: 5px;
            }
            
            .header p {
                opacity: 0.9;
            }
            
            .nav {
                display: flex;
                background: #f8f9fa;
                border-bottom: 1px solid #ddd;
                flex-wrap: wrap;
            }
            
            .nav a {
                flex: 1;
                padding: 15px 20px;
                text-decoration: none;
                color: #333;
                border-right: 1px solid #ddd;
                transition: background 0.3s;
                text-align: center;
                cursor: pointer;
            }
            
            .nav a:hover {
                background: #e9ecef;
            }
            
            .nav a.active {
                background: #ff6b9d;
                color: white;
            }
            
            .content {
                padding: 30px;
            }
            
            .section-title {
                font-size: 24px;
                margin-bottom: 20px;
                color: #333;
                border-bottom: 2px solid #ff6b9d;
                padding-bottom: 10px;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .stat-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
            }
            
            .stat-card .number {
                font-size: 36px;
                font-weight: bold;
                margin: 10px 0;
            }
            
            .stat-card .label {
                opacity: 0.9;
                font-size: 14px;
            }
            
            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            
            .data-table thead {
                background: #f8f9fa;
            }
            
            .data-table th {
                padding: 15px;
                text-align: left;
                color: #333;
                font-weight: 600;
                border-bottom: 2px solid #ddd;
            }
            
            .data-table td {
                padding: 12px 15px;
                border-bottom: 1px solid #ddd;
            }
            
            .data-table tr:hover {
                background: #f8f9fa;
            }
            
            .btn {
                display: inline-block;
                padding: 8px 16px;
                background: #ff6b9d;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
                font-size: 14px;
                transition: background 0.3s;
            }
            
            .btn:hover {
                background: #ff5a8c;
            }
            
            .btn-danger {
                background: #e74c3c;
            }
            
            .btn-danger:hover {
                background: #c0392b;
            }
            
            .btn-success {
                background: #27ae60;
            }
            
            .btn-success:hover {
                background: #229954;
            }
            
            .empty-state {
                text-align: center;
                padding: 40px;
                color: #999;
            }
            
            .empty-state svg {
                width: 80px;
                height: 80px;
                margin-bottom: 20px;
                opacity: 0.5;
            }
            
            .action-buttons {
                margin-top: 20px;
                display: flex;
                gap: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>💕 Admin Dashboard</h1>
                <p>Girlfriend Surprise - Control Center</p>
            </div>
    <?php
}

function renderNav($active_section) {
    $sections = [
        'dashboard' => '📊 Dashboard',
        'messages' => '💌 Messages',
        'forms' => '📝 Forms',
        'logs' => '📋 Logs',
        'media' => '🖼️ Media',
        'settings' => '⚙️ Settings'
    ];
    
    echo '<div class="nav">';
    foreach ($sections as $key => $label) {
        $active = ($active_section === $key) ? 'active' : '';
        echo "<a href='?action=admin&section=$key' class='$active'>$label</a>";
    }
    echo '</div>';
}

function renderDashboard() {
    $data_dir = getDataDir();
    ?>
    <div class="content">
        <h2 class="section-title">📊 System Dashboard</h2>
        
        <div class="stats-grid">
            <?php
            // Messages count
            $messages = readData('messages.json');
            ?>
            <div class="stat-card">
                <div class="label">Total Messages</div>
                <div class="number"><?php echo count($messages); ?></div>
            </div>
            
            <?php
            // Forms count
            $forms = readData('form_submissions.json');
            ?>
            <div class="stat-card">
                <div class="label">Form Submissions</div>
                <div class="number"><?php echo count($forms); ?></div>
            </div>
            
            <?php
            // Game logs count
            $logs = readData('game_logs.json');
            ?>
            <div class="stat-card">
                <div class="label">Game Sessions</div>
                <div class="number"><?php echo count($logs); ?></div>
            </div>
            
            <?php
            // Total files in data
            $files = glob($data_dir . '/*.json');
            ?>
            <div class="stat-card">
                <div class="label">Data Files</div>
                <div class="number"><?php echo count($files); ?></div>
            </div>
        </div>
        
        <h3 style="margin-top: 30px; margin-bottom: 20px; color: #333;">📈 Recent Activity</h3>
        
        <p style="color: #666; margin-bottom: 20px;">
            ✓ System is operational<br>
            ✓ Data directory: <code><?php echo getDataDir(); ?></code><br>
            ✓ PHP Version: <?php echo phpversion(); ?><br>
            ✓ All services active
        </p>
        
        <div class="action-buttons">
            <a href="?action=admin&section=messages" class="btn">View Messages</a>
            <a href="?action=admin&section=forms" class="btn">View Forms</a>
            <a href="?action=admin&section=logs" class="btn">View Logs</a>
        </div>
    </div>
    <?php
}

function renderMessages() {
    $messages = readData('messages.json');
    ?>
    <div class="content">
        <h2 class="section-title">💌 Messages</h2>
        <div style="margin-bottom:16px;">
            <form id="adminAddMessageForm" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <input type="text" id="adminMsgSender" placeholder="Sender" style="padding:8px;" />
                <input type="text" id="adminMsgText" placeholder="Message" style="padding:8px;flex:1;" />
                <button class="btn" type="button" id="adminMsgSend">Add</button>
                <span id="adminMsgStatus" style="margin-left:8px;color:#666;font-size:0.95rem"></span>
            </form>
        </div>

        <?php if (empty($messages)) : ?>
            <div class="empty-state">
                <p>No messages yet</p>
            </div>
        <?php else : ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Sender</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg) : ?>
                    <tr>
                        <td><?php echo substr($msg['id'] ?? 'N/A', 0, 8); ?></td>
                        <td><?php echo substr(sanitize($msg['message'] ?? ''), 0, 50) . '...'; ?></td>
                        <td><?php echo sanitize($msg['sender'] ?? 'Anonymous'); ?></td>
                        <td><?php echo formatDate($msg['created_at'] ?? ''); ?></td>
                        <td><a href="#" class="btn btn-danger">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <script>
            (function(){
                const btn = document.getElementById('adminMsgSend');
                const status = document.getElementById('adminMsgStatus');
                if (!btn) return;
                btn.addEventListener('click', function(){
                    const sender = document.getElementById('adminMsgSender').value || 'Admin';
                    const message = document.getElementById('adminMsgText').value || '';
                    if (!message.trim()) { status.textContent = 'Enter a message'; return; }
                    status.textContent = 'Saving...';
                    fetch('messages.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ sender: sender, message: message }) })
                        .then(r=>r.json()).then(d=>{ if (d.success) { status.textContent='Saved'; setTimeout(()=>location.reload(),600); } else { status.textContent=d.error||'Error'; } })
                        .catch(e=>{ console.error(e); status.textContent='Error'; });
                });
            })();
        </script>
    </div>
    <?php
}

function renderForms() {
    $forms = readData('form_submissions.json');
    ?>
    <div class="content">
        <h2 class="section-title">📝 Form Submissions</h2>
        
        <?php if (empty($forms)) : ?>
            <div class="empty-state">
                <p>No form submissions yet</p>
            </div>
        <?php else : ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Content</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forms as $form) : ?>
                    <tr>
                        <td><?php echo sanitize($form['type'] ?? 'N/A'); ?></td>
                        <td>
                            <?php
                            if (isset($form['message'])) {
                                echo substr(sanitize($form['message']), 0, 50) . '...';
                            } elseif (isset($form['suggestion'])) {
                                echo substr(sanitize($form['suggestion']), 0, 50) . '...';
                            } elseif (isset($form['feedback'])) {
                                echo substr(sanitize($form['feedback']), 0, 50) . '...';
                            }
                            ?>
                        </td>
                        <td><?php echo formatDate($form['created_at'] ?? ''); ?></td>
                        <td><a href="#" class="btn btn-danger">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

function renderLogs() {
    $logs = readData('game_logs.json');
    ?>
    <div class="content">
        <h2 class="section-title">📋 Game Logs</h2>
        
        <?php if (empty($logs)) : ?>
            <div class="empty-state">
                <p>No game logs yet</p>
            </div>
        <?php else : ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Score</th>
                        <th>Time</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo ucfirst(str_replace('_', ' ', $log['game'] ?? 'N/A')); ?></td>
                        <td><?php echo $log['score'] ?? 'N/A'; ?></td>
                        <td><?php echo ($log['time_taken'] ?? 0) . 's'; ?></td>
                        <td><?php echo formatDate($log['timestamp'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

function renderMedia() {
    $img_dir = __DIR__ . '/../img';
    $media_files = [];
    
    if (is_dir($img_dir)) {
        $files = scandir($img_dir);
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm'];
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $extensions)) {
                    $media_files[] = $file;
                }
            }
        }
    }
    ?>
    <div class="content">
        <h2 class="section-title">🖼️ Media Files</h2>
        <div style="margin-bottom:12px;">
            <form id="adminUploadForm" enctype="multipart/form-data" method="post" action="upload_media.php">
                <input type="file" name="media" accept="image/*,video/*" />
                <button class="btn" type="submit">Upload</button>
                <span style="margin-left:8px;color:#666;font-size:0.95rem">Upload directly to img/ folder</span>
            </form>
        </div>

        <?php if (empty($media_files)) : ?>
            <div class="empty-state">
                <p>No media files found</p>
                <p style="font-size: 12px; margin-top: 10px;">Upload photos and videos to the img/ folder</p>
            </div>
        <?php else : ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($media_files as $file) : ?>
                    <tr>
                        <td><?php echo sanitize($file); ?></td>
                        <td><?php echo strtoupper(pathinfo($file, PATHINFO_EXTENSION)); ?></td>
                        <td><?php echo formatBytes(filesize($img_dir . '/' . $file)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

function renderSettings() {
    ?>
    <div class="content">
        <h2 class="section-title">⚙️ Settings</h2>
        
        <h3 style="margin: 20px 0 15px; color: #333;">System Information</h3>
        
        <table class="data-table">
            <tr>
                <td><strong>PHP Version</strong></td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td><strong>Data Directory</strong></td>
                <td><?php echo getDataDir(); ?></td>
            </tr>
            <tr>
                <td><strong>Server OS</strong></td>
                <td><?php echo PHP_OS; ?></td>
            </tr>
            <tr>
                <td><strong>Loaded Extensions</strong></td>
                <td><?php echo count(get_loaded_extensions()); ?> extensions</td>
            </tr>
        </table>
        
        <h3 style="margin: 30px 0 15px; color: #333;">Configuration</h3>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; overflow-x: auto;">
            <pre><?php echo json_encode(getConfig(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></pre>
        </div>
        
        <div class="action-buttons" style="margin-top: 30px;">
            <button class="btn btn-success">Save Settings</button>
            <button class="btn btn-danger">Clear Cache</button>
        </div>
    </div>
    <?php
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Render page
renderHeader();
renderNav($section);

switch ($section) {
    case 'messages':
        renderMessages();
        break;
    case 'forms':
        renderForms();
        break;
    case 'logs':
        renderLogs();
        break;
    case 'media':
        renderMedia();
        break;
    case 'settings':
        renderSettings();
        break;
    default:
        renderDashboard();
}
?>
            </div>
        </div>
    </body>
    </html>
