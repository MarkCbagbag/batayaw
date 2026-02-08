<?php
/**
 * Database Connection Helper
 * Manages PDO connection to MySQL with environment variable support
 */

// Load .env file if exists
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') === false || strpos($line, '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

loadEnv();

class DB {
    private static $instance = null;
    private $pdo = null;

    private function __construct() {
        // Get config from env or use defaults
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = getenv('DB_PORT') ?: 3306;
        $dbName = getenv('DB_NAME') ?: 'girlfriend_surprise';
        $dbUser = getenv('DB_USER') ?: 'gf_user';
        $dbPass = getenv('DB_PASS') ?: 'gf_pass';

        // Support multiple connection attempts (Docker, local host, etc.)
        $hosts = [
            ['host' => 'db', 'port' => 3306, 'user' => $dbUser, 'pass' => $dbPass],
            ['host' => $dbHost, 'port' => $dbPort, 'user' => $dbUser, 'pass' => $dbPass],
            ['host' => 'localhost', 'port' => 3307, 'user' => $dbUser, 'pass' => $dbPass],
        ];

        $lastError = null;
        foreach ($hosts as $config) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $config['host'],
                    $config['port'],
                    $dbName
                );
                $this->pdo = new PDO(
                    $dsn,
                    $config['user'],
                    $config['pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                return; // Success
            } catch (PDOException $e) {
                $lastError = $e->getMessage();
            }
        }

        // If we get here, all connections failed
        throw new Exception("Database connection failed. Last error: $lastError");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }

    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}

// Helper function
function getDB() {
    try {
        return DB::getInstance();
    } catch (Exception $e) {
        // Fallback: log error and return null
        error_log("DB Error: " . $e->getMessage());
        return null;
    }
}

?>
