<?php
// Test if images are loading correctly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Image Test Page</h1>";

$productImages = [
    'Gaming Laptop Pro' => 'Gaming Laptop Pro.png',
    'Mechanical Gaming Keyboard' => 'Mechanical Gaming Keyboard.png',
    'Gaming Mouse Ultra' => 'Gaming Mouse Ultra.png',
    'Gaming Headset Pro' => 'Gaming Headset Pro.png',
    'Graphics Card RTX' => 'Graphics Card RTX..png',
    '4K Gaming Monitor' => '4K Gaming Monitor.png'
];

echo "<h2>Testing Image Paths:</h2>";
echo "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; padding: 2rem;'>";

foreach ($productImages as $name => $filename) {
    $imagePath = 'images/' . $filename;
    $fullPath = __DIR__ . '/' . $imagePath;
    
    echo "<div style='border: 1px solid #ccc; padding: 1rem; border-radius: 8px;'>";
    echo "<h3>$name</h3>";
    echo "<p><strong>Path:</strong> $imagePath</p>";
    echo "<p><strong>File exists:</strong> " . (file_exists($fullPath) ? '✅ YES' : '❌ NO') . "</p>";
    
    if (file_exists($fullPath)) {
        echo "<img src='$imagePath' alt='$name' style='width: 100%; height: 200px; object-fit: contain; background: #f0f0f0; padding: 1rem; border-radius: 8px;'>";
    } else {
        echo "<p style='color: red;'>File not found: $fullPath</p>";
    }
    
    echo "</div>";
}

echo "</div>";

// Test Welcome image
echo "<h2>Welcome Image Test:</h2>";
$welcomePath = 'images/Welcome.jpg';
$welcomeFullPath = __DIR__ . '/' . $welcomePath;
echo "<p><strong>Path:</strong> $welcomePath</p>";
echo "<p><strong>File exists:</strong> " . (file_exists($welcomeFullPath) ? '✅ YES' : '❌ NO') . "</p>";
if (file_exists($welcomeFullPath)) {
    echo "<img src='$welcomePath' alt='Welcome' style='max-width: 600px; border-radius: 8px;'>";
}
?>
