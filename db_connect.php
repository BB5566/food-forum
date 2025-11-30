<?php
/**
 * Food Forum 資料庫連線
 * 支援 SQLite 和 MySQL 雙模式
 * 優先使用 SQLite，方便本地開發與展示
 */

// 載入環境變數
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            $env[$key] = $value;
        }
    }
}

// 讀取資料庫類型，預設使用 SQLite
$dbType = $env['DB_TYPE'] ?? 'sqlite';

if ($dbType === 'sqlite') {
    // SQLite 連線設定
    $dbPath = $env['DB_PATH'] ?? __DIR__ . '/database/food_forum.sqlite';

    // 確保資料庫目錄存在
    $dbDir = dirname($dbPath);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    // 檢查資料庫是否存在
    $dbExists = file_exists($dbPath);

    try {
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // 啟用外鍵約束（SQLite 預設關閉）
        $pdo->exec('PRAGMA foreign_keys = ON;');

        // 如果是新資料庫，執行初始化
        if (!$dbExists) {
            $schemaFile = __DIR__ . '/database/schema.sqlite.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $pdo->exec($sql);
            }
        }

        // 建立相容的 mysqli wrapper（為了相容舊程式碼）
        $conn = new class($pdo) {
            private $pdo;
            public $connect_error = null;

            public function __construct($pdo) {
                $this->pdo = $pdo;
            }

            public function query($sql) {
                try {
                    $stmt = $this->pdo->query($sql);
                    return new class($stmt) {
                        private $stmt;
                        public $num_rows;

                        public function __construct($stmt) {
                            $this->stmt = $stmt;
                            $this->num_rows = $stmt->rowCount();
                        }

                        public function fetch_assoc() {
                            return $this->stmt->fetch(PDO::FETCH_ASSOC);
                        }

                        public function fetch_all($mode = MYSQLI_ASSOC) {
                            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                    };
                } catch (PDOException $e) {
                    return false;
                }
            }

            public function prepare($sql) {
                // 將 ? 佔位符轉換為 PDO 格式
                $stmt = $this->pdo->prepare($sql);
                return new class($stmt, $this->pdo) {
                    private $stmt;
                    private $pdo;
                    private $types = '';
                    private $params = [];
                    public $affected_rows = 0;
                    public $insert_id = 0;

                    public function __construct($stmt, $pdo) {
                        $this->stmt = $stmt;
                        $this->pdo = $pdo;
                    }

                    public function bind_param($types, &...$params) {
                        $this->types = $types;
                        $this->params = $params;
                        return true;
                    }

                    public function execute() {
                        try {
                            $result = $this->stmt->execute($this->params);
                            $this->affected_rows = $this->stmt->rowCount();
                            $this->insert_id = $this->pdo->lastInsertId();
                            return $result;
                        } catch (PDOException $e) {
                            return false;
                        }
                    }

                    public function get_result() {
                        return new class($this->stmt) {
                            private $stmt;
                            public $num_rows;

                            public function __construct($stmt) {
                                $this->stmt = $stmt;
                                $this->num_rows = $stmt->rowCount();
                            }

                            public function fetch_assoc() {
                                return $this->stmt->fetch(PDO::FETCH_ASSOC);
                            }

                            public function fetch_all($mode = MYSQLI_ASSOC) {
                                return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                            }
                        };
                    }

                    public function close() {
                        $this->stmt = null;
                    }
                };
            }

            public function real_escape_string($str) {
                // PDO 使用 prepared statements，這裡只是相容層
                return addslashes($str);
            }

            public function close() {
                $this->pdo = null;
            }

            public function __get($name) {
                if ($name === 'insert_id') {
                    return $this->pdo->lastInsertId();
                }
                if ($name === 'affected_rows') {
                    return 0;
                }
                return null;
            }
        };

    } catch (PDOException $e) {
        die('SQLite 資料庫連線失敗: ' . $e->getMessage());
    }

} else {
    // MySQL 連線設定（向下相容）
    $servername = $env['DB_SERVER'] ?? 'localhost';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';
    $dbname = $env['DB_NAME'] ?? 'food_forum';

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("資料庫連線失敗，請檢查設定：" . $conn->connect_error);
    }

    // 設定字元編碼
    $conn->set_charset("utf8mb4");
}
