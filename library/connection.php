<?php
// connection.php
// Configure these values for your environment:
$server   = "DESKTOP-R0UAL3E"; // or "localhost\\SQLEXPRESS"
$database = "Library_dbms_U";
$username = ""; // SQL Server username (or leave empty if using windows auth properly configured)
$password = ""; // SQL Server password

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
];

try {
    $dsn = "sqlsrv:Server=$server;Database=$database";
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection error: ' . $e->getMessage()]);
    exit;
}
