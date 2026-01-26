<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignImagesToProducts extends Seeder
{
    /**
     * Assign existing images to products
     *
     * @return void
     */
    public function run()
    {
        echo "Assigning existing images to products...\n\n";
        
        // Get all products
        $products = Product::all();
        
        if ($products->isEmpty()) {
            echo "⚠ No products found in database.\n";
            echo "Please run the product seeder first.\n";
            return;
        }
        
        // Get all images from storage
        $imagePath = storage_path('app/public/product');
        $allImages = [];
        
        if (is_dir($imagePath)) {
            $files = scandir($imagePath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imagePath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png'])) {
                        $allImages[] = $file;
                    }
                }
            }
        }
        
        echo "Found " . count($allImages) . " images in storage\n";
        echo "Found " . $products->count() . " products in database\n\n";
        
        if (empty($allImages)) {
            echo "⚠ No images found in storage/app/public/product/\n";
            return;
        }
        
        $assigned = 0;
        $skipped = 0;
        $usedImages = []; // Track used images to avoid duplicates
        
        foreach ($products as $index => $product) {
            $codeLower = strtolower($product->code);
            $expectedImageName = $codeLower . '.webp';
            $expectedThumbName = $codeLower . '-thumb.webp';
            
            // Try to find image by product code
            $foundImage = null;
            $foundThumb = null;
            
            // Method 1: Exact match by product code
            foreach ($allImages as $image) {
                if (in_array($image, $usedImages)) continue; // Skip already used images
                
                $imageLower = strtolower($image);
                $imageBase = pathinfo($imageLower, PATHINFO_FILENAME);
                
                // Check for exact code match (with or without dashes)
                $codeNoDash = str_replace('-', '', $codeLower);
                if ($imageBase == $codeLower || $imageBase == $codeNoDash) {
                    $foundImage = $image;
                    break;
                }
                
                // Check for thumb
                if ($imageBase == $codeLower . '-thumb' || $imageBase == $codeNoDash . 'thumb') {
                    $foundThumb = $image;
                }
            }
            
            // Method 2: If not found, use next available image (skip def.webp and already used)
            if (!$foundImage && !empty($allImages)) {
                foreach ($allImages as $image) {
                    if ($image == 'def.webp' || in_array($image, $usedImages)) continue;
                    $foundImage = $image;
                    break;
                }
            }
            
            // Method 3: If still nothing, use def.webp as last resort
            if (!$foundImage) {
                $foundImage = 'def.webp';
            }
            
            // If we found an image, update the product
            if ($foundImage) {
                // Check if image exists in storage
                if (Storage::disk('public')->exists('product/' . $foundImage)) {
                    // Use found image
                    $imageName = $foundImage;
                    
                    // Create thumbnail path
                    $thumbName = $foundThumb;
                    if (!$thumbName) {
                        // Create thumbnail from main image
                        $thumbPath = storage_path('app/public/product/thumbnail/' . $codeLower . '-thumb.webp');
                        $imagePath = storage_path('app/public/product/' . $imageName);
                        
                        // Ensure thumbnail directory exists
                        $thumbDir = dirname($thumbPath);
                        if (!is_dir($thumbDir)) {
                            mkdir($thumbDir, 0755, true);
                        }
                        
                        // Copy image as thumbnail (or resize if needed)
                        if (file_exists($imagePath)) {
                            copy($imagePath, $thumbPath);
                            $thumbName = $codeLower . '-thumb.webp';
                        } else {
                            $thumbName = $imageName; // Fallback
                        }
                    }
                    
                    // Update product images
                    $images = json_encode([
                        ['image_name' => $imageName, 'storage' => 'public'],
                        ['image_name' => $imageName, 'storage' => 'public'],
                    ]);
                    
                    $product->update([
                        'images' => $images,
                        'thumbnail' => $thumbName,
                        'meta_image' => $thumbName,
                    ]);
                    
                    // Mark image as used
                    if ($foundImage != 'def.webp') {
                        $usedImages[] = $foundImage;
                    }
                    
                    $assigned++;
                    echo "✓ Assigned image '{$imageName}' to product {$product->code} - {$product->name}\n";
                } else {
                    $skipped++;
                    echo "⊘ Image '{$foundImage}' not found in storage for product {$product->code}\n";
                }
            } else {
                $skipped++;
                echo "⊘ No matching image found for product {$product->code}\n";
            }
        }
        
        echo "\n=== Summary ===\n";
        echo "Assigned: $assigned products\n";
        echo "Skipped: $skipped products\n";
        echo "\n✅ Done! Products updated with existing images.\n";
    }
}
