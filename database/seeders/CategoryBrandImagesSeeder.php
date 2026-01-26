<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategoryBrandImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assigns images to categories and brands
     *
     * @return void
     */
    public function run()
    {
        echo "Assigning images to categories and brands...\n\n";

        // Assign images to categories
        $this->assignCategoryImages();
        
        // Assign images to brands
        $this->assignBrandImages();
        
        echo "\n✅ Category and brand images assigned successfully!\n";
    }

    private function assignCategoryImages()
    {
        echo "Assigning category images...\n";
        
        $categories = Category::all();
        $defaultImage = 'def.png';
        
        // Get available product images that can be used for categories
        $availableImages = $this->getAvailableImages('product');
        
        $updated = 0;
        foreach ($categories as $index => $category) {
            // Try to use an image from available images, or use default
            $imageName = $defaultImage;
            
            if (!empty($availableImages) && isset($availableImages[$index % count($availableImages)])) {
                // Use a product image as category icon (you can replace with actual category images later)
                $imageName = $availableImages[$index % count($availableImages)];
            }
            
            // Ensure the image exists, otherwise use default
            if ($imageName !== $defaultImage && !Storage::disk('public')->exists('product/' . $imageName)) {
                $imageName = $defaultImage;
            }
            
            // Copy image to category directory if it's not default
            if ($imageName !== $defaultImage && Storage::disk('public')->exists('product/' . $imageName)) {
                $categoryImagePath = 'category/' . $imageName;
                if (!Storage::disk('public')->exists($categoryImagePath)) {
                    Storage::disk('public')->copy('product/' . $imageName, $categoryImagePath);
                }
                $category->update([
                    'icon' => $imageName,
                    'icon_storage_type' => 'public',
                ]);
            } else {
                // Use default image
                $category->update([
                    'icon' => $defaultImage,
                    'icon_storage_type' => 'public',
                ]);
            }
            
            $updated++;
        }
        
        echo "✓ Updated $updated categories with images\n";
    }

    private function assignBrandImages()
    {
        echo "Assigning brand images...\n";
        
        $brands = Brand::all();
        $defaultImage = 'def.png';
        
        // Get available product images
        $availableImages = $this->getAvailableImages('product');
        
        $updated = 0;
        foreach ($brands as $index => $brand) {
            // Try to use an image from available images, or use default
            $imageName = $defaultImage;
            
            if (!empty($availableImages) && isset($availableImages[$index % count($availableImages)])) {
                $imageName = $availableImages[$index % count($availableImages)];
            }
            
            // Ensure the image exists, otherwise use default
            if ($imageName !== $defaultImage && !Storage::disk('public')->exists('product/' . $imageName)) {
                $imageName = $defaultImage;
            }
            
            // Copy image to brand directory if it's not default
            if ($imageName !== $defaultImage && Storage::disk('public')->exists('product/' . $imageName)) {
                $brandImagePath = 'brand/' . $imageName;
                if (!Storage::disk('public')->exists($brandImagePath)) {
                    Storage::disk('public')->copy('product/' . $imageName, $brandImagePath);
                }
                $brand->update([
                    'image' => $imageName,
                    'image_storage_type' => 'public',
                    'image_alt_text' => $brand->name ?? 'Brand Image',
                ]);
            } else {
                // Use default image
                $brand->update([
                    'image' => $defaultImage,
                    'image_storage_type' => 'public',
                    'image_alt_text' => $brand->name ?? 'Brand Image',
                ]);
            }
            
            $updated++;
        }
        
        echo "✓ Updated $updated brands with images\n";
    }

    /**
     * Get available images from storage
     */
    private function getAvailableImages(string $directory): array
    {
        $imagePath = storage_path("app/public/{$directory}");
        $images = [];
        
        if (is_dir($imagePath)) {
            $files = scandir($imagePath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imagePath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png']) && $file != 'def.webp' && $file != 'def.png') {
                        $images[] = $file;
                    }
                }
            }
        }
        
        return $images;
    }
}
