<?php
// Simple test file to check what's wrong
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing Connection...</h1>";

// Test 1: PHP is working
echo "<p>✅ PHP is working!</p>";

// Test 2: Database connection
try {
    $db_host = 'sql207.infinityfree.com';
    $db_name = 'if0_40043171_shopdb';
    $db_user = 'if0_40043171';
    $db_pass = '6a55rmZjNc';
    
    echo "<p>🔄 Attempting database connection...</p>";
    
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connected successfully!</p>";
    
    // Test 3: Check if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Products table exists</p>";
        
        // Test 4: Try to fetch products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $count = $stmt->fetch();
        echo "<p>✅ Found {$count['count']} products in database</p>";
    } else {
        echo "<p>❌ Products table does NOT exist!</p>";
        echo "<p>🔧 You need to run the database setup SQL</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}

// Test 5: Check PHP version
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 6: Check if sessions work
session_start();
$_SESSION['test'] = 'working';
echo "<p>✅ Sessions working</p>";

echo "<hr>";
echo "<h2>Diagnosis:</h2>";
echo "<p>If you see this page, PHP is working. Check the messages above to see what's failing.</p>";
?>
