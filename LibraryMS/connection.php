<?php
// connection.php
// --- تنظیمات اتصال به SQL Server ---
// حتماً این مقادیر را با مقادیر محیط خودت جایگزین کن:
$server   = "DESKTOP-R0UAL3E";   // یا "localhost\\SQLEXPRESS"
$database = "Library_dbms_U";
$username = ""; // اگر از SQL Auth استفاده می‌کنی مقدار بده، در غیر اینصورت خالی بگذار
$password = ""; // پسورد اگر لازم باشد

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// PDO SQL Server DSN
try {
    $dsn = "sqlsrv:Server=$server;Database=$database";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // در محیط توسعه پیام خطا را نمایش می‌دهیم
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection error: ' . $e->getMessage()]);
    exit;
}
