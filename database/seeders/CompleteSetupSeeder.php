<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\OfflinePaymentMethod;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CompleteSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Complete setup: Logo, Categories, Products, Payments, Banners
     *
     * @return void
     */
    public function run()
    {
        echo "=== COMPLETE SETUP SEEDER ===\n\n";
        
        // 1. Add Logo
        $this->addLogo();
        
        // 2. Fix Category Structure
        $this->fixCategoryStructure();
        
        // 3. Reassign Products to Correct Categories
        $this->reassignProductsToCategories();
        
        // 4. Add Offline Payment
        $this->addOfflinePayment();
        
        // 5. Enable Test Payment Gateway
        $this->enableTestPaymentGateway();
        
        // 6. Add Cover Banners
        $this->addCoverBanners();
        
        // 7. Final Verification
        $this->finalVerification();
        
        echo "\n✅ Complete setup finished!\n";
    }

    private function addLogo()
    {
        echo "1. Adding logo...\n";
        
        // Get available images
        $availableImages = $this->getAvailableImages();
        if (empty($availableImages)) {
            echo "  ⚠ No images found for logo\n";
            return;
        }
        
        // Use first available image as logo (or create a default)
        $logoImage = $availableImages[0];
        
        // Copy to company directory if needed
        $logoPath = 'company/' . $logoImage;
        if (!Storage::disk('public')->exists($logoPath)) {
            if (Storage::disk('public')->exists('product/' . $logoImage)) {
                Storage::disk('public')->copy('product/' . $logoImage, $logoPath);
            }
        }
        
        // Set logo in BusinessSettings
        $logoData = [
            'image_name' => $logoImage,
            'storage' => 'public'
        ];
        
        BusinessSetting::updateOrInsert(
            ['type' => 'company_web_logo'],
            ['value' => json_encode($logoData), 'updated_at' => now(), 'created_at' => now()]
        );
        
        BusinessSetting::updateOrInsert(
            ['type' => 'company_mobile_logo'],
            ['value' => json_encode($logoData), 'updated_at' => now(), 'created_at' => now()]
        );
        
        BusinessSetting::updateOrInsert(
            ['type' => 'company_footer_logo'],
            ['value' => json_encode($logoData), 'updated_at' => now(), 'created_at' => now()]
        );
        
        echo "  ✓ Logo added: {$logoImage}\n\n";
    }

    private function fixCategoryStructure()
    {
        echo "2. Fixing category structure...\n";
        
        // Get all categories
        $allCategories = Category::all();
        $mainCategories = Category::where('parent_id', 0)->get();
        $subCategories = Category::where('parent_id', '>', 0)->get();
        
        echo "  Found: {$mainCategories->count()} main, {$subCategories->count()} sub categories\n";
        
        // Ensure all sub-categories have valid parent
        $fixed = 0;
        foreach ($subCategories as $subCat) {
            $parent = Category::find($subCat->parent_id);
            if (!$parent || $parent->parent_id != 0) {
                // Assign to first main category if parent is invalid
                $mainCat = $mainCategories->first();
                if ($mainCat) {
                    $subCat->update(['parent_id' => $mainCat->id]);
                    $fixed++;
                }
            }
        }
        
        // Ensure we have proper main categories
        if ($mainCategories->count() == 0) {
            echo "  ⚠ No main categories found, creating default...\n";
            $this->createDefaultMainCategories();
        }
        
        echo "  ✓ Fixed {$fixed} sub-categories\n\n";
    }

    private function createDefaultMainCategories()
    {
        $mainCategories = [
            ['en' => 'Floor Tiles', 'ar' => 'بلاط الأرضيات', 'slug' => 'floor-tiles'],
            ['en' => 'Wall Tiles', 'ar' => 'بلاط الجدران', 'slug' => 'wall-tiles'],
            ['en' => 'Bathroom Tiles', 'ar' => 'بلاط الحمامات', 'slug' => 'bathroom-tiles'],
            ['en' => 'Kitchen Tiles', 'ar' => 'بلاط المطابخ', 'slug' => 'kitchen-tiles'],
            ['en' => 'Porcelain Tiles', 'ar' => 'بلاط البورسلين', 'slug' => 'porcelain-tiles'],
        ];
        
        foreach ($mainCategories as $index => $cat) {
            $category = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['en'],
                    'slug' => $cat['slug'],
                    'icon' => 'def.png',
                    'icon_storage_type' => 'public',
                    'parent_id' => 0,
                    'position' => $index + 1,
                    'home_status' => 1,
                    'priority' => $index + 1,
                ]
            );
        }
    }

    private function reassignProductsToCategories()
    {
        echo "3. Reassigning products to correct categories...\n";
        
        $products = Product::all();
        $mainCategories = Category::where('parent_id', 0)->pluck('id')->toArray();
        $subCategories = Category::where('parent_id', '>', 0)->pluck('id')->toArray();
        
        if (empty($mainCategories)) {
            echo "  ⚠ No categories available\n\n";
            return;
        }
        
        $reassigned = 0;
        foreach ($products as $product) {
            // Ensure product has valid category
            if (!$product->category_id || !Category::find($product->category_id)) {
                $categoryId = $mainCategories[array_rand($mainCategories)];
                $subCategoryId = !empty($subCategories) ? $subCategories[array_rand($subCategories)] : null;
                
                $product->update([
                    'category_id' => $categoryId,
                    'category_ids' => (string)$categoryId,
                    'sub_category_id' => $subCategoryId,
                ]);
                $reassigned++;
            }
        }
        
        echo "  ✓ Reassigned {$reassigned} products\n\n";
    }

    private function addOfflinePayment()
    {
        echo "4. Adding offline payment method...\n";
        
        // Check if offline payment method exists
        $existing = OfflinePaymentMethod::where('method_name', 'Bank Transfer')->first();
        
        if (!$existing) {
            OfflinePaymentMethod::create([
                'method_name' => 'Bank Transfer',
                'method_fields' => json_encode([
                    ['input_type' => 'text', 'input_name' => 'bank_name', 'label' => 'Bank Name', 'placeholder' => 'Enter bank name'],
                    ['input_type' => 'text', 'input_name' => 'account_number', 'label' => 'Account Number', 'placeholder' => 'Enter account number'],
                    ['input_type' => 'text', 'input_name' => 'account_holder', 'label' => 'Account Holder Name', 'placeholder' => 'Enter account holder name'],
                ]),
                'method_informations' => json_encode([
                    'instruction' => 'Please transfer the payment to the following bank account. Your order will be processed after payment confirmation.',
                    'bank_name' => 'Al Rajhi Bank',
                    'account_number' => '1234567890',
                    'account_holder' => 'Premium Ceramics Store',
                ]),
                'status' => 1, // Active
            ]);
            echo "  ✓ Created Bank Transfer offline payment method\n";
        } else {
            $existing->update(['status' => 1]);
            echo "  ✓ Activated existing offline payment method\n";
        }
        
        // Enable offline payment in BusinessSettings
        BusinessSetting::updateOrInsert(
            ['type' => 'offline_payment'],
            ['value' => json_encode(['status' => 1]), 'updated_at' => now(), 'created_at' => now()]
        );
        
        // Enable digital payment (required for offline)
        BusinessSetting::updateOrInsert(
            ['type' => 'digital_payment'],
            ['value' => json_encode(['status' => 1]), 'updated_at' => now(), 'created_at' => now()]
        );
        
        echo "  ✓ Offline payment enabled\n\n";
    }

    private function enableTestPaymentGateway()
    {
        echo "5. Enabling test payment gateway...\n";
        
        // Enable Stripe test mode (most common test gateway)
        $stripeTest = Setting::where('key_name', 'stripe')->where('settings_type', 'payment_config')->first();
        
        $testValues = [
            'status' => 1,
            'api_key' => 'pk_test_51Q...',
            'published_key' => 'pk_test_51Q...',
        ];
        
        if ($stripeTest) {
            $stripeTest->update([
                'test_values' => $testValues,
                'live_values' => $testValues,
                'mode' => 'test',
                'is_active' => 1,
            ]);
            echo "  ✓ Stripe test mode enabled\n";
        } else {
            // Create Stripe test configuration
            Setting::create([
                'key_name' => 'stripe',
                'settings_type' => 'payment_config',
                'test_values' => $testValues,
                'live_values' => $testValues,
                'mode' => 'test',
                'is_active' => 1,
                'additional_data' => json_encode([
                    'gateway_title' => 'Stripe',
                    'gateway_image' => null,
                ]),
            ]);
            echo "  ✓ Created Stripe test configuration\n";
        }
        
        echo "  ✓ Test payment gateway enabled\n\n";
    }

    private function addCoverBanners()
    {
        echo "6. Adding cover banners...\n";
        
        $availableImages = $this->getAvailableImages();
        if (empty($availableImages)) {
            echo "  ⚠ No images found for banners\n\n";
            return;
        }
        
        $theme = theme_root_path() ?? 'default';
        $bannerTypes = ['Main Banner', 'Footer Banner', 'Main Section Banner'];
        
        $created = 0;
        $imageIndex = 0;
        
        foreach ($bannerTypes as $bannerType) {
            $existing = Banner::where('banner_type', $bannerType)
                ->where('theme', $theme)
                ->count();
            
            if ($existing < 2) {
                $needed = 2 - $existing;
                for ($i = 0; $i < $needed; $i++) {
                    $imageName = $availableImages[$imageIndex % count($availableImages)];
                    $imageIndex++;
                    
                    // Copy image
                    $bannerPath = 'banner/' . $imageName;
                    if (!Storage::disk('public')->exists($bannerPath)) {
                        if (Storage::disk('public')->exists('product/' . $imageName)) {
                            Storage::disk('public')->copy('product/' . $imageName, $bannerPath);
                        }
                    }
                    
                    $banner = Banner::create([
                        'photo' => $imageName,
                        'banner_type' => $bannerType,
                        'theme' => $theme,
                        'published' => 1,
                        'url' => '/products?page=1',
                        'title' => 'Cover Banner',
                        'sub_title' => 'Special Offer',
                        'button_text' => 'Shop Now',
                    ]);
                    
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
                }
            }
        }
        
        echo "  ✓ Created {$created} cover banners\n\n";
    }

    private function finalVerification()
    {
        echo "7. Final verification...\n";
        
        $logo = BusinessSetting::where('type', 'company_web_logo')->first();
        echo "  " . ($logo ? "✓" : "✗") . " Logo: " . ($logo ? "Set" : "Missing") . "\n";
        
        $mainCats = Category::where('parent_id', 0)->count();
        $subCats = Category::where('parent_id', '>', 0)->count();
        echo "  ✓ Categories: {$mainCats} main, {$subCats} sub\n";
        
        $products = Product::count();
        $productsWithImages = Product::where('thumbnail', '!=', 'def.webp')->where('thumbnail', '!=', 'def.png')->count();
        echo "  ✓ Products: {$products} total, {$productsWithImages} with images\n";
        
        $brands = Brand::count();
        echo "  ✓ Brands: {$brands}\n";
        
        $banners = Banner::count();
        echo "  ✓ Banners: {$banners}\n";
        
        $offlinePayment = OfflinePaymentMethod::where('status', 1)->count();
        echo "  " . ($offlinePayment > 0 ? "✓" : "✗") . " Offline Payment: " . ($offlinePayment > 0 ? "Enabled" : "Disabled") . "\n";
        
        $testGateway = Setting::where('settings_type', 'payment_config')->where('is_active', 1)->count();
        echo "  " . ($testGateway > 0 ? "✓" : "✗") . " Test Gateway: " . ($testGateway > 0 ? "Enabled" : "Disabled") . "\n";
        
        echo "\n";
    }

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
