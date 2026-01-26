<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HeroBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates hero section banners with images from product storage
     *
     * @return void
     */
    public function run()
    {
        echo "Creating hero section banners...\n\n";

        // Get available images from product storage
        $availableImages = $this->getAvailableImages();
        
        if (empty($availableImages)) {
            echo "⚠ No images found in storage. Please add banner images first.\n";
            return;
        }

        echo "Found " . count($availableImages) . " images available\n\n";

        // Create 3-5 hero banners for the slider
        $bannerCount = min(5, count($availableImages));
        
        $banners = [
            [
                'title_en' => 'Premium Ceramic Tiles Collection',
                'title_ar' => 'مجموعة بلاط السيراميك الممتاز',
                'sub_title_en' => 'Discover Our Latest Designs',
                'sub_title_ar' => 'اكتشف أحدث تصاميمنا',
                'button_text_en' => 'Shop Now',
                'button_text_ar' => 'تسوق الآن',
                'url' => route('products', ['data_from' => 'latest', 'page' => 1]),
            ],
            [
                'title_en' => 'Luxury Porcelain Tiles',
                'title_ar' => 'بلاط البورسلين الفاخر',
                'sub_title_en' => 'Elegant Designs for Modern Homes',
                'sub_title_ar' => 'تصاميم أنيقة للمنازل العصرية',
                'button_text_en' => 'Explore Collection',
                'button_text_ar' => 'استكشف المجموعة',
                'url' => '/products?data_from=featured&page=1',
            ],
            [
                'title_en' => 'Special Offers',
                'title_ar' => 'عروض خاصة',
                'sub_title_en' => 'Up to 30% Off on Selected Items',
                'sub_title_ar' => 'خصم يصل إلى 30% على المنتجات المختارة',
                'button_text_en' => 'View Offers',
                'button_text_ar' => 'عرض العروض',
                'url' => '/products?data_from=discounted&page=1',
            ],
            [
                'title_en' => 'New Arrivals',
                'title_ar' => 'وصل حديثاً',
                'sub_title_en' => 'Latest Ceramic Tile Designs',
                'sub_title_ar' => 'أحدث تصاميم بلاط السيراميك',
                'button_text_en' => 'Shop New',
                'button_text_ar' => 'تسوق الجديد',
                'url' => route('products', ['data_from' => 'latest', 'page' => 1]),
            ],
            [
                'title_en' => 'Premium Quality Guaranteed',
                'title_ar' => 'جودة ممتازة مضمونة',
                'sub_title_en' => 'Best Ceramic Tiles in Saudi Arabia',
                'sub_title_ar' => 'أفضل بلاط السيراميك في المملكة العربية السعودية',
                'button_text_en' => 'Browse All',
                'button_text_ar' => 'تصفح الكل',
                'url' => '/products?page=1',
            ],
        ];

        $created = 0;
        $imageIndex = 0;

        foreach ($banners as $index => $bannerData) {
            if ($index >= $bannerCount) break;

            // Get image
            $imageName = $availableImages[$imageIndex % count($availableImages)];
            $imageIndex++;

            // Copy image to banner directory
            $bannerImagePath = 'banner/' . $imageName;
            if (!Storage::disk('public')->exists($bannerImagePath)) {
                if (Storage::disk('public')->exists('product/' . $imageName)) {
                    Storage::disk('public')->copy('product/' . $imageName, $bannerImagePath);
                } else {
                    echo "⚠ Image not found: {$imageName}, skipping...\n";
                    continue;
                }
            }

            try {
                $banner = Banner::create([
                    'photo' => $imageName,
                    'banner_type' => 'Main Banner',
                    'theme' => 'default',
                    'published' => 1,
                    'url' => $bannerData['url'],
                    'resource_type' => null,
                    'resource_id' => null,
                    'title' => $bannerData['title_en'],
                    'sub_title' => $bannerData['sub_title_en'],
                    'button_text' => $bannerData['button_text_en'],
                    'background_color' => null,
                ]);

                // Store storage type in storages table
                DB::table('storages')->updateOrInsert([
                    'data_type' => 'App\Models\Banner',
                    'data_id' => $banner->id,
                    'key' => 'photo',
                ], [
                    'value' => 'public',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $created++;
                echo "✓ Created banner: {$bannerData['title_en']}\n";
                
            } catch (\Exception $e) {
                echo "✗ Error creating banner: " . $e->getMessage() . "\n";
            }
        }

        echo "\n✅ Created {$created} hero banners\n";
        echo "   Banner type: Main Banner\n";
        echo "   Status: Published\n\n";
    }

    /**
     * Get available images from product storage
     */
    private function getAvailableImages(): array
    {
        $imagePath = storage_path('app/public/product');
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
