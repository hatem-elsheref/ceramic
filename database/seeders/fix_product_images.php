<?php
/**
 * Fix Product Images Script
 * 
 * This script checks product images and ensures they exist or uses placeholders
 * 
 * Usage: php database/seeders/fix_product_images.php
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

echo "Checking product images...\n\n";

$products = Product::all();
$fixed = 0;
$missing = 0;

// Check if default image exists
$defaultImage = 'def.webp';
$defaultImagePath = storage_path('app/public/product/' . $defaultImage);

// Create default placeholder if it doesn't exist
if (!file_exists($defaultImagePath)) {
    // Try to find any existing image to use as default
    $existingImages = glob(storage_path('app/public/product/*.webp'));
    if (!empty($existingImages)) {
        $defaultImage = basename($existingImages[0]);
        echo "Using existing image as default: $defaultImage\n\n";
    } else {
        echo "⚠ No default image found. Products will use 'def.webp' as placeholder.\n";
        echo "You may need to upload a default image manually.\n\n";
    }
}

foreach ($products as $product) {
    $needsUpdate = false;
    $updateData = [];
    
    // Check thumbnail
    $thumbnail = $product->thumbnail;
    if ($thumbnail) {
        $thumbPath = 'product/thumbnail/' . $thumbnail;
        if (!Storage::disk('public')->exists($thumbPath)) {
            echo "✗ Missing thumbnail for product {$product->code}: $thumbnail\n";
            $updateData['thumbnail'] = $defaultImage;
            $needsUpdate = true;
        }
    } else {
        $updateData['thumbnail'] = $defaultImage;
        $needsUpdate = true;
    }
    
    // Check images
    $images = json_decode($product->images, true);
    if ($images && is_array($images)) {
        $fixedImages = [];
        foreach ($images as $image) {
            $imageName = is_array($image) ? $image['image_name'] : $image;
            $imagePath = 'product/' . $imageName;
            
            if (!Storage::disk('public')->exists($imagePath)) {
                echo "✗ Missing image for product {$product->code}: $imageName\n";
                $fixedImages[] = [
                    'image_name' => $defaultImage,
                    'storage' => 'public'
                ];
                $needsUpdate = true;
            } else {
                $fixedImages[] = is_array($image) ? $image : [
                    'image_name' => $imageName,
                    'storage' => 'public'
                ];
            }
        }
        if ($needsUpdate) {
            $updateData['images'] = json_encode($fixedImages);
        }
    } else {
        // No images set, add default
        $updateData['images'] = json_encode([
            ['image_name' => $defaultImage, 'storage' => 'public'],
            ['image_name' => $defaultImage, 'storage' => 'public'],
        ]);
        $needsUpdate = true;
    }
    
    // Check meta image
    $metaImage = $product->meta_image;
    if ($metaImage) {
        $metaPath = 'product/meta/' . $metaImage;
        if (!Storage::disk('public')->exists($metaPath)) {
            $updateData['meta_image'] = $defaultImage;
            $needsUpdate = true;
        }
    } else {
        $updateData['meta_image'] = $defaultImage;
        $needsUpdate = true;
    }
    
    if ($needsUpdate) {
        $product->update($updateData);
        $fixed++;
        echo "✓ Fixed images for product: {$product->code} - {$product->name}\n";
    } else {
        echo "✓ Product {$product->code} images are OK\n";
    }
}

echo "\n=== Summary ===\n";
echo "Fixed: $fixed products\n";
echo "Total checked: " . $products->count() . " products\n";

// Check storage link
$storageLink = public_path('storage');
if (!is_link($storageLink) && !is_dir($storageLink)) {
    echo "\n⚠ Storage link not found. Creating...\n";
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        echo "✓ Storage link created\n";
    } catch (\Exception $e) {
        echo "✗ Failed to create storage link: " . $e->getMessage() . "\n";
        echo "Run manually: php artisan storage:link\n";
    }
} else {
    echo "\n✓ Storage link exists\n";
}

echo "\nDone!\n";
