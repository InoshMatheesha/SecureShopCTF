# Moves sensitive/test files out of the web app into SecureShop/private for safe GitHub publishing
$srcUploads = "C:\Users\Lenovo\Documents\GitHub\SecureShopCTF\SecureShop\uploads"
$srcFiles = "C:\Users\Lenovo\Documents\GitHub\SecureShopCTF\SecureShop\files"
$dest = "C:\Users\Lenovo\Documents\GitHub\SecureShopCTF\SecureShop\private"

if (-not (Test-Path $dest)) {
    New-Item -ItemType Directory -Path $dest | Out-Null
}

Write-Host "Moving files from uploads to private..."
Get-ChildItem -Path $srcUploads -File -ErrorAction SilentlyContinue | ForEach-Object {
    $target = Join-Path $dest ($_.Name)
    Move-Item -Path $_.FullName -Destination $target -Force
    Write-Host "Moved: $($_.Name)"
}

Write-Host "Moving files from files to private..."
Get-ChildItem -Path $srcFiles -File -ErrorAction SilentlyContinue | ForEach-Object {
    $target = Join-Path $dest ($_.Name)
    Move-Item -Path $_.FullName -Destination $target -Force
    Write-Host "Moved: $($_.Name)"
}

Write-Host "Cleanup done. Check SecureShop/private. Ensure .gitignore is committed so these files are not pushed."
