<?php
/**
 * Cache Busting Helper
 * Provides dynamic version numbers for assets to prevent browser caching issues
 */

// Static version numbers for assets (increment when assets change)
$assetVersions = [
    'css' => '20251002',
    'js' => '20251002', 
    'images' => '20251002',
    'logo' => '20251002'
];

/**
 * Get versioned asset URL
 * @param string $assetPath - The path to the asset
 * @param string $type - The type of asset (css, js, images, logo)
 * @return string - The asset path with version query string
 */
function getVersionedAsset($assetPath, $type = 'images') {
    global $assetVersions;
    
    $version = isset($assetVersions[$type]) ? $assetVersions[$type] : '1';
    
    // Check if the file has been modified (for dynamic versioning)
    if (file_exists($assetPath)) {
        $fileTime = filemtime($assetPath);
        $version = date('Ymd', $fileTime);
    }
    
    return $assetPath . '?v=' . $version;
}

/**
 * Get static versioned asset URL (faster, uses predefined versions)
 * @param string $assetPath - The path to the asset  
 * @param string $type - The type of asset (css, js, images, logo)
 * @return string - The asset path with version query string
 */
function getStaticVersionedAsset($assetPath, $type = 'images') {
    global $assetVersions;
    
    $version = isset($assetVersions[$type]) ? $assetVersions[$type] : '20251002';
    return $assetPath . '?v=' . $version;
}

/**
 * Update asset version (call this when you update assets)
 * @param string $type - The type of asset to update version for
 * @param string $newVersion - The new version string (optional, defaults to current date)
 */
function updateAssetVersion($type, $newVersion = null) {
    global $assetVersions;
    
    if ($newVersion === null) {
        $newVersion = date('Ymd');
    }
    
    $assetVersions[$type] = $newVersion;
    
    // You could save this to a file or database for persistence
    // For now, it's just in memory during the request
}

// Usage examples:
// echo getVersionedAsset('assets/logo.png', 'logo');
// echo getStaticVersionedAsset('styles.css', 'css'); 
// echo getVersionedAsset('js/theme.js', 'js');
?>