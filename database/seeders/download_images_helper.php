<?php
/**
 * Image Download Helper Script
 * 
 * This script helps download product images for the ceramic demo seeder.
 * 
 * Usage:
 * 1. Update the $imageUrls array below with actual RAK Ceramics product image URLs
 * 2. Run: php download_images_helper.php
 * 3. Images will be saved to storage/app/public/product/
 * 
 * Note: You need to manually find the image URLs from the RAK Ceramics website
 */

// Product codes and their corresponding image URLs
// Update these with actual URLs from https://www.rakceramics.com/ksa/ar/
$imageUrls = [
    'CFT-001' => '', // Premium White Ceramic Floor Tile 60x60
    'PFT-002' => '', // Elegant Marble Look Porcelain Tile 80x80
    'WWT-003' => '', // Modern Geometric Wall Tile 30x60
    'BMT-004' => '', // Bathroom Mosaic Tile Collection
    'KBT-005' => '', // Kitchen Backsplash Ceramic Tile 25x40
    'WLT-006' => '', // Premium Wood Look Porcelain Tile 20x120
    'BFT-007' => '', // Glossy Black Ceramic Floor Tile 50x50
    'DPT-008' => '', // Decorative Pattern Wall Tile 40x40
    'ASB-009' => '', // Anti-Slip Bathroom Floor Tile 30x30
    'SLT-010' => '', // Premium Stone Look Porcelain Tile 60x120
    'SWT-011' => '', // Classic Subway Wall Tile 7.5x15
    'GAM-012' => '', // Luxury Gold Accent Mosaic Tile
    'TFT-013' => '', // Rustic Terracotta Floor Tile 40x40
    'HWT-014' => '', // Modern Hexagon Wall Tile 20x23
    'WBT-015' => '', // Premium Glossy White Bathroom Tile 30x60
    'CLT-016' => '', // Industrial Concrete Look Porcelain Tile 60x60
    'BFT-017' => '', // Elegant Beige Ceramic Floor Tile 50x50
    'FPT-018' => '', // Artistic Floral Pattern Wall Tile 25x40
    'PPT-019' => '', // Premium Non-Slip Pool Tile 15x15
    'MLT-020' => '', // Luxury Marble Effect Porcelain Tile 120x120
];

// Directories
$baseDir = __DIR__ . '/../../storage/app/public/product/';
$thumbnailDir = $baseDir . 'thumbnail/';
$metaDir = $baseDir . 'meta/';

// Create directories if they don't exist
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}
if (!is_dir($thumbnailDir)) {
    mkdir($thumbnailDir, 0755, true);
}
if (!is_dir($metaDir)) {
    mkdir($metaDir, 0755, true);
}

function downloadImage($url, $savePath) {
    if (empty($url)) {
        echo "Skipping empty URL\n";
        return false;
    }
    
    $ch = curl_init($url);
    $fp = fopen($savePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if ($httpCode == 200 && file_exists($savePath)) {
        echo "✓ Downloaded: $savePath\n";
        return true;
    } else {
        echo "✗ Failed to download: $url (HTTP $httpCode)\n";
        if (file_exists($savePath)) {
            unlink($savePath);
        }
        return false;
    }
}

function resizeImage($sourcePath, $targetPath, $maxWidth = 300) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    // Check if GD is available
    if (!function_exists('imagecreatefromjpeg')) {
        echo "GD library not available. Skipping thumbnail creation.\n";
        copy($sourcePath, $targetPath);
        return true;
    }
    
    $info = getimagesize($sourcePath);
    if (!$info) {
        copy($sourcePath, $targetPath);
        return true;
    }
    
    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];
    
    if ($width <= $maxWidth) {
        copy($sourcePath, $targetPath);
        return true;
    }
    
    $ratio = $maxWidth / $width;
    $newWidth = $maxWidth;
    $newHeight = $height * $ratio;
    
    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $source = imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            copy($sourcePath, $targetPath);
            return true;
    }
    
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    $extension = pathinfo($targetPath, PATHINFO_EXTENSION);
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($thumb, $targetPath, 90);
            break;
        case 'png':
            imagepng($thumb, $targetPath);
            break;
        case 'webp':
            imagewebp($thumb, $targetPath, 90);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($thumb);
    
    return true;
}

echo "Starting image download process...\n\n";

foreach ($imageUrls as $code => $url) {
    $codeLower = strtolower($code);
    $imagePath = $baseDir . $codeLower . '.webp';
    $thumbPath = $thumbnailDir . $codeLower . '-thumb.webp';
    $metaPath = $metaDir . $codeLower . '-thumb.webp';
    
    echo "Processing $code...\n";
    
    if (!empty($url)) {
        // Download main image
        if (downloadImage($url, $imagePath)) {
            // Create thumbnail
            resizeImage($imagePath, $thumbPath, 300);
            // Copy to meta directory
            if (file_exists($thumbPath)) {
                copy($thumbPath, $metaPath);
            }
        }
    } else {
        echo "  No URL provided. Skipping.\n";
    }
    
    echo "\n";
}

echo "Done! Images saved to: $baseDir\n";
echo "To use these images, make sure they exist before running the seeder.\n";
