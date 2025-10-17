<?php
// auth.php
session_start();
require_once "connection.php";

// simple CSRF check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch');
    }
}

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$fullname || !$email || !$username || !$password) {
        $_SESSION['error'] = "Please fill all fields";
        header("Location: index.php");
        exit;
    }

    // check username/email exists
    $check = db_query($conn, "SELECT UserID FROM Users WHERE Username = ? OR Email = ?", [$username, $email]);
    if ($check && sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC)) {
        $_SESSION['error'] = "Username or email already exists";
        header("Location: index.php");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Users (FullName, Email, Username, PasswordHash, Role) VALUES (?, ?, ?, ?, 'user')";
    $res = db_query($conn, $sql, [$fullname, $email, $username, $hash]);
    if ($res) {
        $_SESSION['success'] = "Account created. Please login.";
    } else {
        $_SESSION['error'] = "Registration failed.";
    }
    header("Location: index.php");
    exit;
}

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $_SESSION['error'] = "Please fill all fields";
        header("Location: index.php");
        exit;
    }

    $stmt = db_query($conn, "SELECT UserID, Username, FullName, PasswordHash FROM Users WHERE Username = ?", [$username]);
    if ($stmt) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row && password_verify($password, $row['PasswordHash'])) {
            // logged in
            $_SESSION['user'] = $row['Username'];
            $_SESSION['fullname'] = $row['FullName'];
            $_SESSION['userid'] = $row['UserID'];
            $_SESSION['success'] = "Welcome, " . $row['FullName'];
            header("Location: index.php");
            exit;
        }
    }
    $_SESSION['error'] = "Invalid credentials";
    header("Location: index.php");
    exit;
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
