<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AllBannersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all banner types with appropriate images
     *
     * @return void
     */
    public function run()
    {
        echo "Creating all banner types...\n\n";

        // Get available images from product storage
        $availableImages = $this->getAvailableImages();
        
        if (empty($availableImages)) {
            echo "⚠ No images found in storage. Please add banner images first.\n";
            return;
        }

        echo "Found " . count($availableImages) . " images available\n\n";

        $theme = theme_root_path() ?? 'default';
        
        // Define all banner types based on theme
        $bannerTypes = $this->getBannerTypesForTheme($theme);
        
        $imageIndex = 0;
        $totalCreated = 0;

        foreach ($bannerTypes as $bannerType => $bannerConfig) {
            $count = $bannerConfig['count'] ?? 1;
            $banners = $bannerConfig['banners'] ?? [];
            
            echo "Creating {$bannerType} banners...\n";
            
            // Check if banners of this type already exist
            $existingCount = Banner::where('banner_type', $bannerType)
                ->where('theme', $theme)
                ->count();
            
            if ($existingCount >= $count) {
                echo "  ⊘ {$bannerType}: Already has {$existingCount} banners (skipping)\n";
                continue;
            }
            
            $needed = $count - $existingCount;
            
            for ($i = 0; $i < $needed; $i++) {
                // Get banner data (use provided or generate default)
                $bannerData = isset($banners[$i]) ? $banners[$i] : $this->getDefaultBannerData($bannerType, $i);
                
                // Get image
                $imageName = $availableImages[$imageIndex % count($availableImages)];
                $imageIndex++;

                // Copy image to banner directory
                $bannerImagePath = 'banner/' . $imageName;
                if (!Storage::disk('public')->exists($bannerImagePath)) {
                    if (Storage::disk('public')->exists('product/' . $imageName)) {
                        Storage::disk('public')->copy('product/' . $imageName, $bannerImagePath);
                    } else {
                        echo "  ⚠ Image not found: {$imageName}, skipping...\n";
                        continue;
                    }
                }

                try {
                    $banner = Banner::create([
                        'photo' => $imageName,
                        'banner_type' => $bannerType,
                        'theme' => $theme,
                        'published' => 1,
                        'url' => $bannerData['url'],
                        'resource_type' => null,
                        'resource_id' => null,
                        'title' => $bannerData['title'],
                        'sub_title' => $bannerData['sub_title'],
                        'button_text' => $bannerData['button_text'],
                        'background_color' => $bannerData['background_color'] ?? null,
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

                    $totalCreated++;
                    echo "  ✓ Created: {$bannerType} - {$bannerData['title']}\n";
                    
                } catch (\Exception $e) {
                    echo "  ✗ Error creating banner: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "\n✅ Created {$totalCreated} banners\n";
        echo "   Theme: {$theme}\n\n";
    }

    private function getBannerTypesForTheme($theme): array
    {
        $banners = [
            'default' => [
                'Main Banner' => [
                    'count' => 5,
                    'banners' => [
                        [
                            'title' => 'Premium Ceramic Tiles Collection',
                            'sub_title' => 'Discover Our Latest Designs',
                            'button_text' => 'Shop Now',
                            'url' => '/products?data_from=latest&page=1',
                        ],
                        [
                            'title' => 'Luxury Porcelain Tiles',
                            'sub_title' => 'Elegant Designs for Modern Homes',
                            'button_text' => 'Explore Collection',
                            'url' => '/products?data_from=featured&page=1',
                        ],
                        [
                            'title' => 'Special Offers',
                            'sub_title' => 'Up to 30% Off on Selected Items',
                            'button_text' => 'View Offers',
                            'url' => '/products?data_from=discounted&page=1',
                        ],
                    ],
                ],
                'Footer Banner' => [
                    'count' => 2,
                    'banners' => [
                        [
                            'title' => 'Free Shipping',
                            'sub_title' => 'On orders over 500 SAR',
                            'button_text' => 'Shop Now',
                            'url' => '/products?page=1',
                        ],
                        [
                            'title' => 'New Collection',
                            'sub_title' => 'Latest Ceramic Tile Designs',
                            'button_text' => 'Explore',
                            'url' => '/products?data_from=latest&page=1',
                        ],
                    ],
                ],
                'Main Section Banner' => [
                    'count' => 1,
                    'banners' => [
                        [
                            'title' => 'Premium Quality Guaranteed',
                            'sub_title' => 'Best Ceramic Tiles in Saudi Arabia',
                            'button_text' => 'Browse All',
                            'url' => '/products?page=1',
                        ],
                    ],
                ],
                'Popup Banner' => [
                    'count' => 1,
                    'banners' => [
                        [
                            'title' => 'Welcome Offer',
                            'sub_title' => 'Get 15% off on your first order',
                            'button_text' => 'Shop Now',
                            'url' => '/products?page=1',
                        ],
                    ],
                ],
            ],
            'theme_aster' => [
                'Main Banner' => [
                    'count' => 5,
                ],
                'Footer Banner' => [
                    'count' => 2,
                ],
                'Main Section Banner' => [
                    'count' => 1,
                ],
                'Sidebar Banner' => [
                    'count' => 1,
                ],
                'Top Side Banner' => [
                    'count' => 1,
                ],
                'Header Banner' => [
                    'count' => 1,
                ],
                'Popup Banner' => [
                    'count' => 1,
                ],
            ],
            'theme_fashion' => [
                'Main Banner' => [
                    'count' => 5,
                ],
                'Promo Banner Left' => [
                    'count' => 1,
                ],
                'Promo Banner Middle Top' => [
                    'count' => 1,
                ],
                'Promo Banner Middle Bottom' => [
                    'count' => 1,
                ],
                'Promo Banner Right' => [
                    'count' => 1,
                ],
                'Promo Banner Bottom' => [
                    'count' => 1,
                ],
                'Popup Banner' => [
                    'count' => 1,
                ],
            ],
        ];

        return $banners[$theme] ?? $banners['default'];
    }

    private function getDefaultBannerData(string $bannerType, int $index): array
    {
        $defaults = [
            'Main Banner' => [
                'title' => 'Premium Ceramic Tiles',
                'sub_title' => 'Quality You Can Trust',
                'button_text' => 'Shop Now',
                'url' => '/products?page=1',
            ],
            'Footer Banner' => [
                'title' => 'Special Offer',
                'sub_title' => 'Limited Time Deal',
                'button_text' => 'View',
                'url' => '/products?data_from=discounted&page=1',
            ],
            'Main Section Banner' => [
                'title' => 'Featured Collection',
                'sub_title' => 'Best Sellers',
                'button_text' => 'Explore',
                'url' => '/products?data_from=featured&page=1',
            ],
            'Sidebar Banner' => [
                'title' => 'New Arrivals',
                'sub_title' => 'Latest Products',
                'button_text' => 'Shop',
                'url' => '/products?data_from=latest&page=1',
            ],
            'Top Side Banner' => [
                'title' => 'Hot Deals',
                'sub_title' => 'Don\'t Miss Out',
                'button_text' => 'Shop Now',
                'url' => '/products?data_from=discounted&page=1',
            ],
            'Header Banner' => [
                'title' => 'Welcome',
                'sub_title' => 'Premium Ceramics',
                'button_text' => 'Shop',
                'url' => '/products?page=1',
            ],
            'Popup Banner' => [
                'title' => 'Special Offer',
                'sub_title' => 'Get Discount Now',
                'button_text' => 'Shop Now',
                'url' => '/products?page=1',
            ],
            'Promo Banner Left' => [
                'title' => 'Left Promo',
                'sub_title' => 'Special Deal',
                'button_text' => 'View',
                'url' => '/products?page=1',
            ],
            'Promo Banner Middle Top' => [
                'title' => 'Top Promo',
                'sub_title' => 'Featured',
                'button_text' => 'Shop',
                'url' => '/products?data_from=featured&page=1',
            ],
            'Promo Banner Middle Bottom' => [
                'title' => 'Bottom Promo',
                'sub_title' => 'New Items',
                'button_text' => 'View',
                'url' => '/products?data_from=latest&page=1',
            ],
            'Promo Banner Right' => [
                'title' => 'Right Promo',
                'sub_title' => 'Best Deals',
                'button_text' => 'Shop',
                'url' => '/products?data_from=discounted&page=1',
            ],
            'Promo Banner Bottom' => [
                'title' => 'Bottom Banner',
                'sub_title' => 'All Products',
                'button_text' => 'Browse',
                'url' => '/products?page=1',
            ],
        ];

        return $defaults[$bannerType] ?? [
            'title' => 'Banner',
            'sub_title' => 'Special Offer',
            'button_text' => 'Shop Now',
            'url' => '/products?page=1',
        ];
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
