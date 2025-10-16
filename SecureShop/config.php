<?php

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Database configuration
$db_host = 'sql207.infinityfree.com';
$db_name = 'if0_40043171_shopdb';
$db_user = 'if0_40043171';
$db_pass = '6a55rmZjNc';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default user_id for IDOR simulation (Alice logged in)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Alice's ID
    $_SESSION['username'] = 'guest';
    $_SESSION['role'] = 'user';
}
?>