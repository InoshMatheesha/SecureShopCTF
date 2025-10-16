<?php
/**
 * Image Helper - Automatically finds images regardless of case
 */

function getProductImagePath($productName) {
    $imageDir = __DIR__ . '/images/';
    
    // Define possible image mappings
    $imageMap = [
        'Gaming Laptop Pro' => 'Gaming Laptop Pro',
        'Mechanical Gaming Keyboard' => 'Mechanical Gaming Keyboard',
        'Gaming Mouse Ultra' => 'Gaming Mouse Ultra',
        'Gaming Headset Pro' => 'Gaming Headset Pro',
        'Graphics Card RTX' => 'Graphics Card RTX.',
        '4K Gaming Monitor' => '4K Gaming Monitor'
    ];
    
    // Get base filename
    $baseFilename = $imageMap[$productName] ?? $productName;
    
    // Try different extensions
    $extensions = ['.png', '.jpg', '.jpeg', '.webp', '.gif'];
    
    foreach ($extensions as $ext) {
        $filename = $baseFilename . $ext;
        $fullPath = $imageDir . $filename;
        
        if (file_exists($fullPath)) {
            return 'images/' . $filename;
        }
    }
    
    // Fallback
    return 'images/Welcome.jpg';
}

function imageExists($path) {
    return file_exists(__DIR__ . '/' . $path);
}
?>
