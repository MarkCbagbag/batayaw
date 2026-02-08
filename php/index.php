<?php
/**
 * PHP API Documentation & Backend Index
 * Access point for all PHP backend services
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Girlfriend Surprise - Backend API</title>
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
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        
        .card h2 {
            color: #ff6b9d;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .endpoints {
            background: #f8f9fa;
            border-left: 4px solid #ff6b9d;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #ff6b9d 0%, #feca57 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: opacity 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }
        
        .status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .status-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #27ae60;
        }
        
        .status-item.warning {
            border-left-color: #f39c12;
        }
        
        .status-item.error {
            border-left-color: #e74c3c;
        }
        
        .status-item strong {
            color: #333;
        }
        
        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            opacity: 0.8;
        }
        
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💕 Backend API</h1>
            <p>Girlfriend Surprise - Server-Side Services</p>
        </div>
        
        <div class="grid">
            <!-- API Documentation -->
            <div class="card">
                <h2>📡 API Endpoints</h2>
                <p>Access backend services and data</p>
                
                <div class="endpoints">GET /api.php?endpoint=status
GET /api.php?endpoint=stats
GET /api.php?endpoint=health
GET /api.php?endpoint=info</div>
                
                <a href="api.php?endpoint=status" class="btn">View Status</a>
                <a href="api.php?endpoint=health" class="btn" style="background: linear-gradient(135deg, #48a9a6 0%, #5dba9e 100%); margin-left: 10px;">Health Check</a>
            </div>
            
            <!-- Admin Dashboard -->
            <div class="card">
                <h2>👨‍💼 Admin Dashboard</h2>
                <p>Manage all application data and settings</p>
                
                <p style="font-size: 12px; color: #999; margin-bottom: 15px;">
                    View messages, forms, game logs, and media files
                </p>
                
                <a href="admin.php" class="btn">Access Dashboard</a>
                <a href="admin.php?section=logs" class="btn" style="background: linear-gradient(135deg, #48a9a6 0%, #5dba9e 100%); margin-left: 10px;">View Logs</a>
            </div>
            
            <!-- Form Handler -->
            <div class="card">
                <h2>📋 Form Handler</h2>
                <p>Process and store form submissions</p>
                
                <div class="endpoints">POST /form_handler.php
Methods:
- contact
- message
- suggestion
- feedback</div>
                
                <a href="form_handler.php" class="btn">Documentation</a>
            </div>
            
            <!-- Message System -->
            <div class="card">
                <h2>💌 Messages</h2>
                <p>Store and retrieve love messages</p>
                
                <div class="endpoints">GET  /messages.php
POST /messages.php
DELETE /messages.php?id=xxx</div>
                
                <a href="messages.php" class="btn">View Messages</a>
            </div>
            
            <!-- Email Service -->
            <div class="card">
                <h2>📧 Email Service</h2>
                <p>Send romantic emails</p>
                
                <div class="endpoints">POST /send_email.php
Parameters:
- to (email)
- subject
- message</div>
                
                <a href="send_email.php" class="btn">Test Email</a>
            </div>
            
            <!-- Game Logger -->
            <div class="card">
                <h2>🎮 Game Logger</h2>
                <p>Track game statistics and scores</p>
                
                <div class="endpoints">GET  /logger.php
POST /logger.php (log score)
DELETE /logger.php?action=clear</div>
                
                <a href="logger.php" class="btn">View Logs</a>
            </div>
        </div>
        
        <!-- System Status -->
        <div style="background: white; border-radius: 10px; padding: 30px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
            <h2 style="color: #ff6b9d; margin-bottom: 20px; font-size: 24px;">🔍 System Status</h2>
            
            <div class="status">
                <div class="status-item">
                    <strong>PHP Version:</strong><br>
                    <?php echo phpversion(); ?>
                </div>
                
                <div class="status-item">
                    <strong>Server Software:</strong><br>
                    <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                </div>
                
                <div class="status-item<?php echo is_writable(getDataDir()) ? '' : ' error'; ?>">
                    <strong>Data Directory:</strong><br>
                    <?php echo getDataDir(); ?>
                    <br><small><?php echo is_writable(getDataDir()) ? '✓ Writable' : '✗ Not Writable'; ?></small>
                </div>
                
                <div class="status-item<?php echo extension_loaded('json') ? '' : ' error'; ?>">
                    <strong>JSON Extension:</strong><br>
                    <?php echo extension_loaded('json') ? '✓ Enabled' : '✗ Disabled'; ?>
                </div>
            </div>
            
            <h3 style="color: #333; margin-top: 20px; margin-bottom: 15px;">Helper Functions</h3>
            <p style="color: #666; margin-bottom: 15px;">
                Use the helper functions from <code>helpers.php</code> and <code>database.php</code>:
            </p>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #ff6b9d; overflow-x: auto;">
                <pre style="font-family: monospace; font-size: 12px; color: #666;">// Read data
$data = readData('filename.json');

// Add record
$record = addRecord('filename.json', $data_array);

// Update record
updateRecord('filename.json', $id, $updates);

// Delete record
deleteRecord('filename.json', $id);

// Filter records
$results = filterRecords('filename.json', $criteria);

// Get statistics
$stats = getDataStatistics('filename.json');</pre>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
            <h2 style="color: #ff6b9d; margin-bottom: 20px; font-size: 24px;">🔗 Quick Links</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="../index.php" class="btn" style="text-align: center;">← Back to Website</a>
                <a href="admin.php?section=dashboard" class="btn" style="text-align: center; background: linear-gradient(135deg, #48a9a6 0%, #5dba9e 100%);"><strong style="display:block;">📊</strong> Dashboard</a>
                <a href="api.php?endpoint=stats" class="btn" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><strong style="display:block;">📈</strong> Statistics</a>
                <a href="admin.php?section=media" class="btn" style="text-align: center; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"><strong style="display:block;">🖼️</strong> Media</a>
            </div>
        </div>
        
        <div class="footer">
            <p>💕 Girlfriend Surprise Backend | <?php echo date('Y'); ?> | All Systems Operational</p>
        </div>
    </div>
</body>
</html>

<?php

/**
 * Helper function to get data directory
 */
function getDataDir() {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

?>
